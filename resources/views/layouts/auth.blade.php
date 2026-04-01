<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'تسجيل الدخول')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    body{margin:0;font-family:"Cairo",system-ui,sans-serif;background:#f8f6ff;color:#1E1B4B;display:grid;place-items:center;min-height:100vh;padding:16px}
    .box{inline-size:min(480px,100%);background:#fff;border:1px solid #e6dffe;border-radius:16px;padding:24px}
    h1{margin:0 0 8px;font-size:28px}
    p{margin:0 0 16px;color:#6b7280}
    .field{margin-bottom:12px}
    .input{width:100%;padding:12px;border:1px solid #e5e7eb;border-radius:10px;font-family:inherit}
    .btn{width:100%;border:0;background:#6D28D9;color:#fff;padding:12px;border-radius:10px;font-weight:700;cursor:pointer}
    .muted{color:#6b7280;font-size:13px}
    .ok{background:#e8f8ef;color:#047857;padding:10px 12px;border-radius:10px;margin-bottom:12px}
    .err{background:#fef2f2;color:#b91c1c;padding:10px 12px;border-radius:10px;margin-bottom:12px}
    a{color:#6D28D9;text-decoration:none}
  </style>
</head>
<body>
<div class="box">
  @if(session('status'))<div class="ok">{{ session('status') }}</div>@endif
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
  @yield('content')
</div>
</body>
</html>

