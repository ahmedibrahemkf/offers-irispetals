<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensurePasswordResetTokens();
        $this->ensureUsers();
        $this->ensureSettings();
        $this->ensureProductCategories();
        $this->ensureColors();
        $this->ensureShippingZones();
        $this->ensureExpenseCategories();
        $this->ensureCustomers();
        $this->ensureSuppliers();
        $this->ensureProducts();
        $this->ensureOrders();
        $this->ensureOrderItems();
        $this->ensureOrderStatusLogs();
        $this->ensureInvoices();
        $this->ensureInvoiceItems();
        $this->ensureInvoicePayments();
        $this->ensurePurchases();
        $this->ensurePurchaseItems();
        $this->ensureExpenses();
        $this->ensureEmployeeFinancials();
        $this->ensureStockMovements();
        $this->ensureNotifications();
        $this->ensureActivityLogs();
        $this->ensureCollectors();
        $this->ensureOrderCollections();
    }

    public function down(): void
    {
        // Non-destructive production upgrade migration.
    }

    private function ensurePasswordResetTokens(): void
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table): void {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    private function ensureUsers(): void
    {
        $this->addColumnsIfMissing('users', [
            'name' => fn (Blueprint $table) => $table->string('name'),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'email_verified_at' => fn (Blueprint $table) => $table->timestamp('email_verified_at')->nullable(),
            'password' => fn (Blueprint $table) => $table->string('password'),
            'remember_token' => fn (Blueprint $table) => $table->rememberToken(),
            'username' => fn (Blueprint $table) => $table->string('username', 60)->nullable(),
            'phone' => fn (Blueprint $table) => $table->string('phone', 20)->nullable(),
            'role' => fn (Blueprint $table) => $table->string('role', 20)->default('staff'),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
            'base_salary' => fn (Blueprint $table) => $table->decimal('base_salary', 10, 2)->default(0),
            'hire_date' => fn (Blueprint $table) => $table->date('hire_date')->nullable(),
            'avatar_url' => fn (Blueprint $table) => $table->string('avatar_url')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureSettings(): void
    {
        $this->addColumnsIfMissing('settings', [
            'shop_name' => fn (Blueprint $table) => $table->string('shop_name', 150)->default('Iris Petals'),
            'logo_url' => fn (Blueprint $table) => $table->string('logo_url')->nullable(),
            'invoice_logo_url' => fn (Blueprint $table) => $table->string('invoice_logo_url')->nullable(),
            'primary_color' => fn (Blueprint $table) => $table->string('primary_color', 7)->default('#6D28D9'),
            'address' => fn (Blueprint $table) => $table->text('address')->nullable(),
            'phone' => fn (Blueprint $table) => $table->string('phone', 20)->nullable(),
            'phone_alt' => fn (Blueprint $table) => $table->string('phone_alt', 20)->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email', 150)->nullable(),
            'whatsapp' => fn (Blueprint $table) => $table->string('whatsapp', 20)->nullable(),
            'facebook_url' => fn (Blueprint $table) => $table->string('facebook_url')->nullable(),
            'instagram_url' => fn (Blueprint $table) => $table->string('instagram_url')->nullable(),
            'website_url' => fn (Blueprint $table) => $table->string('website_url')->nullable(),
            'tax_number' => fn (Blueprint $table) => $table->string('tax_number', 50)->nullable(),
            'invoice_header_extra' => fn (Blueprint $table) => $table->text('invoice_header_extra')->nullable(),
            'invoice_footer_text' => fn (Blueprint $table) => $table->text('invoice_footer_text')->nullable(),
            'invoice_terms' => fn (Blueprint $table) => $table->text('invoice_terms')->nullable(),
            'show_tax' => fn (Blueprint $table) => $table->boolean('show_tax')->default(false),
            'tax_rate' => fn (Blueprint $table) => $table->decimal('tax_rate', 5, 2)->default(0),
            'currency' => fn (Blueprint $table) => $table->string('currency', 10)->default('EGP'),
            'currency_symbol' => fn (Blueprint $table) => $table->string('currency_symbol', 5)->default('ج'),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureProductCategories(): void
    {
        $this->addColumnsIfMissing('product_categories', [
            'name' => fn (Blueprint $table) => $table->string('name', 120)->nullable(),
            'notes' => fn (Blueprint $table) => $table->text('notes')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureColors(): void
    {
        $this->addColumnsIfMissing('colors', [
            'name' => fn (Blueprint $table) => $table->string('name', 80)->nullable(),
            'hex_code' => fn (Blueprint $table) => $table->string('hex_code', 7)->default('#FFFFFF'),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureShippingZones(): void
    {
        $this->addColumnsIfMissing('shipping_zones', [
            'name' => fn (Blueprint $table) => $table->string('name', 100)->nullable(),
            'fee' => fn (Blueprint $table) => $table->decimal('fee', 10, 2)->default(0),
            'eta_minutes' => fn (Blueprint $table) => $table->integer('eta_minutes')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureExpenseCategories(): void
    {
        $this->addColumnsIfMissing('expense_categories', [
            'name' => fn (Blueprint $table) => $table->string('name', 120)->nullable(),
            'notes' => fn (Blueprint $table) => $table->text('notes')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureCustomers(): void
    {
        $this->addColumnsIfMissing('customers', [
            'name' => fn (Blueprint $table) => $table->string('name', 120)->nullable(),
            'phone' => fn (Blueprint $table) => $table->string('phone', 20)->nullable(),
            'phone_alt' => fn (Blueprint $table) => $table->string('phone_alt', 20)->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'address' => fn (Blueprint $table) => $table->text('address')->nullable(),
            'location_lat' => fn (Blueprint $table) => $table->decimal('location_lat', 10, 7)->nullable(),
            'location_lng' => fn (Blueprint $table) => $table->decimal('location_lng', 10, 7)->nullable(),
            'notes' => fn (Blueprint $table) => $table->text('notes')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureSuppliers(): void
    {
        $this->addColumnsIfMissing('suppliers', [
            'name' => fn (Blueprint $table) => $table->string('name', 120)->nullable(),
            'phone' => fn (Blueprint $table) => $table->string('phone', 20)->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'address' => fn (Blueprint $table) => $table->text('address')->nullable(),
            'notes' => fn (Blueprint $table) => $table->text('notes')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureProducts(): void
    {
        $this->addColumnsIfMissing('products', [
            'product_category_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_category_id')->nullable(),
            'sku' => fn (Blueprint $table) => $table->string('sku', 50)->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name', 180)->nullable(),
            'description' => fn (Blueprint $table) => $table->text('description')->nullable(),
            'sell_price' => fn (Blueprint $table) => $table->decimal('sell_price', 10, 2)->default(0),
            'cost_price' => fn (Blueprint $table) => $table->decimal('cost_price', 10, 2)->default(0),
            'stock_quantity' => fn (Blueprint $table) => $table->integer('stock_quantity')->default(0),
            'min_stock_alert' => fn (Blueprint $table) => $table->integer('min_stock_alert')->default(0),
            'image_url' => fn (Blueprint $table) => $table->string('image_url')->nullable(),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureOrders(): void
    {
        $this->addColumnsIfMissing('orders', [
            'order_number' => fn (Blueprint $table) => $table->string('order_number', 40)->nullable(),
            'customer_id' => fn (Blueprint $table) => $table->unsignedBigInteger('customer_id')->nullable(),
            'customer_name_snapshot' => fn (Blueprint $table) => $table->string('customer_name_snapshot', 120)->nullable(),
            'customer_phone_snapshot' => fn (Blueprint $table) => $table->string('customer_phone_snapshot', 20)->nullable(),
            'source' => fn (Blueprint $table) => $table->string('source', 40)->default('website'),
            'assigned_staff_id' => fn (Blueprint $table) => $table->unsignedBigInteger('assigned_staff_id')->nullable(),
            'assigned_craftsman_id' => fn (Blueprint $table) => $table->unsignedBigInteger('assigned_craftsman_id')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status', 40)->default('new'),
            'payment_status' => fn (Blueprint $table) => $table->string('payment_status', 40)->default('unpaid'),
            'payment_method' => fn (Blueprint $table) => $table->string('payment_method', 50)->nullable(),
            'amount_total' => fn (Blueprint $table) => $table->decimal('amount_total', 10, 2)->default(0),
            'amount_paid' => fn (Blueprint $table) => $table->decimal('amount_paid', 10, 2)->default(0),
            'amount_remaining' => fn (Blueprint $table) => $table->decimal('amount_remaining', 10, 2)->default(0),
            'discount_amount' => fn (Blueprint $table) => $table->decimal('discount_amount', 10, 2)->default(0),
            'discount_reason' => fn (Blueprint $table) => $table->string('discount_reason')->nullable(),
            'delivery_address' => fn (Blueprint $table) => $table->text('delivery_address')->nullable(),
            'delivery_lat' => fn (Blueprint $table) => $table->decimal('delivery_lat', 10, 7)->nullable(),
            'delivery_lng' => fn (Blueprint $table) => $table->decimal('delivery_lng', 10, 7)->nullable(),
            'delivery_date' => fn (Blueprint $table) => $table->date('delivery_date')->nullable(),
            'delivery_time_slot' => fn (Blueprint $table) => $table->string('delivery_time_slot', 50)->nullable(),
            'delivery_fee' => fn (Blueprint $table) => $table->decimal('delivery_fee', 10, 2)->default(0),
            'notes' => fn (Blueprint $table) => $table->text('notes')->nullable(),
            'internal_notes' => fn (Blueprint $table) => $table->text('internal_notes')->nullable(),
            'occasion' => fn (Blueprint $table) => $table->string('occasion', 100)->nullable(),
            'recipient_name' => fn (Blueprint $table) => $table->string('recipient_name', 100)->nullable(),
            'recipient_phone' => fn (Blueprint $table) => $table->string('recipient_phone', 20)->nullable(),
            'card_message' => fn (Blueprint $table) => $table->text('card_message')->nullable(),
            'images' => fn (Blueprint $table) => $table->json('images')->nullable(),
            'cancel_reason' => fn (Blueprint $table) => $table->text('cancel_reason')->nullable(),
            'cancelled_at' => fn (Blueprint $table) => $table->dateTime('cancelled_at')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureOrderItems(): void
    {
        $this->addColumnsIfMissing('order_items', [
            'order_id' => fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable(),
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable(),
            'color_id' => fn (Blueprint $table) => $table->unsignedBigInteger('color_id')->nullable(),
            'item_name' => fn (Blueprint $table) => $table->string('item_name', 180)->nullable(),
            'quantity' => fn (Blueprint $table) => $table->integer('quantity')->default(1),
            'unit_price' => fn (Blueprint $table) => $table->decimal('unit_price', 10, 2)->default(0),
            'line_total' => fn (Blueprint $table) => $table->decimal('line_total', 10, 2)->default(0),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureOrderStatusLogs(): void
    {
        $this->addColumnsIfMissing('order_status_logs', [
            'order_id' => fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable(),
            'old_status' => fn (Blueprint $table) => $table->string('old_status', 40)->nullable(),
            'new_status' => fn (Blueprint $table) => $table->string('new_status', 40)->nullable(),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'changed_by' => fn (Blueprint $table) => $table->unsignedBigInteger('changed_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureInvoices(): void
    {
        $this->addColumnsIfMissing('invoices', [
            'invoice_number' => fn (Blueprint $table) => $table->string('invoice_number', 40)->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type', 30)->default('order'),
            'order_id' => fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable(),
            'customer_id' => fn (Blueprint $table) => $table->unsignedBigInteger('customer_id')->nullable(),
            'customer_name_snapshot' => fn (Blueprint $table) => $table->string('customer_name_snapshot', 120)->nullable(),
            'payment_status' => fn (Blueprint $table) => $table->string('payment_status', 40)->default('unpaid'),
            'sub_total' => fn (Blueprint $table) => $table->decimal('sub_total', 10, 2)->default(0),
            'discount_amount' => fn (Blueprint $table) => $table->decimal('discount_amount', 10, 2)->default(0),
            'tax_amount' => fn (Blueprint $table) => $table->decimal('tax_amount', 10, 2)->default(0),
            'delivery_fee' => fn (Blueprint $table) => $table->decimal('delivery_fee', 10, 2)->default(0),
            'total_amount' => fn (Blueprint $table) => $table->decimal('total_amount', 10, 2)->default(0),
            'paid_amount' => fn (Blueprint $table) => $table->decimal('paid_amount', 10, 2)->default(0),
            'remaining_amount' => fn (Blueprint $table) => $table->decimal('remaining_amount', 10, 2)->default(0),
            'issued_at' => fn (Blueprint $table) => $table->dateTime('issued_at')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureInvoiceItems(): void
    {
        $this->addColumnsIfMissing('invoice_items', [
            'invoice_id' => fn (Blueprint $table) => $table->unsignedBigInteger('invoice_id')->nullable(),
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable(),
            'item_name' => fn (Blueprint $table) => $table->string('item_name', 180)->nullable(),
            'quantity' => fn (Blueprint $table) => $table->integer('quantity')->default(1),
            'unit_price' => fn (Blueprint $table) => $table->decimal('unit_price', 10, 2)->default(0),
            'line_total' => fn (Blueprint $table) => $table->decimal('line_total', 10, 2)->default(0),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureInvoicePayments(): void
    {
        $this->addColumnsIfMissing('invoice_payments', [
            'invoice_id' => fn (Blueprint $table) => $table->unsignedBigInteger('invoice_id')->nullable(),
            'amount' => fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0),
            'method' => fn (Blueprint $table) => $table->string('method', 50)->nullable(),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'paid_at' => fn (Blueprint $table) => $table->dateTime('paid_at')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensurePurchases(): void
    {
        $this->addColumnsIfMissing('purchases', [
            'purchase_number' => fn (Blueprint $table) => $table->string('purchase_number', 40)->nullable(),
            'supplier_id' => fn (Blueprint $table) => $table->unsignedBigInteger('supplier_id')->nullable(),
            'purchase_date' => fn (Blueprint $table) => $table->date('purchase_date')->nullable(),
            'total_amount' => fn (Blueprint $table) => $table->decimal('total_amount', 10, 2)->default(0),
            'notes' => fn (Blueprint $table) => $table->text('notes')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensurePurchaseItems(): void
    {
        $this->addColumnsIfMissing('purchase_items', [
            'purchase_id' => fn (Blueprint $table) => $table->unsignedBigInteger('purchase_id')->nullable(),
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable(),
            'quantity' => fn (Blueprint $table) => $table->integer('quantity')->default(1),
            'unit_cost' => fn (Blueprint $table) => $table->decimal('unit_cost', 10, 2)->default(0),
            'line_total' => fn (Blueprint $table) => $table->decimal('line_total', 10, 2)->default(0),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureExpenses(): void
    {
        $this->addColumnsIfMissing('expenses', [
            'expense_category_id' => fn (Blueprint $table) => $table->unsignedBigInteger('expense_category_id')->nullable(),
            'title' => fn (Blueprint $table) => $table->string('title', 150)->nullable(),
            'amount' => fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0),
            'expense_date' => fn (Blueprint $table) => $table->date('expense_date')->nullable(),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureEmployeeFinancials(): void
    {
        $this->addColumnsIfMissing('employee_financials', [
            'employee_id' => fn (Blueprint $table) => $table->unsignedBigInteger('employee_id')->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type', 30)->default('salary'),
            'amount' => fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0),
            'effective_date' => fn (Blueprint $table) => $table->date('effective_date')->nullable(),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureStockMovements(): void
    {
        $this->addColumnsIfMissing('stock_movements', [
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type', 30)->default('adjustment'),
            'quantity_change' => fn (Blueprint $table) => $table->integer('quantity_change')->default(0),
            'reference_type' => fn (Blueprint $table) => $table->string('reference_type', 50)->nullable(),
            'reference_id' => fn (Blueprint $table) => $table->unsignedBigInteger('reference_id')->nullable(),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureNotifications(): void
    {
        $this->addColumnsIfMissing('notifications', [
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable(),
            'type' => fn (Blueprint $table) => $table->string('type', 50)->nullable(),
            'title' => fn (Blueprint $table) => $table->string('title', 255)->nullable(),
            'body' => fn (Blueprint $table) => $table->text('body')->nullable(),
            'link' => fn (Blueprint $table) => $table->string('link')->nullable(),
            'is_read' => fn (Blueprint $table) => $table->boolean('is_read')->default(false),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureActivityLogs(): void
    {
        $this->addColumnsIfMissing('activity_logs', [
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable(),
            'action' => fn (Blueprint $table) => $table->string('action', 100)->nullable(),
            'description' => fn (Blueprint $table) => $table->text('description')->nullable(),
            'model_type' => fn (Blueprint $table) => $table->string('model_type', 50)->nullable(),
            'model_id' => fn (Blueprint $table) => $table->unsignedBigInteger('model_id')->nullable(),
            'ip_address' => fn (Blueprint $table) => $table->string('ip_address', 45)->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureCollectors(): void
    {
        $this->addColumnsIfMissing('collectors', [
            'name' => fn (Blueprint $table) => $table->string('name', 120)->nullable(),
            'phone' => fn (Blueprint $table) => $table->string('phone', 20)->nullable(),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function ensureOrderCollections(): void
    {
        $this->addColumnsIfMissing('order_collections', [
            'order_id' => fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable(),
            'collector_id' => fn (Blueprint $table) => $table->unsignedBigInteger('collector_id')->nullable(),
            'collector_name_snapshot' => fn (Blueprint $table) => $table->string('collector_name_snapshot', 120)->nullable(),
            'amount' => fn (Blueprint $table) => $table->decimal('amount', 10, 2)->default(0),
            'note' => fn (Blueprint $table) => $table->text('note')->nullable(),
            'created_by' => fn (Blueprint $table) => $table->unsignedBigInteger('created_by')->nullable(),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $table) => $table->softDeletes(),
        ]);
    }

    private function addColumnsIfMissing(string $tableName, array $definitions): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        foreach ($definitions as $columnName => $definition) {
            if (! Schema::hasColumn($tableName, $columnName)) {
                Schema::table($tableName, function (Blueprint $table) use ($definition): void {
                    $definition($table);
                });
            }
        }
    }
};
