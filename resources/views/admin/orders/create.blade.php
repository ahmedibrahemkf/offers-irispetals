@extends('layouts.admin')
@section('title', 'طلب جديد')
@section('page_title', 'إنشاء طلب جديد')
@section('content')
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
        <div><label>اسم العميل</label><input class="input" name="customer_name" required></div>
        <div><label>هاتف العميل</label><input class="input" name="customer_phone"></div>
        <div>
          <label>المصدر</label>
          <select class="select" name="source">
            @foreach(['facebook'=>'فيسبوك','instagram'=>'إنستجرام','whatsapp'=>'واتساب','phone'=>'هاتف','walk_in'=>'زيارة','website'=>'الموقع','other'=>'أخرى'] as $srcKey=>$srcLabel)
              <option value="{{ $srcKey }}">{{ $srcLabel }}</option>
            @endforeach
          </select>
        </div>
        <div><label>المناسبة</label><input class="input" name="occasion"></div>
      </div>
    </section>

    <section class="card">
      <h3>2) تفاصيل الطلب</h3>
      <div class="grid grid-2">
        <div>
          <label>المنتج</label>
          <select class="select" name="product_id">
            <option value="">—</option>
            @foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label>اللون</label>
          <select class="select" name="color_id">
            <option value="">—</option>
            @foreach($colors as $color)<option value="{{ $color->id }}">{{ $color->name }}</option>@endforeach
          </select>
        </div>
        <div><label>الكمية</label><input class="input" type="number" min="1" value="1" name="quantity"></div>
        <div><label>سعر الوحدة</label><input class="input" type="number" step="0.01" min="0" name="unit_price"></div>
        <div><label>رسالة الكارت</label><textarea name="card_message"></textarea></div>
        <div><label>ملاحظات العميل</label><textarea name="notes"></textarea></div>
      </div>
    </section>

    <section class="card">
      <h3>3) التوصيل والتعيين</h3>
      <div class="grid grid-2">
        <div><label>العنوان</label><input class="input" name="delivery_address"></div>
        <div>
          <label>منطقة التوصيل</label>
          <select class="select" id="shipping_zone_id" name="shipping_zone_id">
            <option value="">اختر المنطقة</option>
            @foreach($zones as $zone)
              <option value="{{ $zone->id }}" data-fee="{{ $zone->fee }}">{{ $zone->name }} - {{ number_format((float) $zone->fee, 2) }} ج</option>
            @endforeach
          </select>
        </div>
        <div><label>تاريخ التسليم</label><input class="input" type="date" name="delivery_date"></div>
        <div><label>وقت التسليم</label><input class="input" name="delivery_time_slot" placeholder="مثال: 3:00 م - 5:00 م"></div>
        <div><label>رسوم التوصيل</label><input class="input" id="delivery_fee" type="number" step="0.01" min="0" name="delivery_fee" value="0"></div>
        <div><label>ملاحظات داخلية</label><textarea name="internal_notes"></textarea></div>
        <div><label>اسم المستلم</label><input class="input" name="recipient_name"></div>
        <div><label>هاتف المستلم</label><input class="input" name="recipient_phone"></div>
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

