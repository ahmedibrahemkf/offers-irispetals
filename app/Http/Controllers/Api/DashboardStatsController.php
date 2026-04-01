<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardStatsController extends Controller
{
    public function __invoke(): JsonResponse
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

        return response()->json([
            'today_sales' => $todaySales,
            'sales_growth' => $salesGrowth,
            'new_orders' => Order::query()->where('status', 'new')->count(),
            'pending_orders' => Order::query()->whereIn('status', ['confirmed', 'in_progress', 'ready', 'out_for_delivery'])->count(),
            'receivables' => (float) Order::query()->whereIn('payment_status', ['unpaid', 'partial'])->sum('amount_remaining'),
            'late_orders' => Order::query()
                ->whereDate('delivery_date', '<', $today)
                ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                ->count(),
            'low_stock' => Product::query()->whereRaw('stock_quantity <= min_stock_alert AND min_stock_alert > 0')->count(),
        ]);
    }
}
