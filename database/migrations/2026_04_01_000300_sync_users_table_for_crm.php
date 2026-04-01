<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 60)->nullable();
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable();
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('staff');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (! Schema::hasColumn('users', 'base_salary')) {
                $table->decimal('base_salary', 10, 2)->default(0);
            }

            if (! Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }

            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable();
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty to avoid destructive rollback on production data.
    }
};
