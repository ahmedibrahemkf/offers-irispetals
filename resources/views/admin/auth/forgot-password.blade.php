@extends('layouts.auth')
@section('title', 'استعادة كلمة المرور')
@section('content')
  <h1>استعادة كلمة المرور</h1>
  <p>أدخل اسم المستخدم أو الهاتف أو البريد لإرسال رمز تحقق.</p>
  <form method="post" action="{{ route('admin.password.send-otp') }}">
    @csrf
    <div class="field">
      <label>البيان</label>
      <input class="input" name="identity" value="{{ old('identity') }}" required>
    </div>
    <button class="btn" type="submit">إرسال الرمز</button>
  </form>
@endsection

