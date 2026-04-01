<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>فاتورة {{ $invoice->invoice_number }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body{margin:0;font-family:"Cairo",system-ui,sans-serif;color:#111827;background:#fff}
    .wrap{max-width:980px;margin:auto;padding:24px}
    .head{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;border-bottom:2px solid #e5e7eb;padding-bottom:14px}
    .head img{max-height:74px;max-width:150px;object-fit:contain}
    h1{margin:0;font-size:24px}
    .muted{color:#6b7280}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:18px}
    table{width:100%;border-collapse:collapse;margin-top:14px}
    th,td{border:1px solid #e5e7eb;padding:10px;text-align:right}
    th{background:#f9fafb}
    .totals{margin-top:14px;max-width:360px;margin-inline-start:auto}
    .totals div{display:flex;justify-content:space-between;border-bottom:1px dashed #d1d5db;padding:6px 0}
    .totals div:last-child{border-bottom:2px solid #111827;font-weight:700}
    .actions{display:flex;gap:8px;justify-content:flex-start;margin-top:16px}
    .btn{border:0;border-radius:10px;padding:10px 14px;font-family:inherit;font-weight:700;cursor:pointer}
    .btn-primary{background:#6D28D9;color:#fff}
    .btn-soft{background:#fff;border:1px solid #d1d5db}
    .foot{margin-top:18px;border-top:1px solid #e5e7eb;padding-top:12px}
    @media print{
      .no-print{display:none!important}
      .wrap{padding:0}
      @page{size:A4;margin:10mm}
    }
  </style>
</head>
<body>
<div class="wrap">
  <header class="head">
    <div>
      <h1>فاتورة رقم {{ $invoice->invoice_number }}</h1>
      <div class="muted">تاريخ الإصدار: {{ optional($invoice->issued_at)->format('Y-m-d H:i') }}</div>
      <div class="muted">نوع الفاتورة: {{ $invoice->type === 'direct' ? 'بيع مباشر' : 'فاتورة طلب' }}</div>
    </div>
    <div style="text-align:left">
      @if(!empty($settings?->invoice_logo_url))
        <img src="{{ $settings->invoice_logo_url }}" alt="logo">
      @elseif(!empty($settings?->logo_url))
        <img src="{{ $settings->logo_url }}" alt="logo">
      @endif
      <div><b>{{ $settings?->shop_name ?? 'Iris Petals' }}</b></div>
      <div class="muted">{{ $settings?->phone ?: '' }}</div>
      <div class="muted">{{ $settings?->address ?: '' }}</div>
    </div>
  </header>

  <section class="grid">
    <div><b>اسم العميل:</b> {{ $invoice->customer_name_snapshot ?: 'عميل مباشر' }}</div>
    <div><b>حالة الدفع:</b> {{ $invoice->payment_status }}</div>
    <div><b>المدفوع:</b> {{ number_format((float) $invoice->paid_amount,2) }} {{ $settings?->currency_symbol ?: 'ج' }}</div>
    <div><b>المتبقي:</b> {{ number_format((float) $invoice->remaining_amount,2) }} {{ $settings?->currency_symbol ?: 'ج' }}</div>
  </section>

  <table>
    <thead>
    <tr><th>العنصر</th><th>الكمية</th><th>سعر الوحدة</th><th>الإجمالي</th></tr>
    </thead>
    <tbody>
    @foreach($items as $item)
      <tr>
        <td>{{ $item->item_name }}</td>
        <td>{{ $item->quantity }}</td>
        <td>{{ number_format((float) $item->unit_price,2) }}</td>
        <td>{{ number_format((float) $item->line_total,2) }}</td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <div class="totals">
    <div><span>المجموع قبل الخصم</span><span>{{ number_format((float) $invoice->sub_total,2) }}</span></div>
    <div><span>الخصم</span><span>{{ number_format((float) $invoice->discount_amount,2) }}</span></div>
    <div><span>رسوم التوصيل</span><span>{{ number_format((float) $invoice->delivery_fee,2) }}</span></div>
    <div><span>الإجمالي النهائي</span><span>{{ number_format((float) $invoice->total_amount,2) }} {{ $settings?->currency_symbol ?: 'ج' }}</span></div>
  </div>

  @if($payments->count() > 0)
    <section style="margin-top:14px">
      <h3>سجل الدفعات</h3>
      <table>
        <thead><tr><th>المبلغ</th><th>الطريقة</th><th>التاريخ</th><th>ملاحظة</th></tr></thead>
        <tbody>
        @foreach($payments as $payment)
          <tr>
            <td>{{ number_format((float) $payment->amount,2) }}</td>
            <td>{{ $payment->method ?: '-' }}</td>
            <td>{{ optional($payment->paid_at)->format('Y-m-d H:i') }}</td>
            <td>{{ $payment->note ?: '-' }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </section>
  @endif

  <footer class="foot">
    @if(!empty($settings?->invoice_terms))
      <div><b>الشروط:</b> {{ $settings->invoice_terms }}</div>
    @endif
    @if(!empty($settings?->invoice_footer_text))
      <div class="muted">{{ $settings->invoice_footer_text }}</div>
    @endif
  </footer>

  <div class="actions no-print">
    <button class="btn btn-primary" onclick="window.print()">طباعة الفاتورة</button>
    <a class="btn btn-soft" href="{{ route('admin.invoices.show', $invoice) }}">رجوع للفاتورة</a>
  </div>
</div>
</body>
</html>

