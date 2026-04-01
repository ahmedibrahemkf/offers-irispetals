@extends('layouts.admin')
@section('title', 'المنتجات')
@section('page_title', 'المنتجات والمخزون')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <h3>إضافة منتج</h3>
    <form method="post" class="grid grid-3" action="{{ route('admin.products.store') }}">
      @csrf
      <input class="input" name="name" placeholder="اسم المنتج" required>
      <select class="select" name="product_category_id">
        <option value="">الفئة</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
      <input class="input" name="sku" placeholder="SKU">
      <input class="input" type="number" step="0.01" min="0" name="sell_price" placeholder="سعر البيع" required>
      <input class="input" type="number" step="0.01" min="0" name="cost_price" placeholder="سعر التكلفة">
      <input class="input" type="number" min="0" name="stock_quantity" placeholder="الكمية">
      <input class="input" type="number" min="0" name="min_stock_alert" placeholder="حد تنبيه المخزون">
      <button class="btn btn-primary" type="submit">حفظ</button>
    </form>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>المنتج</th>
          <th>الفئة</th>
          <th>بيع</th>
          <th>تكلفة</th>
          <th>مخزون</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category?->name ?: '-' }}</td>
            <td>{{ number_format((float) $product->sell_price, 2) }} ج</td>
            <td>{{ number_format((float) $product->cost_price, 2) }} ج</td>
            <td>{{ $product->stock_quantity }}</td>
            <td><a class="btn btn-soft" href="{{ route('admin.products.show', $product) }}">تفاصيل</a></td>
          </tr>
        @empty
          <tr><td colspan="6">لا يوجد منتجات</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $products->links() }}</div>
  </section>
@endsection

