<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todaySales = (float) Invoice::query()
            ->whereDate('issued_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $yesterdaySales = (float) Invoice::query()
            ->whereDate('issued_at', $yesterday)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $salesGrowth = $yesterdaySales > 0
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 0;

        $pendingStatuses = ['confirmed', 'in_progress', 'ready', 'out_for_delivery'];
        $stats = [
            'today_sales' => $todaySales,
            'sales_growth' => $salesGrowth,
            'new_orders' => Order::query()->where('status', 'new')->count(),
            'pending_orders' => Order::query()->whereIn('status', $pendingStatuses)->count(),
            'receivables' => (float) Order::query()->whereIn('payment_status', ['unpaid', 'partial'])->sum('amount_remaining'),
            'pending_collections_count' => Invoice::query()->where('remaining_amount', '>', 0)->count(),
            'late_orders' => Order::query()
                ->whereDate('delivery_date', '<', $today)
                ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                ->count(),
            'low_stock' => Product::query()
                ->whereRaw('stock_quantity <= min_stock_alert')
                ->where('min_stock_alert', '>', 0)
                ->count(),
        ];

        return view('admin.dashboard.index', $this->sharedData($request) + [
            'stats' => $stats,
            'recentOrders' => Order::query()
                ->select(['id', 'order_number', 'customer_name_snapshot', 'status', 'amount_total', 'created_at'])
                ->orderByDesc('id')
                ->limit(8)
                ->get(),
            'lowStockProducts' => Product::query()
                ->whereRaw('stock_quantity <= min_stock_alert')
                ->where('min_stock_alert', '>', 0)
                ->orderBy('stock_quantity')
                ->limit(8)
                ->get(['id', 'name', 'stock_quantity', 'min_stock_alert']),
        ]);
    }
}
