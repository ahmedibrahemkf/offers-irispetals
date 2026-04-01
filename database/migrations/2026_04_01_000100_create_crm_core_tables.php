<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('shop_name', 150)->default('Iris Petals');
            $table->string('logo_url')->nullable();
            $table->string('invoice_logo_url')->nullable();
            $table->string('primary_color', 7)->default('#6D28D9');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('phone_alt', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->text('invoice_header_extra')->nullable();
            $table->text('invoice_footer_text')->nullable();
            $table->text('invoice_terms')->nullable();
            $table->boolean('show_tax')->default(false);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('currency', 10)->default('EGP');
            $table->string('currency_symbol', 5)->default('ج');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('colors', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80);
            $table->string('hex_code', 7)->default('#FFFFFF');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shipping_zones', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->decimal('fee', 10, 2)->default(0);
            $table->integer('eta_minutes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 20)->index();
            $table->string('phone_alt', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('sku', 50)->nullable()->unique();
            $table->string('name', 180);
            $table->text('description')->nullable();
            $table->decimal('sell_price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(0);
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name_snapshot', 100);
            $table->string('customer_phone_snapshot', 20)->nullable();
            $table->enum('source', ['facebook', 'instagram', 'whatsapp', 'phone', 'walk_in', 'website', 'other'])->default('walk_in');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_craftsman_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['new', 'confirmed', 'in_progress', 'ready', 'out_for_delivery', 'delivered', 'cancelled', 'returned'])->default('new');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'instapay', 'vodafone_cash', 'bank_transfer', 'other'])->nullable();
            $table->decimal('amount_total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_remaining', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_lat', 10, 7)->nullable();
            $table->decimal('delivery_lng', 10, 7)->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time_slot', 50)->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('occasion', 100)->nullable();
            $table->string('recipient_name', 100)->nullable();
            $table->string('recipient_phone', 20)->nullable();
            $table->text('card_message')->nullable();
            $table->json('images')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('delivery_date');
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->string('item_name', 180);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_status_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('old_status', 40)->nullable();
            $table->string('new_status', 40);
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('invoice_number', 20)->unique();
            $table->enum('type', ['order', 'direct'])->default('order');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name_snapshot', 100)->nullable();
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->dateTime('issued_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name', 180);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method', 50)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchases', function (Blueprint $table): void {
            $table->id();
            $table->string('purchase_number', 20)->unique();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('purchase_date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->string('title', 150);
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('expense_date')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_financials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained('users');
            $table->enum('type', ['salary', 'advance', 'deduction', 'bonus'])->default('salary');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment'])->default('adjustment');
            $table->integer('quantity_change')->default(0);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title', 255);
            $table->text('body');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'is_read']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('action', 100);
            $table->text('description');
            $table->string('model_type', 50)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('employee_financials');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('settings');
    }
};
