<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('rata_penjualan', 15, 2);
            $table->decimal('standar_deviasi', 15, 2);
            $table->decimal('lead_time', 10, 2);
            $table->integer('safety_stock');
            $table->integer('reorder_point');
            $table->integer('periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rop');
    }
};
