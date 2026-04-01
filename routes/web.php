<?php

use App\Http\Controllers\Admin\ActivityLogsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CraftsmanController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EntryController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicOrderController::class, 'show'])->name('home');

Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.password.forgot');
    Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('admin.password.send-otp');
    Route::get('/otp', [AuthController::class, 'showOtp'])->name('admin.password.otp');
    Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('admin.password.verify-otp');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('admin.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');

    Route::middleware('admin.auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('/', [EntryController::class, 'adminRoot'])->name('admin.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:owner,manager')->name('admin.dashboard.home');

        Route::get('/orders', [OrdersController::class, 'index'])->middleware('role:owner,manager,staff')->name('admin.orders.index');
        Route::get('/orders/create', [OrdersController::class, 'create'])->middleware(['role:owner,manager,staff', 'ability:create'])->name('admin.orders.create');
        Route::post('/orders', [OrdersController::class, 'store'])->middleware(['role:owner,manager,staff', 'ability:create'])->name('admin.orders.store');
        Route::post('/orders/create', [OrdersController::class, 'store'])->middleware(['role:owner,manager,staff', 'ability:create'])->name('admin.orders.store.legacy');
        Route::get('/orders/{order}', [OrdersController::class, 'show'])->middleware('role:owner,manager,staff')->name('admin.orders.show');
        Route::get('/orders/{order}/edit', [OrdersController::class, 'edit'])->middleware(['role:owner,manager,staff', 'ability:update'])->name('admin.orders.edit');
        Route::put('/orders/{order}', [OrdersController::class, 'update'])->middleware(['role:owner,manager,staff', 'ability:update'])->name('admin.orders.update');
        Route::delete('/orders/{order}', [OrdersController::class, 'destroy'])->middleware(['role:owner,manager,staff', 'ability:delete'])->name('admin.orders.destroy');

        Route::get('/invoices', [InvoicesController::class, 'index'])->middleware('role:owner,manager,staff')->name('admin.invoices.index');
        Route::post('/invoices/direct', [InvoicesController::class, 'storeDirectSale'])->middleware(['role:owner,manager,staff', 'ability:create'])->name('admin.invoices.direct');
        Route::post('/invoices/from-order/{order}', [InvoicesController::class, 'createFromOrder'])->middleware(['role:owner,manager,staff', 'ability:create'])->name('admin.invoices.from-order');
        Route::get('/invoices/{invoice}', [InvoicesController::class, 'show'])->middleware('role:owner,manager,staff,viewer')->name('admin.invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoicesController::class, 'print'])->middleware('role:owner,manager,staff,viewer')->name('admin.invoices.print');
        Route::post('/invoices/{invoice}/payments', [InvoicesController::class, 'addPayment'])->middleware(['role:owner,manager,staff', 'ability:update'])->name('admin.invoices.payments.store');

        Route::get('/products', [CatalogController::class, 'products'])->middleware('role:owner,manager')->name('admin.products.index');
        Route::post('/products', [CatalogController::class, 'storeProduct'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.products.store');
        Route::get('/products/{product}', [CatalogController::class, 'showProduct'])->middleware('role:owner,manager')->name('admin.products.show');
        Route::post('/products/{product}/stock', [CatalogController::class, 'adjustStock'])->middleware(['role:owner,manager', 'ability:update'])->name('admin.products.stock.adjust');

        Route::get('/suppliers', [CatalogController::class, 'suppliers'])->middleware('role:owner,manager')->name('admin.suppliers.index');
        Route::post('/suppliers', [CatalogController::class, 'storeSupplier'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.suppliers.store');

        Route::get('/purchases', [CatalogController::class, 'purchases'])->middleware('role:owner,manager')->name('admin.purchases.index');
        Route::post('/purchases', [CatalogController::class, 'storePurchase'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.purchases.store');

        Route::get('/expenses', [FinanceController::class, 'expenses'])->middleware('role:owner,manager')->name('admin.expenses.index');
        Route::post('/expenses', [FinanceController::class, 'storeExpense'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.expenses.store');

        Route::get('/employees', [FinanceController::class, 'employees'])->middleware('role:owner,manager')->name('admin.employees.index');
        Route::post('/employees', [FinanceController::class, 'storeEmployee'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.employees.store');
        Route::post('/employees/payroll/monthly', [FinanceController::class, 'storeMonthlyPayroll'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.employees.payroll.monthly');
        Route::get('/employees/{employee}', [FinanceController::class, 'showEmployee'])->middleware('role:owner,manager')->name('admin.employees.show');
        Route::put('/employees/{employee}/permissions', [FinanceController::class, 'updateEmployeePermissions'])->middleware(['role:owner,manager', 'ability:update'])->name('admin.employees.permissions.update');
        Route::post('/employees/financials', [FinanceController::class, 'storeEmployeeFinancial'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.employees.financials.store');

        Route::get('/customers', [CustomersController::class, 'index'])->middleware('role:owner,manager,staff')->name('admin.customers.index');
        Route::post('/customers', [CustomersController::class, 'store'])->middleware(['role:owner,manager,staff', 'ability:create'])->name('admin.customers.store');
        Route::get('/customers/{customer}', [CustomersController::class, 'show'])->middleware('role:owner,manager,staff')->name('admin.customers.show');

        Route::get('/reports', [ReportsController::class, 'index'])->middleware('role:owner,manager,viewer')->name('admin.reports.index');

        Route::get('/settings', [SettingsController::class, 'index'])->middleware('role:owner,manager')->name('admin.settings.index');
        Route::post('/settings/main', [SettingsController::class, 'updateMain'])->middleware(['role:owner,manager', 'ability:update'])->name('admin.settings.main.update');
        Route::post('/settings/zones', [SettingsController::class, 'storeZone'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.settings.zones.store');
        Route::post('/settings/colors', [SettingsController::class, 'storeColor'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.settings.colors.store');
        Route::post('/settings/product-categories', [SettingsController::class, 'storeProductCategory'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.settings.product-categories.store');
        Route::post('/settings/expense-categories', [SettingsController::class, 'storeExpenseCategory'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.settings.expense-categories.store');
        Route::post('/settings/collectors', [SettingsController::class, 'storeCollector'])->middleware(['role:owner,manager', 'ability:create'])->name('admin.settings.collectors.store');
        Route::put('/settings/collectors/{collector}', [SettingsController::class, 'updateCollector'])->middleware(['role:owner,manager', 'ability:update'])->name('admin.settings.collectors.update');
        Route::delete('/settings/collectors/{collector}', [SettingsController::class, 'destroyCollector'])->middleware(['role:owner,manager', 'ability:delete'])->name('admin.settings.collectors.destroy');

        Route::get('/notifications', [NotificationsController::class, 'index'])->name('admin.notifications.index');
        Route::post('/notifications/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('admin.notifications.mark-all-read');

        Route::get('/activity-logs', [ActivityLogsController::class, 'index'])->middleware('role:owner')->name('admin.activity.index');
    });
});

Route::middleware(['admin.auth', 'role:craftsman'])->group(function (): void {
    Route::get('/craftsman', [CraftsmanController::class, 'tasks'])->name('craftsman.tasks');
    Route::post('/craftsman/orders/{order}/status', [CraftsmanController::class, 'updateTaskStatus'])->name('craftsman.orders.status');
});

Route::middleware(['admin.auth', 'role:staff,owner,manager'])->group(function (): void {
    Route::get('/staff/orders', [StaffController::class, 'orders'])->name('staff.orders');
});

Route::middleware('admin.auth')->group(function (): void {
    Route::get('/dashboard', [EntryController::class, 'legacyDashboard'])->name('legacy.dashboard');
    Route::get('/orders', [EntryController::class, 'legacyOrders'])->name('legacy.orders');
});

Route::middleware(['admin.auth', 'role:owner,manager,viewer'])->group(function (): void {
    Route::get('/reports', [EntryController::class, 'legacyReports'])->name('legacy.reports');
});

Route::get('/order', [PublicOrderController::class, 'show'])->name('public.order.show');
Route::post('/order', [PublicOrderController::class, 'submit'])->name('public.order.submit');
