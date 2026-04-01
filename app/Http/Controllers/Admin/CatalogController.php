<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Support\SystemLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CatalogController extends BaseAdminController
{
    public function products(Request $request): View
    {
        return view('admin.catalog.products', $this->sharedData($request) + [
            'products' => Product::query()->with('category')->orderByDesc('id')->paginate(20),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:180',
            'product_category_id' => 'nullable|integer|exists:product_categories,id',
            'sell_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50',
        ]);

        $product = Product::query()->create([
            'name' => $validated['name'],
            'product_category_id' => $validated['product_category_id'] ?? null,
            'sell_price' => $validated['sell_price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'min_stock_alert' => $validated['min_stock_alert'] ?? 0,
            'sku' => $validated['sku'] ?? null,
        ]);

        $user = $this->user($request);
        SystemLogger::log((int) $user->id, 'product_created', 'Added product '.$product->name, 'Product', (int) $product->id, $request);

        return back()->with('status', 'تمت إضافة المنتج');
    }

    public function showProduct(Request $request, Product $product): View
    {
        return view('admin.catalog.product-show', $this->sharedData($request) + [
            'product' => $product->load('category'),
            'movements' => StockMovement::query()->where('product_id', $product->id)->orderByDesc('id')->limit(50)->get(),
        ]);
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity_change' => 'required|integer|not_in:0',
            'note' => 'nullable|string|max:300',
        ]);

        $user = $this->user($request);
        DB::transaction(function () use ($product, $validated, $user): void {
            $change = (int) $validated['quantity_change'];
            $product->stock_quantity = max(0, (int) $product->stock_quantity + $change);
            $product->save();

            StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => $change > 0 ? 'in' : 'out',
                'quantity_change' => $change,
                'reference_type' => 'manual_adjustment',
                'note' => $validated['note'] ?? null,
                'created_by' => $user->id,
            ]);
        });

        SystemLogger::log((int) $user->id, 'product_stock_adjusted', 'Adjusted stock for '.$product->name, 'Product', (int) $product->id, $request);

        return back()->with('status', 'تم تحديث المخزون');
    }

    public function suppliers(Request $request): View
    {
        return view('admin.catalog.suppliers', $this->sharedData($request) + [
            'suppliers' => Supplier::query()->orderByDesc('id')->paginate(20),
        ]);
    }

    public function storeSupplier(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:1500',
            'notes' => 'nullable|string|max:1500',
        ]);

        Supplier::query()->create($validated);

        return back()->with('status', 'تمت إضافة المورد');
    }

    public function purchases(Request $request): View
    {
        return view('admin.catalog.purchases', $this->sharedData($request) + [
            'purchases' => Purchase::query()->with('supplier')->orderByDesc('id')->paginate(20),
            'suppliers' => Supplier::query()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function storePurchase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'purchase_date' => 'nullable|date',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1500',
        ]);

        $user = $this->user($request);

        DB::transaction(function () use ($validated, $user): void {
            $quantity = (int) $validated['quantity'];
            $unitCost = (float) $validated['unit_cost'];
            $total = $quantity * $unitCost;

            $purchase = Purchase::query()->create([
                'purchase_number' => $this->generatePurchaseNumber(),
                'supplier_id' => $validated['supplier_id'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? now()->toDateString(),
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            PurchaseItem::query()->create([
                'purchase_id' => $purchase->id,
                'product_id' => $validated['product_id'],
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'line_total' => $total,
            ]);

            $product = Product::query()->findOrFail((int) $validated['product_id']);
            $product->stock_quantity = (int) $product->stock_quantity + $quantity;
            $product->save();

            StockMovement::query()->create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity_change' => $quantity,
                'reference_type' => 'purchase',
                'reference_id' => $purchase->id,
                'note' => 'إضافة مخزون من المشتريات',
                'created_by' => $user->id,
            ]);
        });

        return back()->with('status', 'تم تسجيل المشتريات');
    }

    private function generatePurchaseNumber(): string
    {
        $year = date('Y');
        $last = Purchase::query()
            ->whereYear('created_at', (int) $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($last && preg_match('/(\d{5})$/', (string) $last->purchase_number, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return 'PUR-'.$year.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}

