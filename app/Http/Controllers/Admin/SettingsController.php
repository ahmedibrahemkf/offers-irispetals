<?php

namespace App\Http\Controllers\Admin;

use App\Models\Collector;
use App\Models\Color;
use App\Models\ExpenseCategory;
use App\Models\ProductCategory;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Support\SystemLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingsController extends BaseAdminController
{
    public function index(Request $request): View
    {
        return view('admin.settings.index', $this->sharedData($request) + [
            'setting' => Setting::query()->first(),
            'zones' => ShippingZone::query()->orderBy('name')->get(),
            'colors' => Color::query()->orderBy('name')->get(),
            'productCategories' => ProductCategory::query()->orderBy('name')->get(),
            'expenseCategories' => ExpenseCategory::query()->orderBy('name')->get(),
            'collectors' => Collector::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function updateMain(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:150',
            'address' => 'nullable|string|max:1500',
            'phone' => 'nullable|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'whatsapp' => 'nullable|string|max:20',
            'website_url' => 'nullable|string|max:255',
            'invoice_header_extra' => 'nullable|string|max:2000',
            'invoice_footer_text' => 'nullable|string|max:2000',
            'invoice_terms' => 'nullable|string|max:3000',
            'show_tax' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:5',
            'primary_color' => 'nullable|string|max:7',
        ]);

        $setting = Setting::query()->first();
        if (! $setting) {
            $setting = new Setting();
        }

        $setting->fill([
            ...$validated,
            'show_tax' => (bool) ($validated['show_tax'] ?? false),
        ]);
        $setting->save();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads/settings', 'public');
            $setting->logo_url = '/storage/'.$path;
            $setting->save();
        }

        if ($request->hasFile('invoice_logo')) {
            $path = $request->file('invoice_logo')->store('uploads/settings', 'public');
            $setting->invoice_logo_url = '/storage/'.$path;
            $setting->save();
        }

        Cache::forget('crm.settings.first');
        Cache::forget('crm.settings.first.v2');
        SystemLogger::log((int) $this->user($request)->id, 'settings_updated', 'Updated system settings', 'Setting', (int) $setting->id, $request);

        return back()->with('status', 'تم حفظ الإعدادات');
    }

    public function storeZone(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'fee' => 'required|numeric|min:0',
            'eta_minutes' => 'nullable|integer|min:0',
        ]);

        ShippingZone::query()->create($validated);

        return back()->with('status', 'تمت إضافة منطقة التوصيل');
    }

    public function storeColor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'hex_code' => 'required|string|max:7',
        ]);

        Color::query()->create($validated);

        return back()->with('status', 'تمت إضافة اللون');
    }

    public function storeProductCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
        ]);

        ProductCategory::query()->create($validated);

        return back()->with('status', 'تمت إضافة فئة منتج');
    }

    public function storeExpenseCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
        ]);

        ExpenseCategory::query()->create($validated);

        return back()->with('status', 'تمت إضافة فئة مصروف');
    }

    public function storeCollector(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:20',
        ]);

        Collector::query()->create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('status', 'تمت إضافة محصل جديد');
    }
}
