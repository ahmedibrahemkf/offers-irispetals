<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $settings?->shop_name ?: 'Iris Petals' }} - صفحة الطلب العامة</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;800&display=swap" rel="stylesheet">
  <style>
    body{margin:0;font-family:"Cairo",system-ui,sans-serif;background:#f7f4ff;color:#1E1B4B}
    .wrap{max-width:980px;margin:auto;padding:18px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .input,.select,textarea{width:100%;padding:12px;border:1px solid #e5e7eb;border-radius:10px;font-family:inherit}
    textarea{min-height:100px}
    .btn{border:0;background:#6D28D9;color:#fff;border-radius:10px;padding:12px 14px;font-family:inherit;font-weight:700;cursor:pointer}
    .ok{background:#e8f8ef;color:#047857;padding:10px 12px;border-radius:10px;margin-bottom:12px}
    .warn{background:#fffbeb;color:#92400e;padding:10px 12px;border-radius:10px;margin-bottom:12px}
    .hero{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-bottom:10px}
    .hero h1{margin:0}
    .btn-wa{display:inline-flex;align-items:center;justify-content:center;background:#16a34a;color:#fff;border-radius:10px;padding:10px 14px;font-weight:700}
    @media(max-width:640px){.grid{grid-template-columns:1fr}.btn{width:100%;min-height:44px}}
  </style>
</head>
<body>
<div class="wrap">
  <div class="hero">
    <h1>وردك في دقائق - من عندنا لبابك مباشرة</h1>
    @if(!empty($publicWhatsapp))
      <a class="btn-wa" target="_blank" href="https://wa.me/{{ $publicWhatsapp }}">واتساب</a>
    @endif
  </div>
  @if(!empty($settings?->shop_name))
    <p style="margin-top:0">{{ $settings->shop_name }}</p>
  @endif
  @if(session('status'))<div class="ok">{{ session('status') }}</div>@endif
  @if(session('wa_link'))
    <div class="warn">تم تجهيز رسالة واتساب تلقائيًا. <a href="{{ session('wa_link') }}" target="_blank">اضغط هنا لإرسالها الآن</a></div>
  @endif
  @if($errors->any())<div class="ok" style="background:#fef2f2;color:#b91c1c">{{ $errors->first() }}</div>@endif

  <form class="card grid" method="post" action="{{ route('public.order.submit') }}">
    @csrf
    <input class="input" name="customer_name" placeholder="اسم العميل" required>
    <input class="input" name="customer_phone" placeholder="الهاتف" required>
    <input class="input" name="delivery_address" placeholder="العنوان" style="grid-column:1/-1" required>
    <select class="select" name="product_id" required>
      <option value="">اختر المنتج</option>
      @foreach($products as $product)
        <option value="{{ $product->id }}">{{ $product->name }} - {{ number_format((float) $product->sell_price, 2) }} ج</option>
      @endforeach
    </select>
    <input class="input" type="number" min="1" name="quantity" value="1" required>
    <select class="select" name="shipping_zone_id">
      <option value="">منطقة التوصيل</option>
      @foreach($zones as $zone)
        <option value="{{ $zone->id }}">{{ $zone->name }} - {{ number_format((float) $zone->fee, 2) }} ج</option>
      @endforeach
    </select>
    <input class="input" type="date" name="delivery_date">
    <input class="input" name="delivery_time_slot" placeholder="وقت التسليم">
    <textarea name="notes" placeholder="ملاحظات"></textarea>
    <button class="btn" type="submit" style="grid-column:1/-1">إرسال الطلب</button>
  </form>
</div>
</body>
</html>

