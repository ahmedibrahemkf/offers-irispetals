@extends('layouts.admin')
@section('title', 'الفواتير')
@section('page_title', 'الفواتير والمبيعات')
@section('content')
  @php
    $paymentLabels = [
      'unpaid' => 'غير مدفوع',
      'partial' => 'مدفوع جزئي',
      'paid' => 'مدفوع',
      'refunded' => 'مسترجع',
    ];
  @endphp

  <div class="grid grid-2">
    <section class="card">
      <h3>بيع مباشر جديد</h3>
      <form method="post" class="grid grid-2" action="{{ route('admin.invoices.direct') }}">
        @csrf
        <div><label>اسم العميل</label><input class="input" name="customer_name" placeholder="عميل مباشر"></div>
        <div><label>العنصر</label><input class="input" name="item_name" required></div>
        <div><label>الكمية</label><input class="input" type="number" min="1" name="quantity" value="1" required></div>
        <div><label>سعر الوحدة</label><input class="input" type="number" step="0.01" min="0" name="unit_price" required></div>
        <div><label>المبلغ المدفوع</label><input class="input" type="number" step="0.01" min="0" name="paid_amount"></div>
        <div class="actions"><button class="btn btn-primary" type="submit">إنشاء بيع مباشر</button></div>
      </form>
    </section>

    <section class="card">
      <h3>إنشاء فاتورة من طلب</h3>
      <form method="post" action="#" id="fromOrderForm">
        @csrf
        <select class="select" id="orderSelect" required>
          <option value="">اختر طلب</option>
          @foreach($ordersWithoutInvoice as $order)
            <option value="{{ route('admin.invoices.from-order', $order) }}">{{ $order->order_number }} - {{ $order->customer_name_snapshot }}</option>
          @endforeach
        </select>
        <div class="actions" style="margin-top:10px">
          <button class="btn btn-primary" type="submit">إنشاء فاتورة</button>
        </div>
      </form>
    </section>
  </div>

  <div class="card table-wrap" style="margin-top:12px">
    <table>
      <thead>
        <tr>
          <th>#فاتورة</th>
          <th>النوع</th>
          <th>العميل</th>
          <th>التاريخ</th>
          <th>الإجمالي</th>
          <th>المدفوع</th>
          <th>المتبقي</th>
          <th>الحالة</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($invoices as $invoice)
          <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->type === 'direct' ? 'بيع مباشر' : 'فاتورة طلب' }}</td>
            <td>{{ $invoice->customer_name_snapshot ?: '-' }}</td>
            <td>{{ optional($invoice->issued_at)->format('Y-m-d H:i') }}</td>
            <td>{{ number_format((float) $invoice->total_amount, 2) }} ج</td>
            <td>{{ number_format((float) $invoice->paid_amount, 2) }} ج</td>
            <td>{{ number_format((float) $invoice->remaining_amount, 2) }} ج</td>
            <td>{{ $paymentLabels[$invoice->payment_status] ?? $invoice->payment_status }}</td>
            <td class="actions">
              <a class="btn btn-soft" href="{{ route('admin.invoices.show', $invoice) }}">عرض</a>
              <a class="btn btn-soft" target="_blank" href="{{ route('admin.invoices.print', $invoice) }}">طباعة</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9">لا يوجد فواتير</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $invoices->links() }}</div>
  </div>

  <script>
    document.getElementById('fromOrderForm').addEventListener('submit', function (event) {
      event.preventDefault();
      var url = document.getElementById('orderSelect').value;
      if (!url) return;
      this.action = url;
      this.submit();
    });
  </script>
@endsection

