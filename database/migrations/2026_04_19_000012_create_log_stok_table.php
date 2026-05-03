<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('tipe');
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('barang_masuk_id')->nullable()->constrained('barang_masuk')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_stok');
    }
};
