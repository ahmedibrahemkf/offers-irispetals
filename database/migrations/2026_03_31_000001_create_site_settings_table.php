<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary();
            $table->timestamp('updated_at')->useCurrent();
            $table->json('payload');
        });

        DB::table('site_settings')->insert([
            'id' => 1,
            'updated_at' => now(),
            'payload' => json_encode(new stdClass()),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
