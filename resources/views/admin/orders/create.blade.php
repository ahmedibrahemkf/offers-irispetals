@extends('layouts.admin')
@section('title', 'طلب جديد')
@section('page_title', 'إنشاء طلب جديد')
@section('content')
  @php
    $requiredFields = is_array($requiredFields ?? null) ? $requiredFields : ['customer_name'];
    $isRequired = static fn (string $field): bool => in_array($field, $requiredFields, true);
  @endphp

  <section class="card" style="margin-bottom:12px">
    <div class="actions" style="gap:10px">
      <span class="badge badge-confirmed">1) بيانات العميل</span>
      <span class="badge badge-progress">2) تفاصيل الطلب</span>
      <span class="badge badge-ready">3) التوصيل والتعيين</span>
      <span class="badge badge-new">4) المراجعة والحفظ</span>
    </div>
  </section>

  <form class="grid" method="post" action="{{ route('admin.orders.store') }}">
    @csrf

    <section class="card">
      <h3>1) بيانات العميل</h3>
      <div class="grid grid-2">
        <div><label>اسم العميل @if($isRequired('customer_name')) * @endif</label><input class="input" name="customer_name" @if($isRequired('customer_name')) required @endif></div>
        <div><label>هاتف العميل @if($isRequired('customer_phone')) * @endif</label><input class="input" name="customer_phone" @if($isRequired('customer_phone')) required @endif></div>
        <div>
          <label>المصدر</label>
          <select class="select" name="source">
            @foreach(['facebook'=>'فيسبوك','instagram'=>'إنستجرام','whatsapp'=>'واتساب','phone'=>'هاتف','walk_in'=>'زيارة','website'=>'الموقع','other'=>'أخرى'] as $srcKey=>$srcLabel)
              <option value="{{ $srcKey }}">{{ $srcLabel }}</option>
            @endforeach
          </select>
        </div>
        <div><label>المناسبة @if($isRequired('occasion')) * @endif</label><input class="input" name="occasion" @if($isRequired('occasion')) required @endif></div>
      </div>
    </section>

    <section class="card">
      <h3>2) تفاصيل الطلب</h3>
      <div class="grid grid-2">
        <div>
          <label>المنتج @if($isRequired('product_id')) * @endif</label>
          <select class="select" name="product_id" @if($isRequired('product_id')) required @endif>
            <option value="">—</option>
            @foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label>اللون @if($isRequired('color_id')) * @endif</label>
          <select class="select" name="color_id" @if($isRequired('color_id')) required @endif>
            <option value="">—</option>
            @foreach($colors as $color)<option value="{{ $color->id }}">{{ $color->name }}</option>@endforeach
          </select>
        </div>
        <div><label>الكمية @if($isRequired('quantity')) * @endif</label><input class="input" type="number" min="1" value="1" name="quantity" @if($isRequired('quantity')) required @endif></div>
        <div><label>سعر الوحدة @if($isRequired('unit_price')) * @endif</label><input class="input" type="number" step="0.01" min="0" name="unit_price" @if($isRequired('unit_price')) required @endif></div>
        <div><label>رسالة الكارت</label><textarea name="card_message"></textarea></div>
        <div><label>ملاحظات العميل @if($isRequired('notes')) * @endif</label><textarea name="notes" @if($isRequired('notes')) required @endif></textarea></div>
      </div>
    </section>

    <section class="card">
      <h3>3) التوصيل والتعيين</h3>
      <div class="grid grid-2">
        <div><label>العنوان @if($isRequired('delivery_address')) * @endif</label><input class="input" name="delivery_address" @if($isRequired('delivery_address')) required @endif></div>
        <div>
          <label>منطقة التوصيل @if($isRequired('shipping_zone_id')) * @endif</label>
          <select class="select" id="shipping_zone_id" name="shipping_zone_id" @if($isRequired('shipping_zone_id')) required @endif>
            <option value="">اختر المنطقة</option>
            @foreach($zones as $zone)
              <option value="{{ $zone->id }}" data-fee="{{ $zone->fee }}">{{ $zone->name }} - {{ number_format((float) $zone->fee, 2) }} ج</option>
            @endforeach
          </select>
        </div>
        <div><label>تاريخ التسليم @if($isRequired('delivery_date')) * @endif</label><input class="input" type="date" name="delivery_date" @if($isRequired('delivery_date')) required @endif></div>
        <div><label>وقت التسليم @if($isRequired('delivery_time_slot')) * @endif</label><input class="input" name="delivery_time_slot" placeholder="مثال: 3:00 م - 5:00 م" @if($isRequired('delivery_time_slot')) required @endif></div>
        <div><label>رسوم التوصيل</label><input class="input" id="delivery_fee" type="number" step="0.01" min="0" name="delivery_fee" value="0"></div>
        <div><label>ملاحظات داخلية @if($isRequired('internal_notes')) * @endif</label><textarea name="internal_notes" @if($isRequired('internal_notes')) required @endif></textarea></div>
        <div><label>اسم المستلم @if($isRequired('recipient_name')) * @endif</label><input class="input" name="recipient_name" @if($isRequired('recipient_name')) required @endif></div>
        <div><label>هاتف المستلم @if($isRequired('recipient_phone')) * @endif</label><input class="input" name="recipient_phone" @if($isRequired('recipient_phone')) required @endif></div>
        <div>
          <label>الموظف المسؤول</label>
          <select class="select" name="assigned_staff_id">
            <option value="">—</option>
            @foreach($staff as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label>الصنايعي</label>
          <select class="select" name="assigned_craftsman_id">
            <option value="">—</option>
            @foreach($craftsmen as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
          </select>
        </div>
      </div>
    </section>

    <section class="card">
      <h3>4) المراجعة والحفظ</h3>
      <p class="muted">تأكد من البيانات قبل الحفظ. بعد الحفظ سيظهر رقم الطلب تلقائيًا.</p>
      <div class="actions">
        <button class="btn btn-primary" type="submit">حفظ الطلب</button>
        <a class="btn btn-soft" href="{{ route('admin.orders.index') }}">إلغاء</a>
      </div>
    </section>
  </form>

  <script>
    (function(){
      const zone = document.getElementById('shipping_zone_id');
      const fee = document.getElementById('delivery_fee');
      if(!zone || !fee) return;
      zone.addEventListener('change', function(){
        const selected = zone.options[zone.selectedIndex];
        const amount = selected ? Number(selected.dataset.fee || 0) : 0;
        if(!Number.isNaN(amount)) {
          fee.value = amount.toFixed(2);
        }
      });
    })();
  </script>
@endsection
