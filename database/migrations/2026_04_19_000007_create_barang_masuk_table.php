<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->date('tanggal_pesan');
            $table->date('tanggal_terima')->nullable();
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('foto_struk')->nullable();
            $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};
