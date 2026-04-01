@extends('layouts.auth')
@section('title', 'تسجيل الدخول')
@section('content')
  <h1>تسجيل الدخول</h1>
  <p>أدخل اسم المستخدم أو رقم الهاتف للدخول إلى النظام.</p>
  <form method="post" action="{{ route('admin.login.submit') }}">
    @csrf
    <div class="field">
      <label>اسم المستخدم أو الهاتف</label>
      <input class="input" name="identity" value="{{ old('identity') }}" required>
    </div>
    <div class="field">
      <label>كلمة المرور</label>
      <input class="input" type="password" name="password" required>
    </div>
    <div class="field muted">
      <label><input type="checkbox" name="remember" value="1"> تذكرني 30 يوم</label>
    </div>
    <button class="btn" type="submit">دخول</button>
  </form>
  <p class="muted" style="margin-top:14px">
    <a href="{{ route('admin.password.forgot') }}">نسيت كلمة المرور؟</a>
  </p>
@endsection

