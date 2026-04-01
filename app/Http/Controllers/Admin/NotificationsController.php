<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationsController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $user = $this->user($request);
        $state = (string) $request->query('state', 'all');

        $query = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id');

        if ($state === 'unread') {
            $query->where('is_read', false);
        } elseif ($state === 'read') {
            $query->where('is_read', true);
        }

        return view('admin.notifications.index', $this->sharedData($request) + [
            'notifications' => $query->paginate(30)->withQueryString(),
            'state' => $state,
            'totalCount' => Notification::query()->where('user_id', $user->id)->count(),
            'unreadCount' => Notification::query()->where('user_id', $user->id)->where('is_read', false)->count(),
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = $this->user($request);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('status', 'تم تحديد كل الإشعارات كمقروءة');
    }
}

