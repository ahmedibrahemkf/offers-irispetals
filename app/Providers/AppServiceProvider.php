<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::creating(function (Model $model): void {
            if ($model->getKeyName() !== 'id') {
                return;
            }

            if ($model->getAttribute('id') !== null && $model->getAttribute('id') !== '') {
                return;
            }

            $table = $model->getTable();
            static $tableHasIdCache = [];

            if (! array_key_exists($table, $tableHasIdCache)) {
                $tableHasIdCache[$table] = Schema::hasTable($table) && Schema::hasColumn($table, 'id');
            }

            if (! $tableHasIdCache[$table]) {
                return;
            }

            static $tableMetadata = [];

            if (! array_key_exists($table, $tableMetadata)) {
                $column = DB::selectOne(
                    'SELECT COLUMN_TYPE, EXTRA
                     FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$table, 'id']
                );

                $columnType = strtolower((string) ($column->COLUMN_TYPE ?? ''));
                $extra = strtolower((string) ($column->EXTRA ?? ''));

                $tableMetadata[$table] = [
                    'integer' => str_contains($columnType, 'int'),
                    'auto_increment' => str_contains($extra, 'auto_increment'),
                ];
            }

            $meta = $tableMetadata[$table];

            if (! $meta['integer'] || $meta['auto_increment']) {
                return;
            }

            $maxId = (int) DB::table($table)->max('id');
            $model->setAttribute('id', max(1, $maxId + 1));
        });
    }
}
