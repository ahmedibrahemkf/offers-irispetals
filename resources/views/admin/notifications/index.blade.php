@extends('layouts.admin')
@section('title', 'الإشعارات')
@section('page_title', 'الإشعارات')

@section('content')
  <section class="grid grid-3" style="margin-bottom:12px">
    <article class="card">
      <div class="muted">إجمالي الإشعارات</div>
      <h3>{{ number_format((int) $totalCount) }}</h3>
    </article>
    <article class="card">
      <div class="muted">غير المقروء</div>
      <h3>{{ number_format((int) $unreadCount) }}</h3>
    </article>
    <article class="card">
      <div class="muted">حالة العرض الحالية</div>
      <h3>
        @if(($state ?? 'all') === 'unread')
          غير المقروءة فقط
        @elseif(($state ?? 'all') === 'read')
          المقروءة فقط
        @else
          الكل
        @endif
      </h3>
    </article>
  </section>

  <section class="card" style="margin-bottom:12px">
    <div class="actions">
      <a class="btn {{ ($state ?? 'all') === 'all' ? 'btn-primary' : 'btn-soft' }}" href="{{ route('admin.notifications.index') }}">الكل</a>
      <a class="btn {{ ($state ?? 'all') === 'unread' ? 'btn-primary' : 'btn-soft' }}" href="{{ route('admin.notifications.index', ['state' => 'unread']) }}">غير المقروءة</a>
      <a class="btn {{ ($state ?? 'all') === 'read' ? 'btn-primary' : 'btn-soft' }}" href="{{ route('admin.notifications.index', ['state' => 'read']) }}">المقروءة</a>

      <form method="post" action="{{ route('admin.notifications.mark-all-read') }}">
        @csrf
        <button class="btn btn-soft" type="submit" @disabled(($unreadCount ?? 0) === 0)>تحديد الكل كمقروء</button>
      </form>
    </div>
  </section>

  <section class="card table-wrap">
    <table>
      <thead>
        <tr>
          <th>النوع</th>
          <th>العنوان</th>
          <th>الوصف</th>
          <th>الحالة</th>
          <th>الوقت</th>
          <th>الرابط</th>
        </tr>
      </thead>
      <tbody>
        @forelse($notifications as $notification)
          <tr>
            <td>{{ $notification->type }}</td>
            <td>{{ $notification->title }}</td>
            <td>{{ $notification->body }}</td>
            <td>
              @if($notification->is_read)
                <span class="badge badge-done">مقروءة</span>
              @else
                <span class="badge badge-new">جديدة</span>
              @endif
            </td>
            <td>{{ optional($notification->created_at)->format('Y-m-d H:i') }}</td>
            <td>
              @if($notification->link)
                <a class="btn btn-soft" href="{{ $notification->link }}">فتح</a>
              @else
                <span class="muted">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6">لا توجد إشعارات</td></tr>
        @endforelse
      </tbody>
    </table>
    <div style="margin-top:10px">{{ $notifications->links() }}</div>
  </section>
@endsection

