@extends('layouts.admin')
@section('title', 'تفاصيل الطلب')
@section('page_title', 'تفاصيل الطلب '.$order->order_number)
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

  <div class="grid grid-2">
    <article class="card">
      <h3>بيانات الطلب</h3>
      <p><b>العميل:</b> {{ $order->customer_name_snapshot }} - {{ $order->customer_phone_snapshot }}</p>
      <p><b>الحالة:</b> {{ $statusLabels[$order->status] ?? $order->status }}</p>
      <p><b>الدفع:</b> {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</p>
      <p><b>الإجمالي:</b> {{ number_format((float) $order->amount_total, 2) }} ج</p>
      <p><b>المدفوع:</b> {{ number_format((float) $order->amount_paid, 2) }} ج</p>
      <p><b>المتبقي:</b> {{ number_format((float) $order->amount_remaining, 2) }} ج</p>
      <p><b>العنوان:</b> {{ $order->delivery_address ?: '-' }}</p>
      <p><b>موعد التسليم:</b> {{ optional($order->delivery_date)->format('Y-m-d') ?: '-' }} @if($order->delivery_time_slot) - {{ $order->delivery_time_slot }} @endif</p>
      <div class="actions">
        @if($authUser?->canUpdateRecords())
          <a class="btn btn-soft" href="{{ route('admin.orders.edit', $order) }}">تعديل</a>
        @endif
        @if($authUser?->canCreateRecords())
          <form method="post" action="{{ route('admin.invoices.from-order', $order) }}">
            @csrf
            <button class="btn btn-primary" type="submit">إنشاء فاتورة</button>
          </form>
        @endif
      </div>
    </article>

    <article class="card">
      <h3>تقسيم التحصيل</h3>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>المحصل</th>
              <th>المبلغ</th>
              <th>ملاحظة</th>
              <th>وقت التسجيل</th>
            </tr>
          </thead>
          <tbody>
            @forelse($order->collections as $collection)
              <tr>
                <td>{{ $collection->collector_name_snapshot }}</td>
                <td>{{ number_format((float) $collection->amount, 2) }} ج</td>
                <td>{{ $collection->note ?: '-' }}</td>
                <td>{{ optional($collection->created_at)->format('Y-m-d H:i') }}</td>
              </tr>
            @empty
              <tr><td colspan="4">لا يوجد تسجيل تحصيل مفصل لهذا الطلب</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </article>
  </div>

  <article class="card" style="margin-top:12px">
    <h3>عناصر الطلب</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>العنصر</th>
            <th>الكمية</th>
            <th>سعر الوحدة</th>
            <th>الإجمالي</th>
          </tr>
        </thead>
        <tbody>
          @forelse($order->items as $item)
            <tr>
              <td>{{ $item->item_name }}</td>
              <td>{{ $item->quantity }}</td>
              <td>{{ number_format((float) $item->unit_price, 2) }} ج</td>
              <td>{{ number_format((float) $item->line_total, 2) }} ج</td>
            </tr>
          @empty
            <tr><td colspan="4">لا يوجد عناصر</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </article>

  <article class="card" style="margin-top:12px">
    <h3>سجل الحالات</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>من</th>
            <th>إلى</th>
            <th>ملاحظة</th>
            <th>الوقت</th>
          </tr>
        </thead>
        <tbody>
          @forelse($statusLogs as $log)
            <tr>
              <td>{{ $statusLabels[$log->old_status] ?? ($log->old_status ?: '-') }}</td>
              <td>{{ $statusLabels[$log->new_status] ?? $log->new_status }}</td>
              <td>{{ $log->note ?: '-' }}</td>
              <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="4">لا يوجد سجل بعد</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </article>
@endsection

