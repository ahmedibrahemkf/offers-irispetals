<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Event::listen('eloquent.creating: *', function (string $eventName, array $payload): void {
            $model = $payload[0] ?? null;
            if (! $model instanceof Model) {
                return;
            }

            $this->hydrateLegacyIdIfNeeded($model);
            $this->hydrateLegacyPayloadIfNeeded($model);
        });
    }

    private function hydrateLegacyIdIfNeeded(Model $model): void
    {
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
            $length = null;
            if (preg_match('/\((\d+)\)/', $columnType, $matches) === 1) {
                $length = (int) $matches[1];
            }

            $tableMetadata[$table] = [
                'integer' => str_contains($columnType, 'int'),
                'character' => str_contains($columnType, 'char'),
                'length' => $length,
                'auto_increment' => str_contains($extra, 'auto_increment'),
            ];
        }

        $meta = $tableMetadata[$table];

        if ($meta['auto_increment']) {
            return;
        }

        if ($meta['integer']) {
            $maxId = (int) DB::table($table)->max('id');
            $model->setAttribute('id', max(1, $maxId + 1));

            return;
        }

        if ($meta['character']) {
            $generatedId = (string) Str::ulid();
            $maxLength = $meta['length'];
            if (is_int($maxLength) && $maxLength > 0 && strlen($generatedId) > $maxLength) {
                $generatedId = substr($generatedId, 0, $maxLength);
            }

            $model->setAttribute('id', $generatedId);
        }

    }

    private function hydrateLegacyPayloadIfNeeded(Model $model): void
    {
        $table = $model->getTable();
        static $payloadMetaCache = [];

        if (! array_key_exists($table, $payloadMetaCache)) {
            $payloadMetaCache[$table] = null;

            if (Schema::hasTable($table) && Schema::hasColumn($table, 'payload')) {
                $column = DB::selectOne(
                    'SELECT DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                     FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$table, 'payload']
                );

                if ($column) {
                    $isNullable = strtoupper((string) $column->IS_NULLABLE) === 'YES';
                    $hasDefault = $column->COLUMN_DEFAULT !== null;
                    $payloadMetaCache[$table] = [
                        'needs_value' => ! $isNullable && ! $hasDefault,
                    ];
                }
            }
        }

        $meta = $payloadMetaCache[$table];
        if (! is_array($meta) || ! ($meta['needs_value'] ?? false)) {
            return;
        }

        $current = $model->getAttribute('payload');
        if ($current !== null && $current !== '') {
            return;
        }

        $model->setAttribute('payload', json_encode([
            'source' => 'laravel-crm',
            'model' => class_basename($model),
            'created_at' => now()->toDateTimeString(),
            'data' => $this->compactPayloadData($model->getAttributes()),
        ], JSON_UNESCAPED_UNICODE));
    }

    private function compactPayloadData(array $attributes): array
    {
        $blocked = ['password', 'remember_token'];
        $result = [];

        foreach ($attributes as $key => $value) {
            if (in_array((string) $key, $blocked, true)) {
                continue;
            }

            if (is_scalar($value) || $value === null) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
