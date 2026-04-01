@extends('layouts.admin')
@section('title', 'الإعدادات')
@section('page_title', 'إعدادات النظام')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <h3>البيانات العامة وإعدادات الفاتورة</h3>
    <form method="post" enctype="multipart/form-data" class="grid grid-3" action="{{ route('admin.settings.main.update') }}">
      @csrf
      <input class="input" name="shop_name" placeholder="اسم المحل" value="{{ old('shop_name', $setting?->shop_name) }}" required>
      <input class="input" name="phone" placeholder="الهاتف" value="{{ old('phone', $setting?->phone) }}">
      <input class="input" name="phone_alt" placeholder="هاتف بديل" value="{{ old('phone_alt', $setting?->phone_alt) }}">
      <input class="input" name="whatsapp" placeholder="واتساب" value="{{ old('whatsapp', $setting?->whatsapp) }}">
      <input class="input" name="email" placeholder="البريد" value="{{ old('email', $setting?->email) }}">
      <input class="input" name="website_url" placeholder="الموقع" value="{{ old('website_url', $setting?->website_url) }}">
      <input class="input" name="primary_color" placeholder="#6D28D9" value="{{ old('primary_color', $setting?->primary_color) }}">
      <input class="input" name="currency" placeholder="EGP" value="{{ old('currency', $setting?->currency) }}">
      <input class="input" name="currency_symbol" placeholder="ج" value="{{ old('currency_symbol', $setting?->currency_symbol) }}">
      <input class="input" type="file" name="logo" accept="image/*">
      <input class="input" type="file" name="invoice_logo" accept="image/*">
      <input class="input" name="tax_rate" type="number" step="0.01" min="0" max="100" value="{{ old('tax_rate', $setting?->tax_rate) }}" placeholder="نسبة الضريبة">
      <label><input type="checkbox" name="show_tax" value="1" @checked(old('show_tax', $setting?->show_tax))> إظهار الضريبة في الفاتورة</label>
      @php
        $selectedRequiredFields = old('order_required_fields', $setting?->order_required_fields ?? ['customer_name']);
        if (! is_array($selectedRequiredFields)) {
            $selectedRequiredFields = ['customer_name'];
        }
      @endphp
      <div class="card" style="grid-column:1/-1;padding:12px">
        <h4 style="margin:0 0 8px">الحقول الإلزامية في شاشة إضافة الطلب</h4>
        <p class="muted" style="margin:0 0 8px">اختاري الحقول اللي لازم المستخدم يملأها قبل الحفظ.</p>
        <div class="grid grid-3">
          @foreach($orderFieldOptions as $fieldKey => $fieldLabel)
            <label style="display:flex;align-items:center;gap:6px">
              <input type="checkbox" name="order_required_fields[]" value="{{ $fieldKey }}" @checked(in_array($fieldKey, $selectedRequiredFields, true))>
              <span>{{ $fieldLabel }}</span>
            </label>
          @endforeach
        </div>
      </div>
      <textarea name="address" placeholder="العنوان">{{ old('address', $setting?->address) }}</textarea>
      <textarea name="invoice_header_extra" placeholder="نص إضافي أعلى الفاتورة">{{ old('invoice_header_extra', $setting?->invoice_header_extra) }}</textarea>
      <textarea name="invoice_footer_text" placeholder="نص أسفل الفاتورة">{{ old('invoice_footer_text', $setting?->invoice_footer_text) }}</textarea>
      <textarea name="invoice_terms" placeholder="شروط البيع">{{ old('invoice_terms', $setting?->invoice_terms) }}</textarea>
      <div class="actions"><button class="btn btn-primary" type="submit">حفظ الإعدادات</button></div>
    </form>
  </section>

  <div class="grid grid-2">
    <section class="card">
      <h3>مناطق التوصيل</h3>
      <form method="post" class="grid grid-3" action="{{ route('admin.settings.zones.store') }}">
        @csrf
        <input class="input" name="name" placeholder="اسم المنطقة" required>
        <input class="input" type="number" step="0.01" min="0" name="fee" placeholder="رسوم">
        <input class="input" type="number" min="0" name="eta_minutes" placeholder="المدة بالدقائق">
        <button class="btn btn-primary" type="submit">إضافة</button>
      </form>
      <ul>
        @forelse($zones as $zone)
          <li>{{ $zone->name }} - {{ number_format((float) $zone->fee, 2) }} ج</li>
        @empty
          <li>لا توجد مناطق</li>
        @endforelse
      </ul>
    </section>

    <section class="card">
      <h3>الألوان</h3>
      <form method="post" class="grid grid-3" action="{{ route('admin.settings.colors.store') }}">
        @csrf
        <input class="input" name="name" placeholder="اسم اللون" required>
        <input class="input" name="hex_code" placeholder="#FFFFFF" required>
        <button class="btn btn-primary" type="submit">إضافة</button>
      </form>
      <ul>
        @forelse($colors as $color)
          <li>{{ $color->name }} - {{ $color->hex_code }}</li>
        @empty
          <li>لا توجد ألوان</li>
        @endforelse
      </ul>
    </section>

    <section class="card">
      <h3>فئات المنتجات</h3>
      <form method="post" class="actions" action="{{ route('admin.settings.product-categories.store') }}">
        @csrf
        <input class="input" name="name" placeholder="اسم الفئة" required>
        <button class="btn btn-primary" type="submit">إضافة</button>
      </form>
      <ul>
        @forelse($productCategories as $category)
          <li>{{ $category->name }}</li>
        @empty
          <li>لا توجد فئات</li>
        @endforelse
      </ul>
    </section>

    <section class="card">
      <h3>فئات المصروفات</h3>
      <form method="post" class="actions" action="{{ route('admin.settings.expense-categories.store') }}">
        @csrf
        <input class="input" name="name" placeholder="اسم الفئة" required>
        <button class="btn btn-primary" type="submit">إضافة</button>
      </form>
      <ul>
        @forelse($expenseCategories as $category)
          <li>{{ $category->name }}</li>
        @empty
          <li>لا توجد فئات</li>
        @endforelse
      </ul>
    </section>

    <section class="card">
      <h3>المحصلون</h3>
      <form method="post" class="grid grid-3" action="{{ route('admin.settings.collectors.store') }}">
        @csrf
        <input class="input" name="name" placeholder="اسم المحصل" required>
        <input class="input" name="phone" placeholder="رقم هاتف المحصل">
        <button class="btn btn-primary" type="submit">إضافة</button>
      </form>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>الاسم</th>
              <th>الهاتف</th>
              <th>الحالة</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($collectors as $collector)
              <tr>
                <td>
                  <input class="input" name="name" value="{{ $collector->name }}" required form="collector-update-{{ $collector->id }}">
                </td>
                <td>
                  <input class="input" name="phone" value="{{ $collector->phone }}" form="collector-update-{{ $collector->id }}">
                </td>
                <td>
                  <label style="display:flex;align-items:center;gap:6px">
                    <input type="checkbox" name="is_active" value="1" @checked($collector->is_active) form="collector-update-{{ $collector->id }}">
                    نشط
                  </label>
                </td>
                <td style="display:flex;gap:8px;flex-wrap:wrap">
                  <form id="collector-update-{{ $collector->id }}" method="post" action="{{ route('admin.settings.collectors.update', $collector) }}" style="display:none">
                    @csrf
                    @method('PUT')
                  </form>
                  <button class="btn btn-primary" type="submit" form="collector-update-{{ $collector->id }}">حفظ</button>
                  <form method="post" action="{{ route('admin.settings.collectors.destroy', $collector) }}" onsubmit="return confirm('حذف المحصل؟');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">حذف</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4">لا يوجد محصلون حتى الآن</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection
