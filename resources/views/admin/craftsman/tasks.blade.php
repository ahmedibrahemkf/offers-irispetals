@extends('layouts.admin')
@section('title', 'مهام الصنايعي')
@section('page_title', 'مهام الصنايعي')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <form class="actions" method="get">
      <select class="select" name="status" style="max-width:220px">
        <option value="all" @selected($statusFilter === 'all')>كل المهام</option>
        @foreach(['confirmed' => 'مؤكد', 'in_progress' => 'قيد التنفيذ', 'ready' => 'جاهز'] as $statusKey => $statusLabel)
          <option value="{{ $statusKey }}" @selected($statusFilter === $statusKey)>{{ $statusLabel }}</option>
        @endforeach
      </select>
      <button class="btn btn-soft" type="submit">تصفية</button>
    </form>
  </section>

  <div class="grid grid-2">
    @forelse($orders as $order)
      <article class="card">
        <h3>{{ $order->order_number }} - {{ $order->status }}</h3>
        <p><b>العميل:</b> {{ $order->customer_name_snapshot }} - {{ $order->customer_phone_snapshot }}</p>
        <p><b>المناسبة:</b> {{ $order->occasion ?: '-' }}</p>
        <p><b>رسالة الكارت:</b> {{ $order->card_message ?: '-' }}</p>
        <p><b>العنوان:</b> {{ $order->delivery_address ?: '-' }}</p>
        <p><b>موعد التسليم:</b> {{ optional($order->delivery_date)->format('Y-m-d') ?: '-' }} {{ $order->delivery_time_slot }}</p>
        <div class="actions">
          @if($order->status === 'confirmed')
            <form method="post" action="{{ route('craftsman.orders.status', $order) }}">@csrf<input type="hidden" name="status" value="in_progress"><button class="btn btn-primary" type="submit">بدأت التنفيذ</button></form>
          @elseif($order->status === 'in_progress')
            <form method="post" action="{{ route('craftsman.orders.status', $order) }}">@csrf<input type="hidden" name="status" value="ready"><button class="btn btn-primary" type="submit">الطلب جاهز</button></form>
          @endif
          <a class="btn btn-soft" target="_blank" href="https://maps.google.com/?q={{ urlencode($order->delivery_address ?? '') }}">خرائط</a>
          <a class="btn btn-soft" href="tel:{{ $order->customer_phone_snapshot }}">اتصال</a>
        </div>
      </article>
    @empty
      <article class="card">لا يوجد مهام حاليًا</article>
    @endforelse
  </div>
  <div style="margin-top:10px">{{ $orders->links() }}</div>
@endsection

