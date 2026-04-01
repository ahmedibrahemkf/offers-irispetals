<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureCoreTables();
        $this->ensureCriticalColumns();
    }

    public function down(): void
    {
        // Non-destructive upgrade migration.
    }

    private function ensureCoreTables(): void
    {
        $this->createTableIfMissing('settings', function (Blueprint $table): void {
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

        $this->createTableIfMissing('product_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('colors', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80);
            $table->string('hex_code', 7)->default('#FFFFFF');
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('shipping_zones', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->decimal('fee', 10, 2)->default(0);
            $table->integer('eta_minutes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('expense_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 20)->nullable();
            $table->string('phone_alt', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('phone');
        });

        $this->createTableIfMissing('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('products', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->string('sku', 50)->nullable();
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

        $this->createTableIfMissing('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number', 40)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name_snapshot', 120)->nullable();
            $table->string('customer_phone_snapshot', 20)->nullable();
            $table->string('source', 40)->default('website');
            $table->unsignedBigInteger('assigned_staff_id')->nullable();
            $table->unsignedBigInteger('assigned_craftsman_id')->nullable();
            $table->string('status', 40)->default('new');
            $table->string('payment_status', 40)->default('unpaid');
            $table->string('payment_method', 50)->nullable();
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
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('occasion', 100)->nullable();
            $table->string('recipient_name', 100)->nullable();
            $table->string('recipient_phone', 20)->nullable();
            $table->text('card_message')->nullable();
            $table->json('images')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('delivery_date');
        });

        $this->createTableIfMissing('order_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->string('item_name', 180);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('order_status_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('old_status', 40)->nullable();
            $table->string('new_status', 40);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('invoice_number', 40)->nullable();
            $table->string('type', 30)->default('order');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name_snapshot', 120)->nullable();
            $table->string('payment_status', 40)->default('unpaid');
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->dateTime('issued_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('item_name', 180);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('invoice_payments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->index();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method', 50)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('purchases', function (Blueprint $table): void {
            $table->id();
            $table->string('purchase_number', 40)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('purchase_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->index();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('expenses', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->string('title', 150);
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('expense_date')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('employee_financials', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('type', 30)->default('salary');
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('effective_date')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('type', 30)->default('adjustment');
            $table->integer('quantity_change')->default(0);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('type', 50);
            $table->string('title', 255);
            $table->text('body');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->createTableIfMissing('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('action', 100);
            $table->text('description');
            $table->string('model_type', 50)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function ensureCriticalColumns(): void
    {
        if (Schema::hasTable('users')) {
            $this->addColumnIfMissing('users', 'username', fn (Blueprint $table) => $table->string('username', 60)->nullable());
            $this->addColumnIfMissing('users', 'phone', fn (Blueprint $table) => $table->string('phone', 20)->nullable());
            $this->addColumnIfMissing('users', 'role', fn (Blueprint $table) => $table->string('role', 20)->default('staff'));
            $this->addColumnIfMissing('users', 'is_active', fn (Blueprint $table) => $table->boolean('is_active')->default(true));
            $this->addColumnIfMissing('users', 'base_salary', fn (Blueprint $table) => $table->decimal('base_salary', 10, 2)->default(0));
            $this->addColumnIfMissing('users', 'hire_date', fn (Blueprint $table) => $table->date('hire_date')->nullable());
            $this->addColumnIfMissing('users', 'avatar_url', fn (Blueprint $table) => $table->string('avatar_url')->nullable());
            $this->addColumnIfMissing('users', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
        }

        if (Schema::hasTable('order_collections')) {
            $this->addColumnIfMissing('order_collections', 'collector_name_snapshot', fn (Blueprint $table) => $table->string('collector_name_snapshot', 120)->nullable());
            $this->addColumnIfMissing('order_collections', 'amount', fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0));
            $this->addColumnIfMissing('order_collections', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
            $this->addColumnIfMissing('order_collections', 'created_at', fn (Blueprint $table) => $table->timestamp('created_at')->nullable());
            $this->addColumnIfMissing('order_collections', 'updated_at', fn (Blueprint $table) => $table->timestamp('updated_at')->nullable());
        }

        if (Schema::hasTable('orders')) {
            $this->addColumnIfMissing('orders', 'amount_paid', fn (Blueprint $table) => $table->decimal('amount_paid', 10, 2)->default(0));
            $this->addColumnIfMissing('orders', 'amount_remaining', fn (Blueprint $table) => $table->decimal('amount_remaining', 10, 2)->default(0));
            $this->addColumnIfMissing('orders', 'payment_status', fn (Blueprint $table) => $table->string('payment_status', 40)->default('unpaid'));
        }
    }

    private function createTableIfMissing(string $tableName, callable $callback): void
    {
        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, $callback);
        }
    }

    private function addColumnIfMissing(string $tableName, string $columnName, callable $callback): void
    {
        if (! Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, $callback);
        }
    }
};

