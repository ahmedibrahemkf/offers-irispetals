<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Setting;
use App\Support\SystemLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoicesController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $query = Invoice::query()->orderByDesc('id');

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where('invoice_number', 'like', "%{$q}%");
        }

        return view('admin.invoices.index', $this->sharedData($request) + [
            'invoices' => $query->paginate(20)->withQueryString(),
            'ordersWithoutInvoice' => Order::query()
                ->whereDoesntHave('invoice')
                ->orderByDesc('id')
                ->limit(20)
                ->get(),
        ]);
    }

    public function createFromOrder(Request $request, Order $order): RedirectResponse
    {
        $user = $this->user($request);

        if (Invoice::query()->where('order_id', $order->id)->exists()) {
            return back()->withErrors(['invoice' => 'الطلب لديه فاتورة بالفعل']);
        }

        $invoice = DB::transaction(function () use ($order, $user) {
            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'type' => 'order',
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'customer_name_snapshot' => $order->customer_name_snapshot,
                'payment_status' => $order->payment_status,
                'sub_total' => (float) $order->amount_total - (float) $order->delivery_fee,
                'discount_amount' => (float) $order->discount_amount,
                'tax_amount' => 0,
                'delivery_fee' => (float) $order->delivery_fee,
                'total_amount' => (float) $order->amount_total,
                'paid_amount' => (float) $order->amount_paid,
                'remaining_amount' => max(0, (float) $order->amount_total - (float) $order->amount_paid),
                'issued_at' => now(),
                'created_by' => $user->id,
            ]);

            foreach ($order->items as $item) {
                InvoiceItem::query()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ]);
            }

            if ((float) $invoice->paid_amount > 0) {
                InvoicePayment::query()->create([
                    'invoice_id' => $invoice->id,
                    'amount' => (float) $invoice->paid_amount,
                    'method' => 'cash',
                    'note' => 'دفعة مرحّلة من الطلب',
                    'created_by' => $user->id,
                    'paid_at' => now(),
                ]);
            }

            return $invoice;
        });

        SystemLogger::log((int) $user->id, 'invoice_created', 'Created invoice from order '.$order->order_number, 'Invoice', (int) $invoice->id, $request);

        return redirect()->route('admin.invoices.show', $invoice)->with('status', 'تم إنشاء الفاتورة');
    }

    public function storeDirectSale(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:120',
            'item_name' => 'required|string|max:180',
            'quantity' => 'required|integer|min:1|max:999',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $user = $this->user($request);

        $invoice = DB::transaction(function () use ($validated, $user) {
            $quantity = (int) $validated['quantity'];
            $unitPrice = (float) $validated['unit_price'];
            $total = $quantity * $unitPrice;
            $paid = min($total, (float) ($validated['paid_amount'] ?? $total));
            $remaining = max(0, $total - $paid);

            $invoice = Invoice::query()->create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'type' => 'direct',
                'customer_name_snapshot' => (string) ($validated['customer_name'] ?: 'عميل مباشر'),
                'payment_status' => $remaining <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'sub_total' => $total,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'issued_at' => now(),
                'created_by' => $user->id,
            ]);

            InvoiceItem::query()->create([
                'invoice_id' => $invoice->id,
                'item_name' => (string) $validated['item_name'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $total,
            ]);

            if ($paid > 0) {
                InvoicePayment::query()->create([
                    'invoice_id' => $invoice->id,
                    'amount' => $paid,
                    'method' => 'cash',
                    'note' => 'دفعة عند إنشاء بيع مباشر',
                    'created_by' => $user->id,
                    'paid_at' => now(),
                ]);
            }

            return $invoice;
        });

        SystemLogger::log((int) $user->id, 'invoice_created', 'Created direct invoice '.$invoice->invoice_number, 'Invoice', (int) $invoice->id, $request);

        return redirect()->route('admin.invoices.show', $invoice)->with('status', 'تم إنشاء البيع المباشر');
    }

    public function show(Request $request, Invoice $invoice): View
    {
        return view('admin.invoices.show', $this->sharedData($request) + [
            'invoice' => $invoice,
            'items' => InvoiceItem::query()->where('invoice_id', $invoice->id)->get(),
            'payments' => InvoicePayment::query()->where('invoice_id', $invoice->id)->orderByDesc('id')->get(),
        ]);
    }

    public function print(Request $request, Invoice $invoice): View
    {
        return view('admin.invoices.print', $this->sharedData($request) + [
            'invoice' => $invoice,
            'items' => InvoiceItem::query()->where('invoice_id', $invoice->id)->get(),
            'payments' => InvoicePayment::query()->where('invoice_id', $invoice->id)->orderBy('id')->get(),
            'settings' => Setting::query()->first(),
        ]);
    }

    public function addPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        $user = $this->user($request);
        $amount = (float) $validated['amount'];
        $maxAllowed = max(0, (float) $invoice->remaining_amount);

        if ($amount > $maxAllowed) {
            return back()->withErrors(['amount' => 'قيمة الدفعة أكبر من المتبقي']);
        }

        DB::transaction(function () use ($invoice, $amount, $validated, $user): void {
            InvoicePayment::query()->create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => (string) ($validated['method'] ?? 'cash'),
                'note' => (string) ($validated['note'] ?? ''),
                'created_by' => $user->id,
                'paid_at' => now(),
            ]);

            $invoice->paid_amount = (float) $invoice->paid_amount + $amount;
            $invoice->remaining_amount = max(0, (float) $invoice->total_amount - (float) $invoice->paid_amount);
            $invoice->payment_status = $invoice->remaining_amount <= 0 ? 'paid' : 'partial';
            $invoice->save();

            if ($invoice->order_id) {
                $order = Order::query()->find($invoice->order_id);
                if ($order) {
                    $order->amount_paid = (float) $invoice->paid_amount;
                    $order->amount_remaining = (float) $invoice->remaining_amount;
                    $order->payment_status = $invoice->payment_status;
                    $order->save();
                }
            }
        });

        SystemLogger::log((int) $user->id, 'invoice_paid', 'Added payment to '.$invoice->invoice_number, 'Invoice', (int) $invoice->id, $request);

        return back()->with('status', 'تم تسجيل الدفعة');
    }

    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $last = Invoice::query()
            ->whereYear('created_at', (int) $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($last && preg_match('/(\d{5})$/', (string) $last->invoice_number, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'INV-'.$year.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}

