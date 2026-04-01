<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EntryController extends BaseAdminController
{
    public function adminRoot(Request $request): RedirectResponse
    {
        $user = $this->user($request);

        $target = match ($user?->role) {
            'staff' => 'staff.orders',
            'craftsman' => 'craftsman.tasks',
            'viewer' => 'admin.reports.index',
            default => 'admin.dashboard.home',
        };

        return redirect()->route($target);
    }

    public function legacyDashboard(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }

    public function legacyOrders(Request $request): RedirectResponse
    {
        $user = $this->user($request);
        $target = $user?->role === 'staff' ? 'staff.orders' : 'admin.orders.index';

        return redirect()->route($target);
    }

    public function legacyReports(): RedirectResponse
    {
        return redirect()->route('admin.reports.index');
    }
}

