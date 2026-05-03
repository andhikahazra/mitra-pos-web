<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_dimensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->unique()->constrained('produk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('panjang', 12, 2);
            $table->decimal('lebar', 12, 2);
            $table->decimal('tinggi', 12, 2);
            $table->decimal('volume', 15, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_dimensi');
    }
};
