@extends('layouts.admin')
@section('title', 'طلبات موظف الاستقبال')
@section('page_title', 'طلبات موظف الاستقبال')
@section('content')
  @php
    $statusLabels = [
      'new' => 'جديد',
      'confirmed' => 'مؤكد',
      'in_progress' => 'قيد التنفيذ',
      'ready' => 'جاهز',
      'out_for_delivery' => 'خارج للتوصيل',
      'delivered' => 'تم التسليم',
      'cancelled' => 'ملغي',
      'returned' => 'مرتجع',
    ];
  @endphp

  <section class="card" style="margin-bottom:12px">
    <form method="get" class="grid grid-3">
      <input class="input" name="q" value="{{ request('q') }}" placeholder="بحث رقم / اسم / هاتف">
      <select class="select" name="status">
        <option value="">كل الحالات</option>
        @foreach($statusLabels as $statusKey => $statusLabel)
          <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
        @endforeach
      </select>
      <div class="actions"><button class="btn btn-primary" type="submit">تصفية</button></div>
    </form>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>رقم الطلب</th>
          <th>العميل</th>
          <th>الحالة</th>
          <th>الإجمالي</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->customer_name_snapshot }}</td>
            <td>{{ $statusLabels[$order->status] ?? $order->status }}</td>
            <td>{{ number_format((float) $order->amount_total, 2) }} ج</td>
            <td><a class="btn btn-soft" href="{{ route('admin.orders.show', $order) }}">عرض</a></td>
          </tr>
        @empty
          <tr><td colspan="5">لا يوجد طلبات مخصصة لك</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $orders->links() }}</div>
  </section>
@endsection

