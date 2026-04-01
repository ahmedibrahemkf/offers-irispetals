@extends('layouts.admin')
@section('title', 'تفاصيل العميل')
@section('page_title', 'تفاصيل العميل '.$customer->name)
@section('content')
  <section class="card">
    <p><b>الاسم:</b> {{ $customer->name }}</p>
    <p><b>الهاتف:</b> {{ $customer->phone }}</p>
    <p><b>هاتف بديل:</b> {{ $customer->phone_alt ?: '-' }}</p>
    <p><b>العنوان:</b> {{ $customer->address ?: '-' }}</p>
    <p><b>ملاحظات:</b> {{ $customer->notes ?: '-' }}</p>
  </section>

  <div class="grid grid-2" style="margin-top:12px">
    <section class="card table-wrap">
      <h3>آخر الطلبات</h3>
      <table>
        <thead>
          <tr>
            <th>رقم</th>
            <th>الإجمالي</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td>{{ $order->order_number }}</td>
              <td>{{ number_format((float) $order->amount_total, 2) }} ج</td>
              <td>{{ $order->status }}</td>
            </tr>
          @empty
            <tr><td colspan="3">لا يوجد</td></tr>
          @endforelse
        </tbody>
      </table>
    </section>

    <section class="card table-wrap">
      <h3>آخر الفواتير</h3>
      <table>
        <thead>
          <tr>
            <th>رقم</th>
            <th>الإجمالي</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $invoice)
            <tr>
              <td>{{ $invoice->invoice_number }}</td>
              <td>{{ number_format((float) $invoice->total_amount, 2) }} ج</td>
              <td>{{ $invoice->payment_status }}</td>
            </tr>
          @empty
            <tr><td colspan="3">لا يوجد</td></tr>
          @endforelse
        </tbody>
      </table>
    </section>
  </div>
@endsection

