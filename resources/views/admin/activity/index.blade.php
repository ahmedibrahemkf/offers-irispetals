@extends('layouts.admin')
@section('title', 'سجل النشاط')
@section('page_title', 'سجل النشاط')

@section('content')
  <section class="grid grid-3" style="margin-bottom:12px">
    <article class="card">
      <div class="muted">إجمالي السجلات</div>
      <h3>{{ number_format((int) $totalLogs) }}</h3>
    </article>
    <article class="card">
      <div class="muted">سجلات اليوم</div>
      <h3>{{ number_format((int) $todayLogs) }}</h3>
    </article>
    <article class="card">
      <div class="muted">نتيجة البحث الحالية</div>
      <h3>{{ number_format((int) $logs->total()) }}</h3>
    </article>
  </section>

  <section class="card" style="margin-bottom:12px">
    <form class="grid grid-4" method="get">
      <input class="input" name="action" value="{{ request('action') }}" placeholder="اسم الإجراء">
      <input class="input" name="user" value="{{ request('user') }}" placeholder="المستخدم">
      <input class="input" type="date" name="from" value="{{ request('from') }}">
      <input class="input" type="date" name="to" value="{{ request('to') }}">
      <div class="actions">
        <button class="btn btn-primary" type="submit">تطبيق</button>
        <a class="btn btn-soft" href="{{ route('admin.activity.index') }}">إعادة تعيين</a>
      </div>
    </form>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>المستخدم</th>
          <th>الإجراء</th>
          <th>الوصف</th>
          <th>النموذج</th>
          <th>IP</th>
          <th>الوقت</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ $log->user?->name ?? '—' }}</td>
            <td>{{ $log->action }}</td>
            <td>{{ $log->description }}</td>
            <td>
              @if($log->model_type)
                {{ $log->model_type }} #{{ $log->model_id }}
              @else
                —
              @endif
            </td>
            <td>{{ $log->ip_address ?? '—' }}</td>
            <td>{{ optional($log->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
        @empty
          <tr><td colspan="6">لا يوجد نشاط مسجل</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $logs->links() }}</div>
  </section>
@endsection

