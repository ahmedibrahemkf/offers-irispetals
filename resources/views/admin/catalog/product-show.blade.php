@extends('layouts.admin')
@section('title', 'تفاصيل المنتج')
@section('page_title', 'تفاصيل المنتج '.$product->name)
@section('content')
  <div class="grid grid-2">
    <section class="card">
      <p><b>الاسم:</b> {{ $product->name }}</p>
      <p><b>الفئة:</b> {{ $product->category?->name ?: '-' }}</p>
      <p><b>سعر البيع:</b> {{ number_format((float) $product->sell_price, 2) }} ج</p>
      <p><b>سعر التكلفة:</b> {{ number_format((float) $product->cost_price, 2) }} ج</p>
      <p><b>المخزون الحالي:</b> {{ $product->stock_quantity }}</p>
      <p><b>حد التنبيه:</b> {{ $product->min_stock_alert }}</p>
    </section>

    <section class="card">
      <h3>تسوية المخزون</h3>
      <form method="post" action="{{ route('admin.products.stock.adjust', $product) }}" class="grid grid-2">
        @csrf
        <input class="input" type="number" name="quantity_change" placeholder="+10 أو -3" required>
        <input class="input" name="note" placeholder="سبب التعديل">
        <button class="btn btn-primary" type="submit">تنفيذ التعديل</button>
      </form>
    </section>
  </div>

  <section class="card table-wrap" style="margin-top:12px">
    <h3>حركات المخزون</h3>
    <table>
      <thead>
        <tr>
          <th>النوع</th>
          <th>التغير</th>
          <th>المرجع</th>
          <th>ملاحظة</th>
          <th>الوقت</th>
        </tr>
      </thead>
      <tbody>
        @forelse($movements as $movement)
          <tr>
            <td>{{ $movement->type }}</td>
            <td>{{ $movement->quantity_change }}</td>
            <td>{{ $movement->reference_type ?: '-' }} @if($movement->reference_id)#{{ $movement->reference_id }}@endif</td>
            <td>{{ $movement->note ?: '-' }}</td>
            <td>{{ optional($movement->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="5">لا يوجد حركات</td></tr>
        @endforelse
      </tbody>
    </table>
  </section>
@endsection

