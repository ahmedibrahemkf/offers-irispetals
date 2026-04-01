<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeAllPayloadColumns();
        $this->normalizeDeletedAtColumns();
    }

    public function down(): void
    {
        // Non-destructive migration.
    }

    private function normalizeAllPayloadColumns(): void
    {
        $rows = DB::select(
            "SELECT TABLE_NAME, COLUMN_TYPE, IS_NULLABLE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME = 'payload'"
        );

        foreach ($rows as $row) {
            $table = (string) ($row->TABLE_NAME ?? '');
            $columnType = (string) ($row->COLUMN_TYPE ?? '');
            $isNullable = strtoupper((string) ($row->IS_NULLABLE ?? ''));

            if ($table === '' || $columnType === '' || $isNullable === 'YES') {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `payload` {$columnType} NULL");
        }
    }

    private function normalizeDeletedAtColumns(): void
    {
        $rows = DB::select(
            "SELECT TABLE_NAME, COLUMN_TYPE, IS_NULLABLE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME = 'deleted_at'"
        );

        foreach ($rows as $row) {
            $table = (string) ($row->TABLE_NAME ?? '');
            $columnType = (string) ($row->COLUMN_TYPE ?? '');
            $isNullable = strtoupper((string) ($row->IS_NULLABLE ?? ''));

            if ($table === '' || $columnType === '' || $isNullable === 'YES') {
                continue;
            }

            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY `deleted_at` {$columnType} NULL");
        }
    }
};
