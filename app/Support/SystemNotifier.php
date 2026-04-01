<?php

namespace App\Support;

use App\Models\Notification;

class SystemNotifier
{
    public static function notify(int $userId, string $type, string $title, string $body, ?string $link = null): void
    {
        Notification::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'link' => $link,
            'is_read' => false,
        ]);
    }
}
