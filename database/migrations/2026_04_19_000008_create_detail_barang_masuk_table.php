<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_masuk_id')->constrained('barang_masuk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga', 15, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_barang_masuk');
    }
};
