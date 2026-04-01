@extends('layouts.admin')
@section('title', 'تفاصيل الفاتورة')
@section('page_title', 'تفاصيل الفاتورة '.$invoice->invoice_number)
@section('content')
  @php
    $paymentLabels = [
      'unpaid' => 'غير مدفوع',
      'partial' => 'مدفوع جزئي',
      'paid' => 'مدفوع',
      'refunded' => 'مسترجع',
    ];
  @endphp

  <section class="card" style="margin-bottom:12px">
    <div class="actions">
      <a class="btn btn-primary" target="_blank" href="{{ route('admin.invoices.print', $invoice) }}">طباعة الفاتورة</a>
      <a class="btn btn-soft" href="{{ route('admin.invoices.index') }}">الرجوع للقائمة</a>
    </div>
  </section>

  <div class="grid grid-2">
    <section class="card">
      <h3>بيانات الفاتورة</h3>
      <p><b>العميل:</b> {{ $invoice->customer_name_snapshot ?: '-' }}</p>
      <p><b>النوع:</b> {{ $invoice->type === 'direct' ? 'بيع مباشر' : 'فاتورة طلب' }}</p>
      <p><b>الإجمالي:</b> {{ number_format((float) $invoice->total_amount, 2) }} ج</p>
      <p><b>المدفوع:</b> {{ number_format((float) $invoice->paid_amount, 2) }} ج</p>
      <p><b>المتبقي:</b> {{ number_format((float) $invoice->remaining_amount, 2) }} ج</p>
      <p><b>الحالة:</b> {{ $paymentLabels[$invoice->payment_status] ?? $invoice->payment_status }}</p>
    </section>

    <section class="card">
      <h3>تسجيل دفعة</h3>
      <form method="post" class="grid grid-2" action="{{ route('admin.invoices.payments.store', $invoice) }}">
        @csrf
        <div><label>المبلغ</label><input class="input" type="number" step="0.01" min="0.01" name="amount" required></div>
        <div><label>الطريقة</label><input class="input" name="method" placeholder="cash / instapay"></div>
        <div style="grid-column:1/-1"><label>ملاحظة</label><textarea name="note"></textarea></div>
        <div><button class="btn btn-primary" type="submit">تسجيل دفعة</button></div>
      </form>
    </section>
  </div>

  <section class="card table-wrap" style="margin-top:12px">
    <h3>بنود الفاتورة</h3>
    <table>
      <thead>
        <tr>
          <th>العنصر</th>
          <th>كمية</th>
          <th>سعر</th>
          <th>إجمالي</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format((float) $item->unit_price, 2) }} ج</td>
            <td>{{ number_format((float) $item->line_total, 2) }} ج</td>
          </tr>
        @empty
          <tr><td colspan="4">لا توجد بنود</td></tr>
        @endforelse
      </tbody>
    </table>
  </section>

  <section class="card table-wrap" style="margin-top:12px">
    <h3>الدفعات</h3>
    <table>
      <thead>
        <tr>
          <th>المبلغ</th>
          <th>الطريقة</th>
          <th>الوقت</th>
          <th>ملاحظة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payments as $payment)
          <tr>
            <td>{{ number_format((float) $payment->amount, 2) }} ج</td>
            <td>{{ $payment->method ?: '-' }}</td>
            <td>{{ optional($payment->paid_at)->format('Y-m-d H:i') }}</td>
            <td>{{ $payment->note ?: '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="4">لا يوجد دفعات بعد</td></tr>
        @endforelse
      </tbody>
    </table>
  </section>
@endsection

