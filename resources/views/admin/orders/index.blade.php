@extends('layouts.admin')
@section('title', 'إدارة الطلبات')
@section('page_title', 'إدارة الطلبات')
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
    $paymentLabels = [
      'unpaid' => 'غير مدفوع',
      'partial' => 'مدفوع جزئي',
      'paid' => 'مدفوع',
      'refunded' => 'مسترجع',
    ];
  @endphp

  <div class="card" style="margin-bottom:12px">
    <form method="get" class="grid grid-4">
      <input class="input" name="q" value="{{ request('q') }}" placeholder="بحث رقم / اسم / هاتف">
      <select class="select" name="status">
        <option value="">كل الحالات</option>
        @foreach($statusLabels as $statusKey => $statusLabel)
          <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
        @endforeach
      </select>
      <select class="select" name="payment_status">
        <option value="">كل حالات الدفع</option>
        @foreach($paymentLabels as $paymentKey => $paymentLabel)
          <option value="{{ $paymentKey }}" @selected(request('payment_status') === $paymentKey)>{{ $paymentLabel }}</option>
        @endforeach
      </select>
      <div class="actions">
        <button class="btn btn-primary" type="submit">تصفية</button>
        @if($authUser?->canCreateRecords())
          <a class="btn btn-soft" href="{{ route('admin.orders.create') }}">طلب جديد</a>
        @endif
      </div>
    </form>
  </div>

  <div class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>العميل</th>
          <th>التاريخ</th>
          <th>الإجمالي</th>
          <th>المدفوع</th>
          <th>المتبقي</th>
          <th>الحالة</th>
          <th>الدفع</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->customer_name_snapshot }}<br><span class="muted">{{ $order->customer_phone_snapshot }}</span></td>
            <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
            <td>{{ number_format((float) $order->amount_total, 2) }} ج</td>
            <td>{{ number_format((float) $order->amount_paid, 2) }} ج</td>
            <td>{{ number_format((float) $order->amount_remaining, 2) }} ج</td>
            <td><span class="badge badge-{{ match($order->status){'new'=>'new','confirmed'=>'confirmed','in_progress'=>'progress','ready'=>'ready','out_for_delivery'=>'delivery','delivered'=>'done','cancelled'=>'cancelled','returned'=>'returned',default=>'new'} }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td>
            <td>{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</td>
            <td class="actions">
              <a class="btn btn-soft" href="{{ route('admin.orders.show', $order) }}">عرض</a>
              @if($authUser?->canUpdateRecords())
                <a class="btn btn-soft" href="{{ route('admin.orders.edit', $order) }}">تعديل</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="muted">لا يوجد طلبات</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $orders->links() }}</div>
  </div>
@endsection

