<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->backfillUsers();
        $this->backfillProductCategories();
        $this->backfillColors();
        $this->backfillShippingZones();
        $this->backfillExpenseCategories();
        $this->backfillCollectors();
        $this->backfillOrders();
        $this->backfillOrderItems();
        $this->backfillOrderStatusLogs();
        $this->backfillProducts();
        $this->backfillInvoices();
        $this->backfillInvoiceItems();
        $this->backfillInvoicePayments();
        $this->backfillSettings();
        $this->backfillCustomers();
        $this->backfillSuppliers();
        $this->backfillExpenses();
        $this->backfillPurchases();
        $this->backfillPurchaseItems();
        $this->backfillStockMovements();
        $this->backfillNotifications();
        $this->backfillActivityLogs();
        $this->backfillOrderCollections();
    }

    public function down(): void
    {
        // Non-destructive upgrade migration.
    }

    private function backfillUsers(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $this->addColumnIfMissing('users', 'username', fn (Blueprint $table) => $table->string('username', 60)->nullable());
        $this->addColumnIfMissing('users', 'phone', fn (Blueprint $table) => $table->string('phone', 20)->nullable());
        $this->addColumnIfMissing('users', 'role', fn (Blueprint $table) => $table->string('role', 20)->default('staff'));
        $this->addColumnIfMissing('users', 'is_active', fn (Blueprint $table) => $table->boolean('is_active')->default(true));
        $this->addColumnIfMissing('users', 'base_salary', fn (Blueprint $table) => $table->decimal('base_salary', 10, 2)->default(0));
        $this->addColumnIfMissing('users', 'hire_date', fn (Blueprint $table) => $table->date('hire_date')->nullable());
        $this->addColumnIfMissing('users', 'avatar_url', fn (Blueprint $table) => $table->string('avatar_url')->nullable());
        $this->addColumnIfMissing('users', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillOrders(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        $this->addColumnIfMissing('orders', 'order_number', fn (Blueprint $table) => $table->string('order_number', 40)->nullable());
        $this->addColumnIfMissing('orders', 'customer_id', fn (Blueprint $table) => $table->unsignedBigInteger('customer_id')->nullable());
        $this->addColumnIfMissing('orders', 'customer_name_snapshot', fn (Blueprint $table) => $table->string('customer_name_snapshot', 120)->nullable());
        $this->addColumnIfMissing('orders', 'customer_phone_snapshot', fn (Blueprint $table) => $table->string('customer_phone_snapshot', 20)->nullable());
        $this->addColumnIfMissing('orders', 'source', fn (Blueprint $table) => $table->string('source', 40)->default('website'));
        $this->addColumnIfMissing('orders', 'assigned_staff_id', fn (Blueprint $table) => $table->unsignedBigInteger('assigned_staff_id')->nullable());
        $this->addColumnIfMissing('orders', 'assigned_craftsman_id', fn (Blueprint $table) => $table->unsignedBigInteger('assigned_craftsman_id')->nullable());
        $this->addColumnIfMissing('orders', 'status', fn (Blueprint $table) => $table->string('status', 40)->default('new'));
        $this->addColumnIfMissing('orders', 'payment_status', fn (Blueprint $table) => $table->string('payment_status', 40)->default('unpaid'));
        $this->addColumnIfMissing('orders', 'payment_method', fn (Blueprint $table) => $table->string('payment_method', 50)->nullable());
        $this->addColumnIfMissing('orders', 'amount_total', fn (Blueprint $table) => $table->decimal('amount_total', 10, 2)->default(0));
        $this->addColumnIfMissing('orders', 'amount_paid', fn (Blueprint $table) => $table->decimal('amount_paid', 10, 2)->default(0));
        $this->addColumnIfMissing('orders', 'amount_remaining', fn (Blueprint $table) => $table->decimal('amount_remaining', 10, 2)->default(0));
        $this->addColumnIfMissing('orders', 'discount_amount', fn (Blueprint $table) => $table->decimal('discount_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('orders', 'discount_reason', fn (Blueprint $table) => $table->string('discount_reason')->nullable());
        $this->addColumnIfMissing('orders', 'delivery_address', fn (Blueprint $table) => $table->text('delivery_address')->nullable());
        $this->addColumnIfMissing('orders', 'delivery_lat', fn (Blueprint $table) => $table->decimal('delivery_lat', 10, 7)->nullable());
        $this->addColumnIfMissing('orders', 'delivery_lng', fn (Blueprint $table) => $table->decimal('delivery_lng', 10, 7)->nullable());
        $this->addColumnIfMissing('orders', 'delivery_date', fn (Blueprint $table) => $table->date('delivery_date')->nullable());
        $this->addColumnIfMissing('orders', 'delivery_time_slot', fn (Blueprint $table) => $table->string('delivery_time_slot', 50)->nullable());
        $this->addColumnIfMissing('orders', 'delivery_fee', fn (Blueprint $table) => $table->decimal('delivery_fee', 10, 2)->default(0));
        $this->addColumnIfMissing('orders', 'notes', fn (Blueprint $table) => $table->text('notes')->nullable());
        $this->addColumnIfMissing('orders', 'internal_notes', fn (Blueprint $table) => $table->text('internal_notes')->nullable());
        $this->addColumnIfMissing('orders', 'occasion', fn (Blueprint $table) => $table->string('occasion', 100)->nullable());
        $this->addColumnIfMissing('orders', 'recipient_name', fn (Blueprint $table) => $table->string('recipient_name', 100)->nullable());
        $this->addColumnIfMissing('orders', 'recipient_phone', fn (Blueprint $table) => $table->string('recipient_phone', 20)->nullable());
        $this->addColumnIfMissing('orders', 'card_message', fn (Blueprint $table) => $table->text('card_message')->nullable());
        $this->addColumnIfMissing('orders', 'images', fn (Blueprint $table) => $table->json('images')->nullable());
        $this->addColumnIfMissing('orders', 'cancel_reason', fn (Blueprint $table) => $table->text('cancel_reason')->nullable());
        $this->addColumnIfMissing('orders', 'cancelled_at', fn (Blueprint $table) => $table->dateTime('cancelled_at')->nullable());
        $this->addColumnIfMissing('orders', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('orders', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillProductCategories(): void
    {
        if (! Schema::hasTable('product_categories')) {
            return;
        }

        $this->addColumnIfMissing('product_categories', 'name', fn (Blueprint $table) => $table->string('name', 120)->nullable());
        $this->addColumnIfMissing('product_categories', 'notes', fn (Blueprint $table) => $table->text('notes')->nullable());
        $this->addColumnIfMissing('product_categories', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillColors(): void
    {
        if (! Schema::hasTable('colors')) {
            return;
        }

        $this->addColumnIfMissing('colors', 'name', fn (Blueprint $table) => $table->string('name', 80)->nullable());
        $this->addColumnIfMissing('colors', 'hex_code', fn (Blueprint $table) => $table->string('hex_code', 7)->default('#FFFFFF'));
        $this->addColumnIfMissing('colors', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillShippingZones(): void
    {
        if (! Schema::hasTable('shipping_zones')) {
            return;
        }

        $this->addColumnIfMissing('shipping_zones', 'name', fn (Blueprint $table) => $table->string('name', 100)->nullable());
        $this->addColumnIfMissing('shipping_zones', 'fee', fn (Blueprint $table) => $table->decimal('fee', 10, 2)->default(0));
        $this->addColumnIfMissing('shipping_zones', 'eta_minutes', fn (Blueprint $table) => $table->integer('eta_minutes')->nullable());
        $this->addColumnIfMissing('shipping_zones', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillExpenseCategories(): void
    {
        if (! Schema::hasTable('expense_categories')) {
            return;
        }

        $this->addColumnIfMissing('expense_categories', 'name', fn (Blueprint $table) => $table->string('name', 120)->nullable());
        $this->addColumnIfMissing('expense_categories', 'notes', fn (Blueprint $table) => $table->text('notes')->nullable());
        $this->addColumnIfMissing('expense_categories', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillCollectors(): void
    {
        if (! Schema::hasTable('collectors')) {
            return;
        }

        $this->addColumnIfMissing('collectors', 'name', fn (Blueprint $table) => $table->string('name', 120)->nullable());
        $this->addColumnIfMissing('collectors', 'phone', fn (Blueprint $table) => $table->string('phone', 20)->nullable());
        $this->addColumnIfMissing('collectors', 'is_active', fn (Blueprint $table) => $table->boolean('is_active')->default(true));
        $this->addColumnIfMissing('collectors', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillOrderItems(): void
    {
        if (! Schema::hasTable('order_items')) {
            return;
        }

        $this->addColumnIfMissing('order_items', 'order_id', fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable());
        $this->addColumnIfMissing('order_items', 'product_id', fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable());
        $this->addColumnIfMissing('order_items', 'color_id', fn (Blueprint $table) => $table->unsignedBigInteger('color_id')->nullable());
        $this->addColumnIfMissing('order_items', 'item_name', fn (Blueprint $table) => $table->string('item_name', 180)->nullable());
        $this->addColumnIfMissing('order_items', 'quantity', fn (Blueprint $table) => $table->integer('quantity')->default(1));
        $this->addColumnIfMissing('order_items', 'unit_price', fn (Blueprint $table) => $table->decimal('unit_price', 10, 2)->default(0));
        $this->addColumnIfMissing('order_items', 'line_total', fn (Blueprint $table) => $table->decimal('line_total', 10, 2)->default(0));
        $this->addColumnIfMissing('order_items', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillOrderStatusLogs(): void
    {
        if (! Schema::hasTable('order_status_logs')) {
            return;
        }

        $this->addColumnIfMissing('order_status_logs', 'order_id', fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable());
        $this->addColumnIfMissing('order_status_logs', 'old_status', fn (Blueprint $table) => $table->string('old_status', 40)->nullable());
        $this->addColumnIfMissing('order_status_logs', 'new_status', fn (Blueprint $table) => $table->string('new_status', 40)->nullable());
        $this->addColumnIfMissing('order_status_logs', 'note', fn (Blueprint $table) => $table->text('note')->nullable());
        $this->addColumnIfMissing('order_status_logs', 'changed_by', fn (Blueprint $table) => $table->unsignedBigInteger('changed_by')->nullable());
        $this->addColumnIfMissing('order_status_logs', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillProducts(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        $this->addColumnIfMissing('products', 'product_category_id', fn (Blueprint $table) => $table->unsignedBigInteger('product_category_id')->nullable());
        $this->addColumnIfMissing('products', 'sku', fn (Blueprint $table) => $table->string('sku', 50)->nullable());
        $this->addColumnIfMissing('products', 'name', fn (Blueprint $table) => $table->string('name', 180)->nullable());
        $this->addColumnIfMissing('products', 'description', fn (Blueprint $table) => $table->text('description')->nullable());
        $this->addColumnIfMissing('products', 'sell_price', fn (Blueprint $table) => $table->decimal('sell_price', 10, 2)->default(0));
        $this->addColumnIfMissing('products', 'cost_price', fn (Blueprint $table) => $table->decimal('cost_price', 10, 2)->default(0));
        $this->addColumnIfMissing('products', 'stock_quantity', fn (Blueprint $table) => $table->integer('stock_quantity')->default(0));
        $this->addColumnIfMissing('products', 'min_stock_alert', fn (Blueprint $table) => $table->integer('min_stock_alert')->default(0));
        $this->addColumnIfMissing('products', 'image_url', fn (Blueprint $table) => $table->string('image_url')->nullable());
        $this->addColumnIfMissing('products', 'is_active', fn (Blueprint $table) => $table->boolean('is_active')->default(true));
        $this->addColumnIfMissing('products', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillInvoices(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        $this->addColumnIfMissing('invoices', 'invoice_number', fn (Blueprint $table) => $table->string('invoice_number', 40)->nullable());
        $this->addColumnIfMissing('invoices', 'type', fn (Blueprint $table) => $table->string('type', 30)->default('order'));
        $this->addColumnIfMissing('invoices', 'order_id', fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable());
        $this->addColumnIfMissing('invoices', 'customer_id', fn (Blueprint $table) => $table->unsignedBigInteger('customer_id')->nullable());
        $this->addColumnIfMissing('invoices', 'customer_name_snapshot', fn (Blueprint $table) => $table->string('customer_name_snapshot', 120)->nullable());
        $this->addColumnIfMissing('invoices', 'payment_status', fn (Blueprint $table) => $table->string('payment_status', 40)->default('unpaid'));
        $this->addColumnIfMissing('invoices', 'sub_total', fn (Blueprint $table) => $table->decimal('sub_total', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'discount_amount', fn (Blueprint $table) => $table->decimal('discount_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'tax_amount', fn (Blueprint $table) => $table->decimal('tax_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'delivery_fee', fn (Blueprint $table) => $table->decimal('delivery_fee', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'total_amount', fn (Blueprint $table) => $table->decimal('total_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'paid_amount', fn (Blueprint $table) => $table->decimal('paid_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'remaining_amount', fn (Blueprint $table) => $table->decimal('remaining_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('invoices', 'issued_at', fn (Blueprint $table) => $table->dateTime('issued_at')->nullable());
        $this->addColumnIfMissing('invoices', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('invoices', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillInvoiceItems(): void
    {
        if (! Schema::hasTable('invoice_items')) {
            return;
        }

        $this->addColumnIfMissing('invoice_items', 'invoice_id', fn (Blueprint $table) => $table->unsignedBigInteger('invoice_id')->nullable());
        $this->addColumnIfMissing('invoice_items', 'product_id', fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable());
        $this->addColumnIfMissing('invoice_items', 'item_name', fn (Blueprint $table) => $table->string('item_name', 180)->nullable());
        $this->addColumnIfMissing('invoice_items', 'quantity', fn (Blueprint $table) => $table->integer('quantity')->default(1));
        $this->addColumnIfMissing('invoice_items', 'unit_price', fn (Blueprint $table) => $table->decimal('unit_price', 10, 2)->default(0));
        $this->addColumnIfMissing('invoice_items', 'line_total', fn (Blueprint $table) => $table->decimal('line_total', 10, 2)->default(0));
        $this->addColumnIfMissing('invoice_items', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillInvoicePayments(): void
    {
        if (! Schema::hasTable('invoice_payments')) {
            return;
        }

        $this->addColumnIfMissing('invoice_payments', 'invoice_id', fn (Blueprint $table) => $table->unsignedBigInteger('invoice_id')->nullable());
        $this->addColumnIfMissing('invoice_payments', 'amount', fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0));
        $this->addColumnIfMissing('invoice_payments', 'method', fn (Blueprint $table) => $table->string('method', 50)->nullable());
        $this->addColumnIfMissing('invoice_payments', 'note', fn (Blueprint $table) => $table->text('note')->nullable());
        $this->addColumnIfMissing('invoice_payments', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('invoice_payments', 'paid_at', fn (Blueprint $table) => $table->dateTime('paid_at')->nullable());
        $this->addColumnIfMissing('invoice_payments', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $this->addColumnIfMissing('settings', 'shop_name', fn (Blueprint $table) => $table->string('shop_name', 150)->default('Iris Petals'));
        $this->addColumnIfMissing('settings', 'logo_url', fn (Blueprint $table) => $table->string('logo_url')->nullable());
        $this->addColumnIfMissing('settings', 'invoice_logo_url', fn (Blueprint $table) => $table->string('invoice_logo_url')->nullable());
        $this->addColumnIfMissing('settings', 'primary_color', fn (Blueprint $table) => $table->string('primary_color', 7)->default('#6D28D9'));
        $this->addColumnIfMissing('settings', 'address', fn (Blueprint $table) => $table->text('address')->nullable());
        $this->addColumnIfMissing('settings', 'phone', fn (Blueprint $table) => $table->string('phone', 20)->nullable());
        $this->addColumnIfMissing('settings', 'whatsapp', fn (Blueprint $table) => $table->string('whatsapp', 20)->nullable());
        $this->addColumnIfMissing('settings', 'currency', fn (Blueprint $table) => $table->string('currency', 10)->default('EGP'));
        $this->addColumnIfMissing('settings', 'currency_symbol', fn (Blueprint $table) => $table->string('currency_symbol', 5)->default('ج'));
        $this->addColumnIfMissing('settings', 'show_tax', fn (Blueprint $table) => $table->boolean('show_tax')->default(false));
        $this->addColumnIfMissing('settings', 'tax_rate', fn (Blueprint $table) => $table->decimal('tax_rate', 5, 2)->default(0));
        $this->addColumnIfMissing('settings', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillCustomers(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        $this->addColumnIfMissing('customers', 'name', fn (Blueprint $table) => $table->string('name', 120)->nullable());
        $this->addColumnIfMissing('customers', 'phone', fn (Blueprint $table) => $table->string('phone', 20)->nullable());
        $this->addColumnIfMissing('customers', 'address', fn (Blueprint $table) => $table->text('address')->nullable());
        $this->addColumnIfMissing('customers', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillSuppliers(): void
    {
        if (! Schema::hasTable('suppliers')) {
            return;
        }

        $this->addColumnIfMissing('suppliers', 'name', fn (Blueprint $table) => $table->string('name', 120)->nullable());
        $this->addColumnIfMissing('suppliers', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillExpenses(): void
    {
        if (! Schema::hasTable('expenses')) {
            return;
        }

        $this->addColumnIfMissing('expenses', 'title', fn (Blueprint $table) => $table->string('title', 150)->nullable());
        $this->addColumnIfMissing('expenses', 'amount', fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0));
        $this->addColumnIfMissing('expenses', 'expense_date', fn (Blueprint $table) => $table->date('expense_date')->nullable());
        $this->addColumnIfMissing('expenses', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('expenses', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillPurchases(): void
    {
        if (! Schema::hasTable('purchases')) {
            return;
        }

        $this->addColumnIfMissing('purchases', 'purchase_number', fn (Blueprint $table) => $table->string('purchase_number', 40)->nullable());
        $this->addColumnIfMissing('purchases', 'supplier_id', fn (Blueprint $table) => $table->unsignedBigInteger('supplier_id')->nullable());
        $this->addColumnIfMissing('purchases', 'purchase_date', fn (Blueprint $table) => $table->date('purchase_date')->nullable());
        $this->addColumnIfMissing('purchases', 'total_amount', fn (Blueprint $table) => $table->decimal('total_amount', 10, 2)->default(0));
        $this->addColumnIfMissing('purchases', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('purchases', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillPurchaseItems(): void
    {
        if (! Schema::hasTable('purchase_items')) {
            return;
        }

        $this->addColumnIfMissing('purchase_items', 'purchase_id', fn (Blueprint $table) => $table->unsignedBigInteger('purchase_id')->nullable());
        $this->addColumnIfMissing('purchase_items', 'product_id', fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable());
        $this->addColumnIfMissing('purchase_items', 'quantity', fn (Blueprint $table) => $table->integer('quantity')->default(1));
        $this->addColumnIfMissing('purchase_items', 'unit_cost', fn (Blueprint $table) => $table->decimal('unit_cost', 10, 2)->default(0));
        $this->addColumnIfMissing('purchase_items', 'line_total', fn (Blueprint $table) => $table->decimal('line_total', 10, 2)->default(0));
        $this->addColumnIfMissing('purchase_items', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillStockMovements(): void
    {
        if (! Schema::hasTable('stock_movements')) {
            return;
        }

        $this->addColumnIfMissing('stock_movements', 'product_id', fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable());
        $this->addColumnIfMissing('stock_movements', 'type', fn (Blueprint $table) => $table->string('type', 30)->default('adjustment'));
        $this->addColumnIfMissing('stock_movements', 'quantity_change', fn (Blueprint $table) => $table->integer('quantity_change')->default(0));
        $this->addColumnIfMissing('stock_movements', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('stock_movements', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillNotifications(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        $this->addColumnIfMissing('notifications', 'user_id', fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable());
        $this->addColumnIfMissing('notifications', 'type', fn (Blueprint $table) => $table->string('type', 50)->nullable());
        $this->addColumnIfMissing('notifications', 'title', fn (Blueprint $table) => $table->string('title', 255)->nullable());
        $this->addColumnIfMissing('notifications', 'body', fn (Blueprint $table) => $table->text('body')->nullable());
        $this->addColumnIfMissing('notifications', 'link', fn (Blueprint $table) => $table->string('link')->nullable());
        $this->addColumnIfMissing('notifications', 'is_read', fn (Blueprint $table) => $table->boolean('is_read')->default(false));
        $this->addColumnIfMissing('notifications', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillActivityLogs(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        $this->addColumnIfMissing('activity_logs', 'user_id', fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable());
        $this->addColumnIfMissing('activity_logs', 'action', fn (Blueprint $table) => $table->string('action', 100)->nullable());
        $this->addColumnIfMissing('activity_logs', 'description', fn (Blueprint $table) => $table->text('description')->nullable());
        $this->addColumnIfMissing('activity_logs', 'model_type', fn (Blueprint $table) => $table->string('model_type', 50)->nullable());
        $this->addColumnIfMissing('activity_logs', 'model_id', fn (Blueprint $table) => $table->unsignedBigInteger('model_id')->nullable());
        $this->addColumnIfMissing('activity_logs', 'ip_address', fn (Blueprint $table) => $table->string('ip_address', 45)->nullable());
        $this->addColumnIfMissing('activity_logs', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function backfillOrderCollections(): void
    {
        if (! Schema::hasTable('order_collections')) {
            return;
        }

        $this->addColumnIfMissing('order_collections', 'order_id', fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable());
        $this->addColumnIfMissing('order_collections', 'collector_id', fn (Blueprint $table) => $table->unsignedBigInteger('collector_id')->nullable());
        $this->addColumnIfMissing('order_collections', 'collector_name_snapshot', fn (Blueprint $table) => $table->string('collector_name_snapshot', 120)->nullable());
        $this->addColumnIfMissing('order_collections', 'amount', fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0));
        $this->addColumnIfMissing('order_collections', 'note', fn (Blueprint $table) => $table->text('note')->nullable());
        $this->addColumnIfMissing('order_collections', 'created_by', fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable());
        $this->addColumnIfMissing('order_collections', 'deleted_at', fn (Blueprint $table) => $table->softDeletes());
    }

    private function addColumnIfMissing(string $tableName, string $columnName, callable $callback): void
    {
        if (! Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, $callback);
        }
    }
};
