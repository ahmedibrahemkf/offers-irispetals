<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeFinancial;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Support\SystemLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinanceController extends BaseAdminController
{
    public function expenses(Request $request): View
    {
        return view('admin.finance.expenses', $this->sharedData($request) + [
            'expenses' => Expense::query()->with('category')->orderByDesc('id')->paginate(20),
            'categories' => ExpenseCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'expense_category_id' => 'nullable|integer|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'nullable|date',
            'note' => 'nullable|string|max:1500',
        ]);

        $user = $this->user($request);

        Expense::query()->create([
            'title' => $validated['title'],
            'expense_category_id' => $validated['expense_category_id'] ?? null,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'] ?? now()->toDateString(),
            'note' => $validated['note'] ?? null,
            'created_by' => $user->id,
        ]);

        SystemLogger::log((int) $user->id, 'expense_created', 'Added new expense', 'Expense', null, $request);

        return back()->with('status', 'تمت إضافة المصروف');
    }

    public function employees(Request $request): View
    {
        $payrollMonth = $request->string('month')->toString();
        if ($payrollMonth === '') {
            $payrollMonth = now()->format('Y-m');
        }

        return view('admin.finance.employees', $this->sharedData($request) + [
            'employees' => User::query()->orderByDesc('id')->paginate(20),
            'employeesForSelect' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'role', 'base_salary']),
            'financials' => EmployeeFinancial::query()->with('employee')->orderByDesc('id')->limit(50)->get(),
            'payrollMonth' => $payrollMonth,
        ]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'username' => 'required|string|max:60|unique:users,username',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'role' => 'required|in:owner,manager,staff,craftsman,viewer',
            'base_salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'password' => 'required|string|min:8|max:100',
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => true,
            'base_salary' => (float) ($validated['base_salary'] ?? 0),
            'hire_date' => $validated['hire_date'] ?? null,
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'تمت إضافة الموظف');
    }

    public function storeEmployeeFinancial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:salary,advance,deduction,bonus',
            'amount' => 'required|numeric|min:0.01',
            'effective_date' => 'nullable|date',
            'note' => 'nullable|string|max:1000',
        ]);

        $user = $this->user($request);
        EmployeeFinancial::query()->create([
            'employee_id' => $validated['employee_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'effective_date' => $validated['effective_date'] ?? now()->toDateString(),
            'note' => $validated['note'] ?? null,
            'created_by' => $user->id,
        ]);

        SystemLogger::log((int) $user->id, 'salary_activity_created', 'Added employee financial entry', 'EmployeeFinancial', null, $request);

        return back()->with('status', 'تم تسجيل الحركة المالية');
    }

    public function showEmployee(Request $request, User $employee): View
    {
        $financials = EmployeeFinancial::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->paginate(30);

        $summary = [
            'salary' => (float) EmployeeFinancial::query()->where('employee_id', $employee->id)->where('type', 'salary')->sum('amount'),
            'advance' => (float) EmployeeFinancial::query()->where('employee_id', $employee->id)->where('type', 'advance')->sum('amount'),
            'deduction' => (float) EmployeeFinancial::query()->where('employee_id', $employee->id)->where('type', 'deduction')->sum('amount'),
            'bonus' => (float) EmployeeFinancial::query()->where('employee_id', $employee->id)->where('type', 'bonus')->sum('amount'),
        ];
        $summary['net'] = $summary['salary'] + $summary['bonus'] - $summary['advance'] - $summary['deduction'];

        return view('admin.finance.employee-show', $this->sharedData($request) + [
            'employee' => $employee,
            'financials' => $financials,
            'summary' => $summary,
        ]);
    }

    public function storeMonthlyPayroll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|string|regex:/^\d{4}-(0[1-9]|1[0-2])$/',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'integer|exists:users,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $user = $this->user($request);
        $month = $validated['month'];
        $periodStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $periodEnd = (clone $periodStart)->endOfMonth();
        $employeesQuery = User::query()
            ->where('is_active', true)
            ->whereIn('role', ['manager', 'staff', 'craftsman']);

        if (! empty($validated['employee_ids'])) {
            $employeesQuery->whereIn('id', $validated['employee_ids']);
        }

        $employees = $employeesQuery->get();
        $note = trim((string) ($validated['note'] ?? ''));
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($employees, $month, $periodStart, $periodEnd, $note, $user, &$created, &$skipped): void {
            foreach ($employees as $employee) {
                if ((float) $employee->base_salary <= 0) {
                    $skipped++;
                    continue;
                }

                $exists = EmployeeFinancial::query()
                    ->where('employee_id', $employee->id)
                    ->where('type', 'salary')
                    ->whereBetween('effective_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $autoNote = 'راتب شهر '.$month;
                if ($note !== '') {
                    $autoNote .= ' - '.$note;
                }

                EmployeeFinancial::query()->create([
                    'employee_id' => $employee->id,
                    'type' => 'salary',
                    'amount' => (float) $employee->base_salary,
                    'effective_date' => $periodStart->toDateString(),
                    'note' => $autoNote,
                    'created_by' => $user->id,
                ]);

                $created++;
            }
        });

        SystemLogger::log((int) $user->id, 'monthly_payroll_created', 'Created payroll for '.$month.' ('.$created.' employees)', 'EmployeeFinancial', null, $request);

        return back()->with('status', 'تم تسجيل المرتبات الشهرية. تمت الإضافة: '.$created.' | تم التجاوز: '.$skipped);
    }
}

