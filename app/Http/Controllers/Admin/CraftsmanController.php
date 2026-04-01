<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Support\SystemLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CraftsmanController extends BaseAdminController
{
    public function tasks(Request $request): View
    {
        $user = $this->user($request);
        $status = (string) $request->string('status', 'all');

        $query = Order::query()
            ->where('assigned_craftsman_id', $user->id)
            ->orderBy('delivery_date')
            ->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        return view('admin.craftsman.tasks', $this->sharedData($request) + [
            'orders' => $query->paginate(30)->withQueryString(),
            'statusFilter' => $status,
        ]);
    }

    public function updateTaskStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,ready',
        ]);

        $user = $this->user($request);

        if ((int) $order->assigned_craftsman_id !== (int) $user->id) {
            abort(403, 'هذا الطلب غير مخصص لك');
        }

        $oldStatus = $order->status;
        $order->status = $validated['status'];
        $order->save();

        OrderStatusLog::query()->create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $order->status,
            'note' => 'تحديث من صفحة الصنايعي',
            'changed_by' => $user->id,
        ]);

        SystemLogger::log((int) $user->id, 'order_status_changed', 'Changed order '.$order->order_number.' status to '.$order->status, 'Order', (int) $order->id, $request);

        return back()->with('status', 'تم تحديث حالة الطلب');
    }
}

