<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\User;
use App\Support\SystemNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicOrderController extends Controller
{
    public function show(): View
    {
        $settings = Setting::query()->first();

        return view('public.order', [
            'settings' => $settings,
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'zones' => ShippingZone::query()->orderBy('name')->get(),
            'publicWhatsapp' => $this->normalizePhone((string) ($settings?->whatsapp ?: '01055835754')),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:120',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:2000',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999',
            'shipping_zone_id' => 'nullable|integer|exists:shipping_zones,id',
            'delivery_date' => 'nullable|date',
            'delivery_time_slot' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1500',
        ]);

        $systemUserId = (int) (User::query()->where('role', 'owner')->value('id') ?: 1);

        $order = DB::transaction(function () use ($validated, $systemUserId): Order {
            $customer = Customer::query()->firstOrCreate(
                ['phone' => $validated['customer_phone']],
                [
                    'name' => $validated['customer_name'],
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['delivery_address'],
                ]
            );

            $product = Product::query()->findOrFail((int) $validated['product_id']);
            $quantity = (int) $validated['quantity'];
            $itemTotal = $quantity * (float) $product->sell_price;
            $shippingFee = 0;

            if (! empty($validated['shipping_zone_id'])) {
                $shippingFee = (float) (ShippingZone::query()->where('id', (int) $validated['shipping_zone_id'])->value('fee') ?? 0);
            }

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name_snapshot' => $validated['customer_name'],
                'customer_phone_snapshot' => $validated['customer_phone'],
                'source' => 'website',
                'status' => 'new',
                'payment_status' => 'unpaid',
                'amount_total' => $itemTotal + $shippingFee,
                'amount_paid' => 0,
                'amount_remaining' => $itemTotal + $shippingFee,
                'delivery_address' => $validated['delivery_address'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'delivery_time_slot' => $validated['delivery_time_slot'] ?? null,
                'delivery_fee' => $shippingFee,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $systemUserId,
            ]);

            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'item_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $product->sell_price,
                'line_total' => $itemTotal,
            ]);

            return $order;
        });

        User::query()
            ->whereIn('role', ['owner', 'manager'])
            ->where('is_active', true)
            ->pluck('id')
            ->each(static fn ($id) => SystemNotifier::notify(
                (int) $id,
                'new_order',
                'طلب جديد من الصفحة العامة',
                'رقم الطلب '.$order->order_number,
                route('admin.orders.show', $order)
            ));

        $whatsappMessage = implode("\n", [
            'طلب جديد من الصفحة العامة',
            'رقم الطلب: '.$order->order_number,
            'العميل: '.$order->customer_name_snapshot,
            'الهاتف: '.$order->customer_phone_snapshot,
            'العنوان: '.$order->delivery_address,
            'الإجمالي: '.number_format((float) $order->amount_total, 2).' ج',
        ]);

        $settings = Setting::query()->first();
        $whatsappNumber = $this->normalizePhone((string) ($settings?->whatsapp ?: '01055835754'));
        $whatsappUrl = 'https://wa.me/'.$whatsappNumber.'?text='.urlencode($whatsappMessage);

        return redirect()
            ->route('public.order.show')
            ->with('status', 'تم إرسال الطلب بنجاح. رقم الطلب: '.$order->order_number)
            ->with('wa_link', $whatsappUrl);
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

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';
        if ($digits === '') {
            return '201055835754';
        }

        if (str_starts_with($digits, '00')) {
            return substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            return '2'.ltrim($digits, '0');
        }

        return $digits;
    }
}

