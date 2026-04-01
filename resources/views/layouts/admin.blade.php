<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'لوحة الإدارة') - {{ $shopSettings->shop_name ?? 'Iris Petals' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#6D28D9;--primary-light:#EDE9FE;--success:#059669;--warning:#D97706;--danger:#DC2626;
      --info:#2563EB;--dark:#1E1B4B;--body:#4B5563;--muted:#9CA3AF;--bg:#F9FAFB;--white:#FFF;--border:#E5E7EB;
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:"Cairo",system-ui,sans-serif;background:var(--bg);color:var(--body)}
    a{text-decoration:none;color:inherit}
    .app{display:grid;grid-template-columns:280px 1fr;min-height:100vh}
    .sidebar{background:#fff;border-inline-start:1px solid var(--border);padding:16px;position:sticky;top:0;height:100vh;overflow:auto}
    .brand{display:flex;align-items:center;gap:12px;padding-bottom:14px;border-bottom:1px solid var(--border);margin-bottom:14px}
    .brand img{inline-size:42px;block-size:42px;border-radius:50%;object-fit:cover;background:#f2eafe}
    .brand h1{margin:0;font-size:18px;color:var(--dark)}
    .nav-group{margin-bottom:12px}
    .nav-group-header{display:flex;justify-content:space-between;padding:10px 12px;color:var(--muted);font-size:12px;font-weight:700}
    .nav-item{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;margin-bottom:4px}
    .nav-item:hover{background:#f7f5ff}
    .nav-item.active{background:var(--primary-light);color:var(--primary);font-weight:700;border-inline-end:3px solid var(--primary)}
    .badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700}
    .badge-new{background:#DBEAFE;color:#2563EB}
    .badge-confirmed{background:#EDE9FE;color:#6D28D9}
    .badge-progress{background:#FEF3C7;color:#D97706}
    .badge-ready{background:#D1FAE5;color:#065F46}
    .badge-delivery{background:#FEF3C7;color:#92400E}
    .badge-done{background:#D1FAE5;color:#059669}
    .badge-cancelled,.badge-returned{background:#FEE2E2;color:#DC2626}
    .content{padding:24px}
    .top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:16px}
    .top h2{margin:0;color:var(--dark);font-size:28px}
    .card{background:#fff;border:1px solid var(--border);border-radius:14px;padding:16px}
    .grid{display:grid;gap:12px}
    .grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}
    .grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
    .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
    .input,.select,textarea{width:100%;padding:11px 12px;border:1px solid var(--border);border-radius:10px;font-family:inherit;font-size:14px}
    textarea{min-height:90px}
    .btn{border:0;border-radius:10px;padding:10px 14px;font-family:inherit;font-weight:700;cursor:pointer}
    .btn-primary{background:var(--primary);color:#fff}
    .btn-soft{background:#fff;border:1px solid var(--primary);color:var(--primary)}
    .btn-danger{background:var(--danger);color:#fff}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .table-wrap{overflow:auto}
    table{width:100%;border-collapse:collapse;font-size:13px}
    th,td{padding:10px;border-bottom:1px solid var(--border);text-align:right;vertical-align:top}
    th{color:var(--muted);font-size:12px}
    .mobile-table-cards{display:none;gap:10px}
    .mobile-table-card{padding:12px}
    .mobile-table-row{display:flex;justify-content:space-between;gap:10px;padding:7px 0;border-bottom:1px dashed var(--border)}
    .mobile-table-row:last-child{border-bottom:0}
    .mobile-table-label{font-size:12px;color:var(--muted);font-weight:700}
    .mobile-table-value{font-size:13px;color:var(--dark);text-align:left;max-width:65%}
    .mobile-table-empty{color:var(--muted);font-size:13px}
    .muted{color:var(--muted)}
    .ok{background:#e8f8ef;color:#047857;padding:10px 12px;border-radius:10px}
    .err{background:#fef2f2;color:#b91c1c;padding:10px 12px;border-radius:10px}
    .mobile-nav{display:none}
    @media (max-width:1024px){.app{grid-template-columns:1fr}.sidebar{display:none}.content{padding:14px}.grid-4,.grid-3{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:640px){
      .grid-4,.grid-3,.grid-2{grid-template-columns:1fr}
      .top{flex-direction:column;align-items:flex-start}
      .mobile-nav{display:grid;grid-template-columns:repeat(5,1fr);position:fixed;inset:auto 0 0 0;background:#fff;border-top:1px solid var(--border);padding:8px;z-index:30}
      .mobile-nav a{font-size:11px;text-align:center;padding:8px;border-radius:10px}
      .mobile-nav a.active{background:var(--primary-light);color:var(--primary)}
      .content{padding-bottom:88px}
      .btn{min-height:44px}
      .table-wrap.mobile-table-source > table{display:none}
      .mobile-table-cards{display:grid}
    }
  </style>
</head>
<body>
@php
  $role = $authUser->role ?? '';
@endphp
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <img src="{{ $shopSettings->logo_url ?? '' }}" alt="logo">
      <div>
        <h1>{{ $shopSettings->shop_name ?? 'Iris Petals' }}</h1>
        <div class="muted">نظام إدارة المبيعات والعمليات</div>
      </div>
    </div>

    <div class="nav-group">
      @if(in_array($role, ['owner', 'manager'], true))
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"><span>الرئيسية</span></a>
      @endif
    </div>

    @if(in_array($role, ['owner', 'manager', 'staff'], true))
      <div class="nav-group">
        <div class="nav-group-header">المبيعات</div>
        <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><span>الطلبات</span></a>
        <a href="{{ route('admin.invoices.index') }}" class="nav-item {{ request()->routeIs('admin.invoices*') ? 'active' : '' }}"><span>الفواتير</span></a>
        <a href="{{ route('admin.customers.index') }}" class="nav-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}"><span>العملاء</span></a>
      </div>
    @endif

    @if(in_array($role, ['owner', 'manager'], true))
      <div class="nav-group">
        <div class="nav-group-header">المخزون والشراء</div>
        <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}"><span>المنتجات</span></a>
        <a href="{{ route('admin.suppliers.index') }}" class="nav-item {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}"><span>الموردون</span></a>
        <a href="{{ route('admin.purchases.index') }}" class="nav-item {{ request()->routeIs('admin.purchases*') ? 'active' : '' }}"><span>المشتريات</span></a>
      </div>
      <div class="nav-group">
        <div class="nav-group-header">المالية والموارد</div>
        <a href="{{ route('admin.expenses.index') }}" class="nav-item {{ request()->routeIs('admin.expenses*') ? 'active' : '' }}"><span>المصروفات</span></a>
        <a href="{{ route('admin.employees.index') }}" class="nav-item {{ request()->routeIs('admin.employees*') ? 'active' : '' }}"><span>الموظفون</span></a>
        <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"><span>التقارير</span></a>
      </div>
    @endif

    @if($role === 'viewer')
      <div class="nav-group">
        <div class="nav-group-header">التقارير</div>
        <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"><span>التقارير</span></a>
      </div>
    @endif

    <div class="nav-group">
      <div class="nav-group-header">النظام</div>
      <a href="{{ route('admin.notifications.index') }}" class="nav-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}"><span>الإشعارات</span><span class="badge badge-new">{{ $unreadNotificationsCount ?? 0 }}</span></a>
      @if(in_array($role, ['owner','manager'], true))
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><span>الإعدادات</span></a>
      @endif
      @if($role === 'owner')
        <a href="{{ route('admin.activity.index') }}" class="nav-item {{ request()->routeIs('admin.activity*') ? 'active' : '' }}"><span>سجل النشاط</span></a>
      @endif
      @if($role === 'staff')
        <a href="{{ route('staff.orders') }}" class="nav-item {{ request()->routeIs('staff.orders') ? 'active' : '' }}"><span>طلبات الاستقبال</span></a>
      @endif
      @if($role === 'craftsman')
        <a href="{{ route('craftsman.tasks') }}" class="nav-item {{ request()->routeIs('craftsman.tasks') ? 'active' : '' }}"><span>مهام الصنايعي</span></a>
      @endif
    </div>
  </aside>

  <main class="content">
    <div class="top">
      <h2>@yield('page_title', 'لوحة الإدارة')</h2>
      <div class="actions">
        <a href="{{ route('public.order.show') }}" target="_blank" class="btn btn-soft">صفحة الطلب العامة</a>
        <form method="post" action="{{ route('admin.logout') }}">
          @csrf
          <button class="btn btn-danger" type="submit">تسجيل الخروج</button>
        </form>
      </div>
    </div>

    @if(session('status'))
      <div class="ok">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="err">
        <ul style="margin:0;padding-inline-start:18px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </main>
</div>

<nav class="mobile-nav">
  @if(in_array($role, ['owner','manager'], true))
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">الرئيسية</a>
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">الطلبات</a>
    <a href="{{ route('admin.invoices.index') }}" class="{{ request()->routeIs('admin.invoices*') ? 'active' : '' }}">الفواتير</a>
    <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">إشعارات</a>
    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">المزيد</a>
  @elseif($role === 'staff')
    <a href="{{ route('staff.orders') }}" class="{{ request()->routeIs('staff.orders') ? 'active' : '' }}">الرئيسية</a>
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">الطلبات</a>
    <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers*') ? 'active' : '' }}">العملاء</a>
    <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">إشعارات</a>
    <a href="{{ route('staff.orders') }}" class="{{ request()->routeIs('staff.orders') ? 'active' : '' }}">المزيد</a>
  @elseif($role === 'craftsman')
    <a href="{{ route('craftsman.tasks') }}" class="{{ request()->routeIs('craftsman.tasks') ? 'active' : '' }}">الرئيسية</a>
    <a href="{{ route('craftsman.tasks') }}" class="{{ request()->routeIs('craftsman.tasks') ? 'active' : '' }}">المهام</a>
    <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">إشعارات</a>
    <a href="{{ route('public.order.show') }}" target="_blank">الطلب العام</a>
    <a href="{{ route('craftsman.tasks') }}" class="{{ request()->routeIs('craftsman.tasks') ? 'active' : '' }}">المزيد</a>
  @else
    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">الرئيسية</a>
    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">التقارير</a>
    <a href="{{ route('admin.notifications.index') }}" class="{{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">إشعارات</a>
    <a href="{{ route('public.order.show') }}" target="_blank">الطلب العام</a>
    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">المزيد</a>
  @endif
</nav>

<script>
  (function () {
    function buildMobileCards() {
      document.querySelectorAll('.table-wrap').forEach(function (wrap) {
        if (wrap.dataset.mobileBuilt === '1' || wrap.classList.contains('no-auto-mobile')) {
          return;
        }

        var table = wrap.querySelector('table');
        if (!table) {
          return;
        }

        var headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
          return (th.textContent || '').trim();
        });
        var bodyRows = Array.from(table.querySelectorAll('tbody tr'));
        var cards = document.createElement('div');
        cards.className = 'mobile-table-cards';

        bodyRows.forEach(function (row) {
          var cells = Array.from(row.children).filter(function (cell) {
            return cell.tagName.toLowerCase() === 'td';
          });

          if (!cells.length) {
            return;
          }

          if (cells.length === 1 && cells[0].hasAttribute('colspan')) {
            var emptyCard = document.createElement('article');
            emptyCard.className = 'mobile-table-card card';
            emptyCard.innerHTML = '<div class="mobile-table-empty">' + ((cells[0].textContent || '').trim()) + '</div>';
            cards.appendChild(emptyCard);
            return;
          }

          var card = document.createElement('article');
          card.className = 'mobile-table-card card';

          cells.forEach(function (cell, index) {
            var line = document.createElement('div');
            line.className = 'mobile-table-row';

            var label = document.createElement('div');
            label.className = 'mobile-table-label';
            label.textContent = headers[index] || ('حقل ' + (index + 1));

            var value = document.createElement('div');
            value.className = 'mobile-table-value';
            value.innerHTML = cell.innerHTML;

            line.appendChild(label);
            line.appendChild(value);
            card.appendChild(line);
          });

          cards.appendChild(card);
        });

        wrap.classList.add('mobile-table-source');
        wrap.insertAdjacentElement('afterend', cards);
        wrap.dataset.mobileBuilt = '1';
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', buildMobileCards);
    } else {
      buildMobileCards();
    }
  })();
</script>
</body>
</html>
