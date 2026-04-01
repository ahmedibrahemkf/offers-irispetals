<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SystemLogger
{
    public static function log(
        int $userId,
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?Request $request = null
    ): void {
        ActivityLog::query()->create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => $request?->ip(),
        ]);
    }
}
