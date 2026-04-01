<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogsController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $query = ActivityLog::query()
            ->with('user')
            ->orderByDesc('id');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%'.$request->string('action').'%');
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($userQuery) use ($request): void {
                $keyword = (string) $request->string('user');
                $userQuery
                    ->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('username', 'like', '%'.$keyword.'%');
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->string('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->string('to'));
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.activity.index', $this->sharedData($request) + [
            'logs' => $logs,
            'totalLogs' => ActivityLog::query()->count(),
            'todayLogs' => ActivityLog::query()->whereDate('created_at', today())->count(),
        ]);
    }
}

