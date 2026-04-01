@extends('layouts.admin')
@section('title', 'المصروفات')
@section('page_title', 'المصروفات')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <h3>إضافة مصروف</h3>
    <form method="post" class="grid grid-3" action="{{ route('admin.expenses.store') }}">
      @csrf
      <input class="input" name="title" placeholder="عنوان المصروف" required>
      <select class="select" name="expense_category_id">
        <option value="">فئة</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
      <input class="input" type="number" step="0.01" min="0" name="amount" placeholder="القيمة" required>
      <input class="input" type="date" name="expense_date">
      <input class="input" name="note" placeholder="ملاحظة">
      <button class="btn btn-primary" type="submit">حفظ</button>
    </form>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>العنوان</th>
          <th>الفئة</th>
          <th>القيمة</th>
          <th>التاريخ</th>
          <th>ملاحظة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($expenses as $expense)
          <tr>
            <td>{{ $expense->title }}</td>
            <td>{{ $expense->category?->name }}</td>
            <td>{{ number_format((float) $expense->amount, 2) }} ج</td>
            <td>{{ $expense->expense_date }}</td>
            <td>{{ $expense->note ?: '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="5">لا يوجد مصروفات</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $expenses->links() }}</div>
  </section>
@endsection

