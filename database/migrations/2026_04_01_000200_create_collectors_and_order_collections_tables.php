<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('collectors')) {
            Schema::create('collectors', function (Blueprint $table): void {
                $table->id();
                $table->string('name', 120);
                $table->string('phone', 20)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('order_collections')) {
            Schema::create('order_collections', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('collector_id')->nullable()->constrained('collectors')->nullOnDelete();
                $table->string('collector_name_snapshot', 120);
                $table->decimal('amount', 10, 2)->default(0);
                $table->text('note')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['order_id', 'collector_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_collections');
        Schema::dropIfExists('collectors');
    }
};
