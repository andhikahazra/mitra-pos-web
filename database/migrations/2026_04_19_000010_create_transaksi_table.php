<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_pelanggan')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('total_harga', 15, 2);
            $table->string('metode_pembayaran');
            $table->string('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
