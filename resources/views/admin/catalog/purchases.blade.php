@extends('layouts.admin')
@section('title', 'المشتريات')
@section('page_title', 'المشتريات')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <h3>إضافة فاتورة شراء</h3>
    <form method="post" class="grid grid-3" action="{{ route('admin.purchases.store') }}">
      @csrf
      <select class="select" name="supplier_id">
        <option value="">اختر مورد</option>
        @foreach($suppliers as $supplier)
          <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
        @endforeach
      </select>
      <input class="input" type="date" name="purchase_date">
      <select class="select" name="product_id" required>
        <option value="">اختر منتج</option>
        @foreach($products as $product)
          <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
      </select>
      <input class="input" type="number" min="1" name="quantity" placeholder="الكمية" required>
      <input class="input" type="number" step="0.01" min="0" name="unit_cost" placeholder="تكلفة الوحدة" required>
      <input class="input" name="notes" placeholder="ملاحظات">
      <button class="btn btn-primary" type="submit">حفظ</button>
    </form>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>رقم الشراء</th>
          <th>المورد</th>
          <th>التاريخ</th>
          <th>الإجمالي</th>
        </tr>
      </thead>
      <tbody>
        @forelse($purchases as $purchase)
          <tr>
            <td>{{ $purchase->purchase_number }}</td>
            <td>{{ $purchase->supplier?->name ?: '-' }}</td>
            <td>{{ $purchase->purchase_date }}</td>
            <td>{{ number_format((float) $purchase->total_amount, 2) }} ج</td>
          </tr>
        @empty
          <tr><td colspan="4">لا يوجد عمليات شراء</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $purchases->links() }}</div>
  </section>
@endsection

