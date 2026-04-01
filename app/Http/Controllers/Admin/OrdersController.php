<?php

namespace App\Http\Controllers\Admin;

use App\Models\Collector;
use App\Models\Color;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderCollection;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\User;
use App\Support\SystemLogger;
use App\Support\SystemNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class OrdersController extends BaseAdminController
{
    private const ORDER_FIELD_LABELS = [
        'customer_name' => 'اسم العميل',
        'customer_phone' => 'هاتف العميل',
        'source' => 'المصدر',
        'assigned_staff_id' => 'الموظف المسؤول',
        'assigned_craftsman_id' => 'الصنايعي',
        'delivery_address' => 'عنوان التوصيل',
        'shipping_zone_id' => 'منطقة التوصيل',
        'delivery_date' => 'تاريخ التوصيل',
        'delivery_time_slot' => 'وقت التوصيل',
        'delivery_fee' => 'رسوم التوصيل',
        'occasion' => 'المناسبة',
        'recipient_name' => 'اسم المستلم',
        'recipient_phone' => 'هاتف المستلم',
        'card_message' => 'رسالة الكارت',
        'notes' => 'ملاحظات العميل',
        'internal_notes' => 'ملاحظات داخلية',
        'product_id' => 'المنتج',
        'color_id' => 'اللون',
        'quantity' => 'الكمية',
        'unit_price' => 'سعر الوحدة',
    ];

    public function index(Request $request): View
    {
        $query = Order::query()->with(['customer'])->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($sub) use ($q): void {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name_snapshot', 'like', "%{$q}%")
                    ->orWhere('customer_phone_snapshot', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', $this->sharedData($request) + [
            'orders' => $orders,
        ]);
    }

    public function create(Request $request): View
    {
        $requiredFields = $this->orderRequiredFields();

        return view('admin.orders.create', $this->sharedData($request) + [
            'products' => Product::query()->orderBy('name')->get(),
            'colors' => Color::query()->orderBy('name')->get(),
            'zones' => ShippingZone::query()->orderBy('name')->get(),
            'staff' => User::query()->whereIn('role', ['owner', 'manager', 'staff'])->where('is_active', true)->get(),
            'craftsmen' => User::query()->where('role', 'craftsman')->where('is_active', true)->get(),
            'requiredFields' => $requiredFields,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $requiredFields = $this->orderRequiredFields();
        $validated = $request->validate(
            $this->orderStoreRules($requiredFields),
            ['required' => 'حقل :attribute مطلوب'],
            self::ORDER_FIELD_LABELS
        );

        $user = $this->user($request);

        $order = DB::transaction(function () use ($validated, $user, $request): Order {
            $customerName = trim((string) ($validated['customer_name'] ?? ''));
            if ($customerName === '') {
                $customerName = 'عميل بدون اسم';
            }

            $customerPhone = trim((string) ($validated['customer_phone'] ?? ''));

            if ($customerPhone !== '') {
                $customer = Customer::query()->firstOrCreate(
                    ['phone' => $customerPhone],
                    [
                        'name' => $customerName,
                        'phone' => $customerPhone,
                        'address' => (string) ($validated['delivery_address'] ?? ''),
                    ]
                );
            } else {
                $customer = Customer::query()->create([
                    'name' => $customerName,
                    'phone' => 'NP'.substr(str_replace('.', '', uniqid('', true)), -10),
                    'address' => (string) ($validated['delivery_address'] ?? ''),
                ]);
            }

            if (blank($customer->name)) {
                $customer->name = $customerName;
                $customer->save();
            }

            $quantity = (int) ($validated['quantity'] ?? 1);
            $unitPrice = (float) ($validated['unit_price'] ?? 0);
            $zoneFee = 0.0;
            if (! empty($validated['shipping_zone_id'])) {
                $zoneFee = (float) (ShippingZone::query()->where('id', (int) $validated['shipping_zone_id'])->value('fee') ?? 0);
            }
            $deliveryFee = (float) ($validated['delivery_fee'] ?? $zoneFee);
            $lineTotal = $quantity * $unitPrice;
            $orderTotal = $lineTotal + $deliveryFee;

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name_snapshot' => $customerName,
                'customer_phone_snapshot' => (string) ($validated['customer_phone'] ?? ''),
                'source' => (string) ($validated['source'] ?? 'walk_in'),
                'assigned_staff_id' => $validated['assigned_staff_id'] ?? null,
                'assigned_craftsman_id' => $validated['assigned_craftsman_id'] ?? null,
                'status' => 'new',
                'payment_status' => 'unpaid',
                'amount_total' => $orderTotal,
                'amount_paid' => 0,
                'amount_remaining' => $orderTotal,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'delivery_time_slot' => $validated['delivery_time_slot'] ?? null,
                'delivery_fee' => $deliveryFee,
                'occasion' => $validated['occasion'] ?? null,
                'recipient_name' => $validated['recipient_name'] ?? null,
                'recipient_phone' => $validated['recipient_phone'] ?? null,
                'card_message' => $validated['card_message'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'created_by' => $user->id,
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $validated['product_id'] ?? null,
                'color_id' => $validated['color_id'] ?? null,
                'item_name' => $this->resolveItemName($validated['product_id'] ?? null),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ]);

            OrderStatusLog::query()->create([
                'order_id' => $order->id,
                'old_status' => null,
                'new_status' => 'new',
                'note' => 'إنشاء الطلب',
                'changed_by' => $user->id,
            ]);

            SystemLogger::log((int) $user->id, 'order_created', 'Created order '.$order->order_number, 'Order', (int) $order->id, $request);
            $this->notifyManagers('new_order', 'طلب جديد '.$order->order_number, 'تم إنشاء طلب جديد ويحتاج مراجعة', route('admin.orders.show', $order));

            return $order;
        });

        $this->forceOrderVisibleIfLegacySoftDeleteDefault($order);

        if ($this->canOpenOrderDetails($order)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('status', 'تم إنشاء الطلب بنجاح');
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'تم إنشاء الطلب بنجاح');
    }

    public function show(Request $request, Order $order): View
    {
        $order->load(['customer', 'items', 'statusLogs', 'assignedStaff', 'assignedCraftsman', 'collections.collector']);

        return view('admin.orders.show', $this->sharedData($request) + [
            'order' => $order,
            'statusLogs' => $order->statusLogs()->orderByDesc('id')->get(),
        ]);
    }

    public function edit(Request $request, Order $order): View
    {
        $order->load(['items', 'collections.collector']);

        return view('admin.orders.edit', $this->sharedData($request) + [
            'order' => $order,
            'products' => Product::query()->orderBy('name')->get(),
            'colors' => Color::query()->orderBy('name')->get(),
            'staff' => User::query()->whereIn('role', ['owner', 'manager', 'staff'])->where('is_active', true)->get(),
            'craftsmen' => User::query()->where('role', 'craftsman')->where('is_active', true)->get(),
            'collectors' => Collector::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,confirmed,in_progress,ready,out_for_delivery,delivered,cancelled,returned',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
            'amount_paid' => 'nullable|numeric|min:0',
            'assigned_staff_id' => 'nullable|integer|exists:users,id',
            'assigned_craftsman_id' => 'nullable|integer|exists:users,id',
            'delivery_date' => 'nullable|date',
            'delivery_time_slot' => 'nullable|string|max:50',
            'internal_notes' => 'nullable|string|max:3000',
            'collector_ids' => 'nullable|array',
            'collector_ids.*' => 'nullable|integer|exists:collectors,id',
            'collector_amounts' => 'nullable|array',
            'collector_amounts.*' => 'nullable|numeric|min:0.01',
            'collector_notes' => 'nullable|array',
            'collector_notes.*' => 'nullable|string|max:300',
        ]);

        [$collectionRows, $collectionsTotal, $collectionsError] = $this->parseCollectionRows($request);
        if ($collectionsError !== null) {
            return back()->withInput()->withErrors(['collector_amounts' => $collectionsError]);
        }

        $user = $this->user($request);
        $oldStatus = $order->status;
        $newPaid = (float) ($validated['amount_paid'] ?? $order->amount_paid);

        if ($collectionsTotal > (float) $order->amount_total) {
            return back()->withInput()->withErrors([
                'collector_amounts' => 'إجمالي التحصيل لا يمكن أن يتجاوز إجمالي الطلب',
            ]);
        }

        if ($collectionsTotal > 0) {
            $newPaid = $collectionsTotal;
        }

        if ($newPaid > (float) $order->amount_total) {
            return back()->withInput()->withErrors(['amount_paid' => 'المبلغ المدفوع لا يمكن أن يتجاوز إجمالي الطلب']);
        }

        DB::transaction(function () use ($order, $validated, $newPaid, $collectionRows, $request, $user, $oldStatus): void {
            $order->fill([
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'assigned_staff_id' => $validated['assigned_staff_id'] ?? null,
                'assigned_craftsman_id' => $validated['assigned_craftsman_id'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'delivery_time_slot' => $validated['delivery_time_slot'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
            ]);

            $order->amount_paid = $newPaid;
            $order->amount_remaining = max(0, (float) $order->amount_total - (float) $order->amount_paid);

            if ($order->payment_status !== 'refunded') {
                $order->payment_status = $this->resolvePaymentStatus((float) $order->amount_paid, (float) $order->amount_total);
            }

            $order->save();

            if ($request->has('collector_ids') || $request->has('collector_amounts')) {
                $order->collections()->delete();

                foreach ($collectionRows as $row) {
                    OrderCollection::query()->create([
                        'order_id' => $order->id,
                        'collector_id' => $row['collector_id'],
                        'collector_name_snapshot' => $row['collector_name_snapshot'],
                        'amount' => $row['amount'],
                        'note' => $row['note'],
                        'created_by' => $user->id,
                    ]);
                }
            }

            if ($oldStatus !== $order->status) {
                OrderStatusLog::query()->create([
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $order->status,
                    'note' => 'تغيير حالة الطلب من لوحة الإدارة',
                    'changed_by' => $user->id,
                ]);
            }

            SystemLogger::log((int) $user->id, 'order_updated', 'Updated order '.$order->order_number, 'Order', (int) $order->id, $request);
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('status', 'تم تحديث الطلب');
    }

    public function destroy(Request $request, Order $order): RedirectResponse
    {
        $user = $this->user($request);
        $order->delete();

        SystemLogger::log((int) $user->id, 'order_deleted', 'Deleted order '.$order->order_number, 'Order', (int) $order->id, $request);

        return redirect()->route('admin.orders.index')->with('status', 'تم حذف الطلب');
    }

    private function resolveItemName(?int $productId): string
    {
        if (! $productId) {
            return 'عنصر طلب';
        }

        return (string) (Product::query()->where('id', $productId)->value('name') ?? 'عنصر طلب');
    }

    private function parseCollectionRows(Request $request): array
    {
        $collectorIds = $request->input('collector_ids', []);
        $collectorAmounts = $request->input('collector_amounts', []);
        $collectorNotes = $request->input('collector_notes', []);

        if (! is_array($collectorIds) || ! is_array($collectorAmounts)) {
            return [[], 0.0, null];
        }

        $collectorNames = Collector::query()
            ->whereIn('id', array_filter(array_map('intval', $collectorIds)))
            ->pluck('name', 'id')
            ->all();

        $rows = [];
        $total = 0.0;
        $max = max(count($collectorIds), count($collectorAmounts), count($collectorNotes));

        for ($i = 0; $i < $max; $i++) {
            $collectorId = isset($collectorIds[$i]) ? (int) $collectorIds[$i] : 0;
            $amountRaw = $collectorAmounts[$i] ?? null;
            $note = isset($collectorNotes[$i]) ? trim((string) $collectorNotes[$i]) : null;
            $amount = is_numeric($amountRaw) ? (float) $amountRaw : 0.0;

            if ($amount <= 0 && $collectorId <= 0) {
                continue;
            }

            if ($collectorId <= 0 && $amount > 0) {
                return [[], 0.0, 'يجب اختيار اسم المحصل لكل مبلغ تحصيل'];
            }

            if (! isset($collectorNames[$collectorId])) {
                return [[], 0.0, 'المحصل المختار غير صالح'];
            }

            $rows[] = [
                'collector_id' => $collectorId,
                'collector_name_snapshot' => (string) $collectorNames[$collectorId],
                'amount' => $amount,
                'note' => $note !== '' ? $note : null,
            ];
            $total += $amount;
        }

        return [$rows, round($total, 2), null];
    }

    private function resolvePaymentStatus(float $paid, float $total): string
    {
        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $total) {
            return 'paid';
        }

        return 'partial';
    }

    private function generateOrderNumber(): string
    {
        $year = date('Y');
        $last = Order::query()
            ->whereYear('created_at', (int) $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($last && preg_match('/(\d{5})$/', (string) $last->order_number, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'ORD-'.$year.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function notifyManagers(string $type, string $title, string $body, string $link): void
    {
        User::query()
            ->whereIn('role', ['owner', 'manager'])
            ->where('is_active', true)
            ->pluck('id')
            ->each(static fn ($id) => SystemNotifier::notify((int) $id, $type, $title, $body, $link));
    }

    private function canOpenOrderDetails(Order $order): bool
    {
        return Order::query()->whereKey($order->getKey())->exists();
    }

    private function forceOrderVisibleIfLegacySoftDeleteDefault(Order $order): void
    {
        if (! Schema::hasColumn($order->getTable(), 'deleted_at')) {
            return;
        }

        Order::withoutGlobalScopes()
            ->whereKey($order->getKey())
            ->update(['deleted_at' => null]);
    }

    private function orderRequiredFields(): array
    {
        $value = Setting::query()->value('order_required_fields');

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (! is_array($value)) {
            $value = ['customer_name'];
        }

        $allowed = array_keys(self::ORDER_FIELD_LABELS);
        $filtered = array_values(array_intersect($allowed, array_map('strval', $value)));

        if (count($filtered) === 0) {
            return ['customer_name'];
        }

        return $filtered;
    }

    private function orderStoreRules(array $requiredFields): array
    {
        return [
            'customer_name' => $this->composeRule($requiredFields, 'customer_name', 'string|max:100'),
            'customer_phone' => $this->composeRule($requiredFields, 'customer_phone', 'string|max:20'),
            'source' => $this->composeRule($requiredFields, 'source', 'in:facebook,instagram,whatsapp,phone,walk_in,website,other'),
            'assigned_staff_id' => $this->composeRule($requiredFields, 'assigned_staff_id', 'integer|exists:users,id'),
            'assigned_craftsman_id' => $this->composeRule($requiredFields, 'assigned_craftsman_id', 'integer|exists:users,id'),
            'delivery_address' => $this->composeRule($requiredFields, 'delivery_address', 'string|max:2000'),
            'shipping_zone_id' => $this->composeRule($requiredFields, 'shipping_zone_id', 'integer|exists:shipping_zones,id'),
            'delivery_date' => $this->composeRule($requiredFields, 'delivery_date', 'date'),
            'delivery_time_slot' => $this->composeRule($requiredFields, 'delivery_time_slot', 'string|max:50'),
            'delivery_fee' => $this->composeRule($requiredFields, 'delivery_fee', 'numeric|min:0'),
            'occasion' => $this->composeRule($requiredFields, 'occasion', 'string|max:100'),
            'recipient_name' => $this->composeRule($requiredFields, 'recipient_name', 'string|max:100'),
            'recipient_phone' => $this->composeRule($requiredFields, 'recipient_phone', 'string|max:20'),
            'card_message' => $this->composeRule($requiredFields, 'card_message', 'string|max:3000'),
            'notes' => $this->composeRule($requiredFields, 'notes', 'string|max:3000'),
            'internal_notes' => $this->composeRule($requiredFields, 'internal_notes', 'string|max:3000'),
            'product_id' => $this->composeRule($requiredFields, 'product_id', 'integer|exists:products,id'),
            'color_id' => $this->composeRule($requiredFields, 'color_id', 'integer|exists:colors,id'),
            'quantity' => $this->composeRule($requiredFields, 'quantity', 'integer|min:1|max:999'),
            'unit_price' => $this->composeRule($requiredFields, 'unit_price', 'numeric|min:0'),
        ];
    }

    private function composeRule(array $requiredFields, string $field, string $tail): string
    {
        return (in_array($field, $requiredFields, true) ? 'required|' : 'nullable|').$tail;
    }
}
