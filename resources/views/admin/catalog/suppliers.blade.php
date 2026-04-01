@extends('layouts.admin')
@section('title', 'الموردون')
@section('page_title', 'الموردون')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <h3>إضافة مورد</h3>
    <form method="post" class="grid grid-3" action="{{ route('admin.suppliers.store') }}">
      @csrf
      <input class="input" name="name" placeholder="اسم المورد" required>
      <input class="input" name="phone" placeholder="الهاتف">
      <input class="input" name="email" placeholder="البريد">
      <input class="input" name="address" placeholder="العنوان">
      <input class="input" name="notes" placeholder="ملاحظات">
      <button class="btn btn-primary" type="submit">حفظ</button>
    </form>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>الاسم</th>
          <th>الهاتف</th>
          <th>البريد</th>
          <th>العنوان</th>
        </tr>
      </thead>
      <tbody>
        @forelse($suppliers as $supplier)
          <tr>
            <td>{{ $supplier->name }}</td>
            <td>{{ $supplier->phone ?: '-' }}</td>
            <td>{{ $supplier->email ?: '-' }}</td>
            <td>{{ $supplier->address ?: '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="4">لا يوجد موردون</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $suppliers->links() }}</div>
  </section>
@endsection

