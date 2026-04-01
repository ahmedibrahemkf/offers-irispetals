@extends('layouts.admin')
@section('title', 'العملاء')
@section('page_title', 'العملاء')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <form method="get" class="actions">
      <input class="input" style="max-width:380px" name="q" value="{{ request('q') }}" placeholder="بحث بالاسم أو الهاتف">
      <button class="btn btn-soft" type="submit">بحث</button>
    </form>
  </section>

  <section class="card" style="margin-bottom:12px">
    <h3>إضافة عميل</h3>
    <form method="post" class="grid grid-3" action="{{ route('admin.customers.store') }}">
      @csrf
      <input class="input" name="name" placeholder="الاسم" required>
      <input class="input" name="phone" placeholder="الهاتف" required>
      <input class="input" name="phone_alt" placeholder="هاتف بديل">
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
          <th>العميل</th>
          <th>الهاتف</th>
          <th>العنوان</th>
          <th>إجراء</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $customer)
          <tr>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->address ?: '-' }}</td>
            <td><a class="btn btn-soft" href="{{ route('admin.customers.show', $customer) }}">التفاصيل</a></td>
          </tr>
        @empty
          <tr><td colspan="4">لا يوجد عملاء</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $customers->links() }}</div>
  </section>
@endsection

