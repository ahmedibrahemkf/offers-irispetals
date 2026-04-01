<?php

namespace App\Http\Controllers\Admin;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportsController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $from = $request->date('from') ?: now()->startOfMonth();
        $to = $request->date('to') ?: now()->endOfMonth();
        $fromDateTime = $from->copy()->startOfDay();
        $toDateTime = $to->copy()->endOfDay();

        $invoicesQuery = Invoice::query()->whereBetween('issued_at', [$fromDateTime, $toDateTime]);
        $totalSales = (float) (clone $invoicesQuery)->sum('total_amount');
        $collected = (float) (clone $invoicesQuery)->sum('paid_amount');
        $receivables = (float) (clone $invoicesQuery)->sum('remaining_amount');

        $estimatedCost = (float) (DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereBetween('invoices.issued_at', [$fromDateTime, $toDateTime])
            ->selectRaw('COALESCE(SUM(invoice_items.quantity * COALESCE(products.cost_price, 0)), 0) as total_cost')
            ->value('total_cost') ?? 0);

        $expenses = (float) Expense::query()
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');

        $grossProfit = $totalSales - $estimatedCost;
        $netProfit = $collected - $estimatedCost - $expenses;

        $statusSummary = Order::query()
            ->whereBetween('created_at', [$fromDateTime, $toDateTime])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $sourceSummary = Order::query()
            ->whereBetween('created_at', [$fromDateTime, $toDateTime])
            ->selectRaw('source, count(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        $pendingCollectionsCount = Invoice::query()
            ->whereBetween('issued_at', [$fromDateTime, $toDateTime])
            ->where('remaining_amount', '>', 0)
            ->count();

        return view('admin.reports.index', $this->sharedData($request) + [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'metrics' => [
                'sales_booked' => $totalSales,
                'collected' => $collected,
                'receivables' => $receivables,
                'cost' => $estimatedCost,
                'gross' => $grossProfit,
                'expenses' => $expenses,
                'net' => $netProfit,
                'orders_count' => Order::query()->whereBetween('created_at', [$fromDateTime, $toDateTime])->count(),
                'low_stock_count' => Product::query()->whereRaw('stock_quantity <= min_stock_alert')->where('min_stock_alert', '>', 0)->count(),
                'pending_collections_count' => $pendingCollectionsCount,
            ],
            'statusSummary' => $statusSummary,
            'sourceSummary' => $sourceSummary,
        ]);
    }
}

