@extends('layouts.auth')
@section('title', 'رمز التحقق')
@section('content')
  <h1>رمز التحقق</h1>
  <p>أدخل الرمز المرسل. صالح لمدة 10 دقائق.</p>
  @if(session('otp_hint'))
    <div class="ok">{{ session('otp_hint') }}</div>
  @endif
  <form method="post" action="{{ route('admin.password.verify-otp') }}">
    @csrf
    <div class="field">
      <label>رمز التحقق</label>
      <input class="input" name="otp" maxlength="6" required>
    </div>
    <button class="btn" type="submit">تحقق</button>
  </form>
@endsection

