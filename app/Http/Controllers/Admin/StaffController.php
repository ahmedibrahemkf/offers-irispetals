<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends BaseAdminController
{
    public function orders(Request $request): View
    {
        $user = $this->user($request);
        $query = Order::query()
            ->where('assigned_staff_id', $user->id)
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q): void {
                $sub->where('order_number', 'like', '%'.$q.'%')
                    ->orWhere('customer_name_snapshot', 'like', '%'.$q.'%')
                    ->orWhere('customer_phone_snapshot', 'like', '%'.$q.'%');
            });
        }

        return view('admin.staff.orders', $this->sharedData($request) + [
            'orders' => $query->paginate(30)->withQueryString(),
        ]);
    }
}
