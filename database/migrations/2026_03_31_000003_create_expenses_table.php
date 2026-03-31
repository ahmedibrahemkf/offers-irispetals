<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            $table->string('id', 80)->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->json('payload');

            $table->index('created_at', 'idx_expenses_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
