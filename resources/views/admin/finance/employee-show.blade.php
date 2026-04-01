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

