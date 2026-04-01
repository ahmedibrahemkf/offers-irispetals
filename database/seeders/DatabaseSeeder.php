<?php

namespace Database\Seeders;

use App\Models\Collector;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner',
                'phone' => '01000000000',
                'role' => 'owner',
                'is_active' => true,
                'base_salary' => 0,
                'hire_date' => now()->toDateString(),
                'password' => Hash::make('Owner@123456'),
            ]
        );

        Setting::query()->firstOrCreate([], [
            'shop_name' => 'Iris Petals',
            'primary_color' => '#6D28D9',
            'currency' => 'EGP',
            'currency_symbol' => 'ج',
            'show_tax' => false,
            'tax_rate' => 0,
        ]);

        foreach (['بوكيهات', 'ورود مفردة', 'إكسسوارات'] as $name) {
            ProductCategory::query()->firstOrCreate(['name' => $name]);
        }

        foreach (['إيجار', 'رواتب', 'مواصلات', 'تشغيل'] as $name) {
            ExpenseCategory::query()->firstOrCreate(['name' => $name]);
        }

        foreach ([
            ['القاهرة', 150],
            ['الجيزة', 200],
            ['القليوبية', 220],
        ] as [$zone, $fee]) {
            ShippingZone::query()->firstOrCreate(['name' => $zone], ['fee' => $fee]);
        }

        Product::query()->firstOrCreate(['name' => 'بوكيه هولندي فاخر'], [
            'sell_price' => 2750,
            'cost_price' => 1800,
            'stock_quantity' => 50,
            'min_stock_alert' => 5,
        ]);

        Product::query()->firstOrCreate(['name' => 'بوكيه مصري طازج'], [
            'sell_price' => 1200,
            'cost_price' => 700,
            'stock_quantity' => 80,
            'min_stock_alert' => 10,
        ]);

        Collector::query()->firstOrCreate(['name' => 'Ahmed Ibrahim'], [
            'phone' => '01055835754',
            'is_active' => true,
        ]);
    }
}

