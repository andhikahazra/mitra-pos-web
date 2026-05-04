<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah tanggal di tabel transaksi menjadi dateTime agar mencatat jam
        Schema::table('transaksi', function (Blueprint $blueprint) {
            $blueprint->dateTime('tanggal')->change();
        });

        // Ubah tanggal di tabel barang_masuk menjadi dateTime
        Schema::table('barang_masuk', function (Blueprint $blueprint) {
            $blueprint->dateTime('tanggal_pesan')->change();
            $blueprint->dateTime('tanggal_terima')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $blueprint) {
            $blueprint->date('tanggal')->change();
        });

        Schema::table('barang_masuk', function (Blueprint $blueprint) {
            $blueprint->date('tanggal_pesan')->change();
            $blueprint->date('tanggal_terima')->nullable()->change();
        });
    }
};
