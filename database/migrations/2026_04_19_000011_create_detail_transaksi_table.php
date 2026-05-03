<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('batch_id')->constrained('stok_batch')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga', 15, 2);
            $table->decimal('harga_modal', 15, 2);
            $table->decimal('subtotal', 15, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
