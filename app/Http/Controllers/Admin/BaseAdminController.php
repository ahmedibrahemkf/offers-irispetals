<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

abstract class BaseAdminController extends Controller
{
    protected function user(Request $request)
    {
        return $request->attributes->get('authUser');
    }

    protected function sharedData(Request $request): array
    {
        $user = $this->user($request);

        return [
            'authUser' => $user,
            'shopSettings' => Cache::remember('crm.settings.first', 3600, static fn () => Setting::query()->first()),
            'unreadNotificationsCount' => $user
                ? Notification::query()->where('user_id', $user->id)->where('is_read', false)->count()
                : 0,
        ];
    }
}
