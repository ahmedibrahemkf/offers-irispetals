@extends('layouts.admin')
@section('title', 'التقارير')
@section('page_title', 'التقارير والمالية')
@section('content')
  <section class="card" style="margin-bottom:12px">
    <form method="get" class="grid grid-3">
      <div><label>من</label><input class="input" type="date" name="from" value="{{ $from }}"></div>
      <div><label>إلى</label><input class="input" type="date" name="to" value="{{ $to }}"></div>
      <div class="actions"><button class="btn btn-primary" type="submit">تحديث التقرير</button></div>
    </form>
  </section>

  @if(($metrics['pending_collections_count'] ?? 0) > 0)
    <section class="card" style="margin-bottom:12px;border-color:#f59e0b;background:#fffbeb">
      <b>تنبيه مالي:</b>
      يوجد {{ $metrics['pending_collections_count'] }} فاتورة بها مبالغ متبقية لم تُحصَّل بالكامل.
    </section>
  @endif

  <section class="grid grid-4">
    <article class="card"><div class="muted">إجمالي المبيعات (فواتير)</div><h3>{{ number_format((float) $metrics['sales_booked'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">المتحصل فعليًا</div><h3>{{ number_format((float) $metrics['collected'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">المتبقي للتحصيل</div><h3>{{ number_format((float) $metrics['receivables'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">تكلفة البضاعة التقديرية</div><h3>{{ number_format((float) $metrics['cost'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">الربح الإجمالي (قبل المصروفات)</div><h3>{{ number_format((float) $metrics['gross'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">المصروفات</div><h3>{{ number_format((float) $metrics['expenses'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">صافي الربح (مبني على التحصيل الفعلي)</div><h3>{{ number_format((float) $metrics['net'], 2) }} ج</h3></article>
    <article class="card"><div class="muted">عدد الطلبات</div><h3>{{ $metrics['orders_count'] }}</h3></article>
  </section>

  <section class="card" style="margin-top:12px">
    <h3>ملخص P&L</h3>
    <p><b>المبيعات:</b> {{ number_format((float) $metrics['sales_booked'], 2) }} ج</p>
    <p><b>المتحصل:</b> {{ number_format((float) $metrics['collected'], 2) }} ج</p>
    <p><b>المتبقي للتحصيل:</b> {{ number_format((float) $metrics['receivables'], 2) }} ج</p>
    <p><b>تكلفة البضاعة:</b> {{ number_format((float) $metrics['cost'], 2) }} ج</p>
    <p><b>المصروفات:</b> {{ number_format((float) $metrics['expenses'], 2) }} ج</p>
    <p><b>صافي الربح (التحصيل - التكلفة - المصروفات):</b> {{ number_format((float) $metrics['net'], 2) }} ج</p>
  </section>

  <section class="grid grid-2" style="margin-top:12px">
    <article class="card table-wrap">
      <h3>ملخص حالات الطلبات</h3>
      <table>
        <thead><tr><th>الحالة</th><th>عدد الطلبات</th></tr></thead>
        <tbody>
          @forelse($statusSummary as $status => $count)
            <tr><td>{{ $status }}</td><td>{{ $count }}</td></tr>
          @empty
            <tr><td colspan="2">لا توجد بيانات</td></tr>
          @endforelse
        </tbody>
      </table>
    </article>

    <article class="card table-wrap">
      <h3>مصادر الطلبات</h3>
      <table>
        <thead><tr><th>المصدر</th><th>عدد الطلبات</th></tr></thead>
        <tbody>
          @forelse($sourceSummary as $source => $count)
            <tr><td>{{ $source }}</td><td>{{ $count }}</td></tr>
          @empty
            <tr><td colspan="2">لا توجد بيانات</td></tr>
          @endforelse
        </tbody>
      </table>
    </article>
  </section>
@endsection

