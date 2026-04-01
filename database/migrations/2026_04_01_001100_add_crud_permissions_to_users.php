<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'can_create_records')) {
                $table->boolean('can_create_records')->nullable()->after('is_active');
            }

            if (! Schema::hasColumn('users', 'can_update_records')) {
                $table->boolean('can_update_records')->nullable()->after('can_create_records');
            }

            if (! Schema::hasColumn('users', 'can_delete_records')) {
                $table->boolean('can_delete_records')->nullable()->after('can_update_records');
            }
        });

        DB::table('users')
            ->whereIn('role', ['owner', 'manager'])
            ->where(function ($q): void {
                $q->whereNull('can_create_records')
                    ->orWhereNull('can_update_records')
                    ->orWhereNull('can_delete_records');
            })
            ->update([
                'can_create_records' => 1,
                'can_update_records' => 1,
                'can_delete_records' => 1,
            ]);

        DB::table('users')
            ->where('role', 'staff')
            ->where(function ($q): void {
                $q->whereNull('can_create_records')
                    ->orWhereNull('can_update_records')
                    ->orWhereNull('can_delete_records');
            })
            ->update([
                'can_create_records' => 1,
                'can_update_records' => 1,
                'can_delete_records' => 0,
            ]);

        DB::table('users')
            ->whereNotIn('role', ['owner', 'manager', 'staff'])
            ->where(function ($q): void {
                $q->whereNull('can_create_records')
                    ->orWhereNull('can_update_records')
                    ->orWhereNull('can_delete_records');
            })
            ->update([
                'can_create_records' => 0,
                'can_update_records' => 0,
                'can_delete_records' => 0,
            ]);
    }

    public function down(): void
    {
        // Non-destructive rollback on production.
    }
};

