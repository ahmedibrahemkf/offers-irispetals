<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

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
            'shopSettings' => $this->resolveShopSettings(),
            'unreadNotificationsCount' => $user
                ? Notification::query()->where('user_id', $user->id)->where('is_read', false)->count()
                : 0,
        ];
    }

    protected function resolveShopSettings(): object
    {
        $fallback = (object) [
            'shop_name' => 'Iris Petals',
            'logo_url' => null,
            'invoice_logo_url' => null,
            'currency_symbol' => 'ج',
        ];

        try {
            $cacheKey = 'crm.settings.first.v2';
            $cached = Cache::remember($cacheKey, 3600, static function (): array {
                $setting = Setting::query()->first();

                return $setting ? $setting->getAttributes() : [];
            });

            if (! is_array($cached)) {
                Cache::forget($cacheKey);

                return $fallback;
            }

            return (object) array_merge((array) $fallback, $cached);
        } catch (Throwable) {
            return $fallback;
        }
    }
}
