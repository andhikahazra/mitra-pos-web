<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('detail_barang_masuk_id')->constrained('detail_barang_masuk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('qty_sisa');
            $table->decimal('harga_beli', 15, 2);
            $table->date('tanggal_masuk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_batch');
    }
};
