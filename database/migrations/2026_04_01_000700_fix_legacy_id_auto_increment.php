<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users',
            'settings',
            'product_categories',
            'colors',
            'shipping_zones',
            'expense_categories',
            'customers',
            'suppliers',
            'products',
            'orders',
            'order_items',
            'order_status_logs',
            'invoices',
            'invoice_items',
            'invoice_payments',
            'purchases',
            'purchase_items',
            'expenses',
            'employee_financials',
            'stock_movements',
            'notifications',
            'activity_logs',
            'collectors',
            'order_collections',
        ];

        foreach ($tables as $table) {
            $this->ensureIdIsAutoIncrement($table);
        }
    }

    public function down(): void
    {
        // Non-destructive migration.
    }

    private function ensureIdIsAutoIncrement(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
            return;
        }

        $column = DB::selectOne(
            'SELECT COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, 'id']
        );

        if (! $column) {
            return;
        }

        $columnType = strtolower((string) $column->COLUMN_TYPE);
        $extra = strtolower((string) $column->EXTRA);
        $columnKey = strtolower((string) $column->COLUMN_KEY);

        if (! str_contains($columnType, 'int')) {
            return;
        }

        if (str_contains($extra, 'auto_increment')) {
            return;
        }

        $baseType = $this->resolveIntegerBaseType($columnType);
        if ($baseType === null) {
            return;
        }

        $unsigned = str_contains($columnType, 'unsigned') ? ' UNSIGNED' : '';

        if ($columnKey !== 'pri') {
            $primaryIndex = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = 'PRIMARY'");
            if (count($primaryIndex) === 0) {
                DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`id`)");
            }
        }

        DB::statement("ALTER TABLE `{$table}` MODIFY `id` {$baseType}{$unsigned} NOT NULL AUTO_INCREMENT");
    }

    private function resolveIntegerBaseType(string $columnType): ?string
    {
        return match (true) {
            str_starts_with($columnType, 'tinyint') => 'TINYINT',
            str_starts_with($columnType, 'smallint') => 'SMALLINT',
            str_starts_with($columnType, 'mediumint') => 'MEDIUMINT',
            str_starts_with($columnType, 'int') => 'INT',
            str_starts_with($columnType, 'bigint') => 'BIGINT',
            default => null,
        };
    }
};
