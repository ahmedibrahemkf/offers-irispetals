@extends('layouts.admin')
@section('title', 'صفحة الموظف')
@section('page_title', 'بيانات الموظف '.$employee->name)
@section('content')
  <div class="grid grid-3">
    <article class="card">
      <div class="muted">المرتب الأساسي</div>
      <h3>{{ number_format((float) $employee->base_salary, 2) }} ج</h3>
    </article>
    <article class="card">
      <div class="muted">إجمالي صافي المستحقات</div>
      <h3>{{ number_format((float) $summary['net'], 2) }} ج</h3>
    </article>
    <article class="card">
      <div class="muted">تاريخ التعيين</div>
      <h3>{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}</h3>
    </article>
  </div>

  <section class="card" style="margin-top:12px">
    <h3>بيانات أساسية</h3>
    <div class="grid grid-2">
      <p><b>الاسم:</b> {{ $employee->name }}</p>
      <p><b>الدور:</b> {{ $employee->role }}</p>
      <p><b>اسم المستخدم:</b> {{ $employee->username }}</p>
      <p><b>الهاتف:</b> {{ $employee->phone ?: '-' }}</p>
      <p><b>الحالة:</b> {{ $employee->is_active ? 'نشط' : 'موقوف' }}</p>
      <p><b>تاريخ الإضافة:</b> {{ optional($employee->created_at)->format('Y-m-d') }}</p>
    </div>
  </section>

  <section class="card" style="margin-top:12px">
    <h3>صلاحيات العمليات</h3>
    <form method="post" class="grid grid-2" action="{{ route('admin.employees.permissions.update', $employee) }}">
      @csrf
      @method('put')

      <div class="card" style="padding:10px">
        <div class="actions">
          <input type="hidden" name="can_create_records" value="0">
          <label><input type="checkbox" name="can_create_records" value="1" {{ $employee->canCreateRecords() ? 'checked' : '' }}> إضافة</label>

          <input type="hidden" name="can_update_records" value="0">
          <label><input type="checkbox" name="can_update_records" value="1" {{ $employee->canUpdateRecords() ? 'checked' : '' }}> تعديل</label>

          <input type="hidden" name="can_delete_records" value="0">
          <label><input type="checkbox" name="can_delete_records" value="1" {{ $employee->canDeleteRecords() ? 'checked' : '' }}> حذف</label>
        </div>
      </div>

      <div class="card" style="padding:10px">
        <input type="hidden" name="is_active" value="0">
        <label><input type="checkbox" name="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }}> المستخدم نشط</label>
      </div>

      <div class="actions" style="grid-column:1 / -1">
        <button class="btn btn-primary" type="submit">حفظ الصلاحيات</button>
      </div>
    </form>
  </section>

  <section class="card table-wrap" style="margin-top:12px">
    <h3>الحركات المالية</h3>
    <table>
      <thead>
        <tr>
          <th>النوع</th>
          <th>القيمة</th>
          <th>التاريخ</th>
          <th>ملاحظة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($financials as $row)
          <tr>
            <td>{{ $row->type }}</td>
            <td>{{ number_format((float) $row->amount, 2) }} ج</td>
            <td>{{ optional($row->effective_date)->format('Y-m-d') }}</td>
            <td>{{ $row->note ?: '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="4">لا توجد حركات مالية مسجلة</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $financials->links() }}</div>
  </section>
@endsection

