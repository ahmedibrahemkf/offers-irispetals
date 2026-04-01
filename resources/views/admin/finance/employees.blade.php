@extends('layouts.admin')
@section('title', 'الموظفون')
@section('page_title', 'الموظفون والرواتب والسلف')
@section('content')
  <div class="grid grid-2">
    <section class="card">
      <h3>إضافة موظف</h3>
      <form method="post" class="grid grid-3" action="{{ route('admin.employees.store') }}">
        @csrf
        <input class="input" name="name" placeholder="الاسم" required>
        <input class="input" name="username" placeholder="اسم المستخدم" required>
        <input class="input" name="phone" placeholder="الهاتف">
        <select class="select" name="role">
          @foreach(['owner' => 'مالك', 'manager' => 'مدير', 'staff' => 'استقبال', 'craftsman' => 'صنايعي', 'viewer' => 'متابع'] as $roleKey => $roleLabel)
            <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
          @endforeach
        </select>
        <input class="input" type="number" step="0.01" min="0" name="base_salary" placeholder="المرتب الأساسي">
        <input class="input" type="date" name="hire_date">
        <input class="input" type="password" name="password" placeholder="كلمة المرور" required>
        <button class="btn btn-primary" type="submit">حفظ الموظف</button>
      </form>
    </section>

    <section class="card">
      <h3>تسجيل حركة مالية</h3>
      <form method="post" class="grid grid-2" action="{{ route('admin.employees.financials.store') }}">
        @csrf
        <select class="select" name="employee_id" required>
          <option value="">اختر موظف</option>
          @foreach($employeesForSelect as $employee)
            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->role }})</option>
          @endforeach
        </select>
        <select class="select" name="type">
          <option value="salary">راتب</option>
          <option value="advance">سلفة</option>
          <option value="deduction">خصم</option>
          <option value="bonus">مكافأة</option>
        </select>
        <input class="input" type="number" step="0.01" min="0.01" name="amount" placeholder="القيمة" required>
        <input class="input" type="date" name="effective_date">
        <input class="input" name="note" placeholder="ملاحظة">
        <button class="btn btn-primary" type="submit">تسجيل</button>
      </form>
    </section>
  </div>

  <section class="card" style="margin-top:12px">
    <h3>صرف المرتبات الشهرية</h3>
    <form method="post" class="grid grid-3" action="{{ route('admin.employees.payroll.monthly') }}">
      @csrf
      <div>
        <label>الشهر</label>
        <input class="input" type="month" name="month" value="{{ $payrollMonth }}" required>
      </div>
      <div>
        <label>ملاحظة (اختياري)</label>
        <input class="input" name="note" placeholder="مثال: دفعة شهر أبريل">
      </div>
      <div class="actions" style="align-items:end">
        <button class="btn btn-primary" type="submit">تسجيل صرف المرتبات</button>
      </div>
    </form>
    <p class="muted" style="margin:8px 0 0">يتم صرف المرتب الأساسي لكل موظف نشط مرة واحدة فقط لكل شهر.</p>
  </section>

  <section class="card table-wrap" style="margin-top:12px">
    <h3>قائمة الموظفين</h3>
    <table>
      <thead>
        <tr>
          <th>الاسم</th>
          <th>اسم المستخدم</th>
          <th>الدور</th>
          <th>المرتب</th>
          <th>الحالة</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($employees as $employee)
          <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->username }}</td>
            <td>{{ $employee->role }}</td>
            <td>{{ number_format((float) $employee->base_salary, 2) }} ج</td>
            <td>{{ $employee->is_active ? 'نشط' : 'موقوف' }}</td>
            <td><a class="btn btn-soft" href="{{ route('admin.employees.show', $employee) }}">عرض</a></td>
          </tr>
        @empty
          <tr><td colspan="6">لا يوجد موظفون</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $employees->links() }}</div>
  </section>

  <section class="card table-wrap" style="margin-top:12px">
    <h3>آخر الحركات المالية</h3>
    <table>
      <thead>
        <tr>
          <th>الموظف</th>
          <th>النوع</th>
          <th>القيمة</th>
          <th>التاريخ</th>
          <th>ملاحظة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($financials as $financial)
          <tr>
            <td>{{ $financial->employee?->name }}</td>
            <td>{{ $financial->type }}</td>
            <td>{{ number_format((float) $financial->amount, 2) }} ج</td>
            <td>{{ $financial->effective_date }}</td>
            <td>{{ $financial->note ?: '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="5">لا توجد بيانات</td></tr>
        @endforelse
      </tbody>
    </table>
  </section>
@endsection

