<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->makeLegacyPayloadNullable('orders');
        $this->makeLegacyPayloadNullable('expenses');
        $this->makeLegacyPayloadNullable('site_settings');

        $this->alignOrderRelationKeyTypes();
    }

    public function down(): void
    {
        // Non-destructive production migration.
    }

    private function makeLegacyPayloadNullable(string $table): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'payload')) {
            return;
        }

        $column = DB::selectOne(
            'SELECT COLUMN_TYPE, IS_NULLABLE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, 'payload']
        );

        if (! $column) {
            return;
        }

        if (strtoupper((string) $column->IS_NULLABLE) === 'YES') {
            return;
        }

        $columnType = (string) $column->COLUMN_TYPE;
        if ($columnType === '') {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` MODIFY `payload` {$columnType} NULL");
    }

    private function alignOrderRelationKeyTypes(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'id')) {
            return;
        }

        $ordersIdMeta = DB::selectOne(
            'SELECT COLUMN_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['orders', 'id']
        );

        $ordersIdType = strtolower((string) ($ordersIdMeta->COLUMN_TYPE ?? ''));
        if ($ordersIdType === '') {
            return;
        }

        $ordersIdIsCharacter = str_contains($ordersIdType, 'char') || str_contains($ordersIdType, 'text');
        if (! $ordersIdIsCharacter) {
            return;
        }

        foreach ($this->orderReferenceColumns() as [$table, $column]) {
            $this->convertColumnToVarcharIfNeeded($table, $column);
        }
    }

    private function orderReferenceColumns(): array
    {
        return [
            ['order_items', 'order_id'],
            ['order_status_logs', 'order_id'],
            ['order_collections', 'order_id'],
            ['invoices', 'order_id'],
        ];
    }

    private function convertColumnToVarcharIfNeeded(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $meta = DB::selectOne(
            'SELECT COLUMN_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );

        $columnType = strtolower((string) ($meta->COLUMN_TYPE ?? ''));
        if ($columnType === '') {
            return;
        }

        if (str_contains($columnType, 'char') || str_contains($columnType, 'text')) {
            return;
        }

        $this->dropForeignKeyConstraintsForColumn($table, $column);
        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(64) NULL");
    }

    private function dropForeignKeyConstraintsForColumn(string $table, string $column): void
    {
        $constraints = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        foreach ($constraints as $constraint) {
            $name = (string) ($constraint->CONSTRAINT_NAME ?? '');
            if ($name === '') {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$name}`");
        }
    }
};
