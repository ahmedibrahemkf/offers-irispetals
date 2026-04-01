<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomersController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $query = Customer::query()->orderByDesc('id');
        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q): void {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        return view('admin.customers.index', $this->sharedData($request) + [
            'customers' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:1500',
            'notes' => 'nullable|string|max:1500',
        ]);

        Customer::query()->create($validated);

        return back()->with('status', 'تمت إضافة العميل');
    }

    public function show(Request $request, Customer $customer): View
    {
        return view('admin.customers.show', $this->sharedData($request) + [
            'customer' => $customer,
            'orders' => Order::query()->where('customer_id', $customer->id)->orderByDesc('id')->limit(50)->get(),
            'invoices' => Invoice::query()->where('customer_id', $customer->id)->orderByDesc('id')->limit(50)->get(),
        ]);
    }
}

