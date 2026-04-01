@extends('layouts.admin')
@section('title', 'لوحة التحكم')
@section('page_title', 'لوحة التحكم')
@section('content')
  @php
    $statusLabels = [
      'new' => 'جديد',
      'confirmed' => 'مؤكد',
      'in_progress' => 'قيد التنفيذ',
      'ready' => 'جاهز',
      'out_for_delivery' => 'خارج للتوصيل',
      'delivered' => 'تم التسليم',
      'cancelled' => 'ملغي',
      'returned' => 'مرتجع',
    ];
  @endphp

  <section class="card" style="margin-bottom:12px">
    <div class="actions">
      <a class="btn btn-primary" href="{{ route('admin.orders.create') }}">إضافة طلب جديد</a>
      <a class="btn btn-soft" href="{{ route('admin.invoices.index') }}">الفواتير</a>
      <a class="btn btn-soft" href="{{ route('admin.reports.index') }}">التقارير</a>
      <a class="btn btn-soft" href="{{ route('admin.notifications.index') }}">الإشعارات</a>
    </div>
  </section>

  @if(($stats['pending_collections_count'] ?? 0) > 0)
    <section class="card" style="margin-bottom:12px;border-color:#f59e0b;background:#fffbeb">
      <b>تنبيه:</b>
      يوجد {{ $stats['pending_collections_count'] }} فاتورة بها مبالغ متبقية للتحصيل.
    </section>
  @endif

  <section class="grid grid-4">
    <article class="card"><div class="muted">مبيعات اليوم (مدفوعة)</div><h3>{{ number_format((float) $stats['today_sales'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">نمو المبيعات</div><h3>{{ $stats['sales_growth'] }}%</h3></article>
    <article class="card"><div class="muted">طلبات جديدة</div><h3>{{ $stats['new_orders'] }}</h3></article>
    <article class="card"><div class="muted">طلبات معلقة</div><h3>{{ $stats['pending_orders'] }}</h3></article>
    <article class="card"><div class="muted">مبالغ مستحقة</div><h3>{{ number_format((float) $stats['receivables'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">طلبات متأخرة</div><h3>{{ $stats['late_orders'] }}</h3></article>
    <article class="card"><div class="muted">منتجات منخفضة المخزون</div><h3>{{ $stats['low_stock'] }}</h3></article>
  </section>

  <section class="grid grid-2" style="margin-top:12px">
    <article class="card table-wrap">
      <h3>آخر الطلبات</h3>
      <table>
        <thead>
          <tr>
            <th>رقم الطلب</th>
            <th>العميل</th>
            <th>الحالة</th>
            <th>الإجمالي</th>
            <th>الوقت</th>
            <th>إجراء</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentOrders as $order)
            <tr>
              <td>{{ $order->order_number }}</td>
              <td>{{ $order->customer_name_snapshot }}</td>
              <td>{{ $statusLabels[$order->status] ?? $order->status }}</td>
              <td>{{ number_format((float) $order->amount_total, 2) }} ج</td>
              <td>{{ optional($order->created_at)->format('Y-m-d H:i') }}</td>
              <td><a class="btn btn-soft" href="{{ route('admin.orders.show', $order->id) }}">فتح</a></td>
            </tr>
          @empty
            <tr><td colspan="6">لا توجد طلبات</td></tr>
          @endforelse
        </tbody>
      </table>
    </article>

    <article class="card table-wrap">
      <h3>تنبيهات المخزون</h3>
      <table>
        <thead>
          <tr>
            <th>المنتج</th>
            <th>المتوفر</th>
            <th>حد التنبيه</th>
            <th>إجراء</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lowStockProducts as $product)
            <tr>
              <td>{{ $product->name }}</td>
              <td>{{ $product->stock_quantity }}</td>
              <td>{{ $product->min_stock_alert }}</td>
              <td><a class="btn btn-soft" href="{{ route('admin.products.show', $product->id) }}">فتح</a></td>
            </tr>
          @empty
            <tr><td colspan="4">لا يوجد منتجات أسفل حد التنبيه</td></tr>
          @endforelse
        </tbody>
      </table>
    </article>
  </section>
@endsection

