<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        if (! Schema::hasColumn('settings', 'order_required_fields')) {
            Schema::table('settings', function (Blueprint $table): void {
                $table->json('order_required_fields')->nullable()->after('currency_symbol');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        if (Schema::hasColumn('settings', 'order_required_fields')) {
            Schema::table('settings', function (Blueprint $table): void {
                $table->dropColumn('order_required_fields');
            });
        }
    }
};
