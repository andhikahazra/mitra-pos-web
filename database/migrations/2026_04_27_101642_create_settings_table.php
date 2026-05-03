<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('settings');
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko')->nullable();
            $table->json('alamat_toko')->nullable(); // Diubah jadi JSON
            $table->string('no_hp')->nullable();
            $table->json('deskripsi')->nullable();   // Diubah jadi JSON
            $table->decimal('biaya_admin_qris', 15, 2)->default(0);
            $table->json('rekening_bank')->nullable();
            $table->text('footer_nota')->nullable();
            $table->timestamps();
        });

        // Masukkan data awal agar tidak kosong
        DB::table('settings')->insert([
            'nama_toko' => 'MitraPOS Toko Kita',
            'alamat_toko' => json_encode([
                'jalan' => '',
                'kelurahan' => '',
                'kecamatan' => '',
                'kota' => '',
                'provinsi' => ''
            ]),
            'deskripsi' => json_encode([
                'slogan' => '',
                'keterangan' => ''
            ]),
            'biaya_admin_qris' => 0,
            'rekening_bank' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
