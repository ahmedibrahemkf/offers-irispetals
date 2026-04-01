@extends('layouts.auth')
@section('title', 'تغيير كلمة المرور')
@section('content')
  <h1>تغيير كلمة المرور</h1>
  <p>أدخل كلمة المرور الجديدة.</p>
  <form method="post" action="{{ route('admin.password.update') }}">
    @csrf
    <div class="field">
      <label>كلمة المرور الجديدة</label>
      <input class="input" type="password" name="password" required>
    </div>
    <div class="field">
      <label>تأكيد كلمة المرور</label>
      <input class="input" type="password" name="password_confirmation" required>
    </div>
    <button class="btn" type="submit">حفظ</button>
  </form>
@endsection

