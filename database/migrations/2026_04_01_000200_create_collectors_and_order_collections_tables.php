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
                // Keep compatibility with legacy schemas where key types differ.
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('collector_id')->nullable();
                $table->string('collector_name_snapshot', 120);
                $table->decimal('amount', 10, 2)->default(0);
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['order_id', 'collector_id']);
                $table->index('created_by');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_collections');
        Schema::dropIfExists('collectors');
    }
};
