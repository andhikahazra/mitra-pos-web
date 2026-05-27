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
        // 1. Tambahkan kolom baru ke tabel produk
        Schema::table('produk', function (Blueprint $table) {
            $table->decimal('panjang', 12, 2)->nullable()->after('status');
            $table->decimal('lebar', 12, 2)->nullable()->after('panjang');
            $table->decimal('tinggi', 12, 2)->nullable()->after('lebar');
            $table->decimal('volume', 15, 2)->nullable()->after('tinggi');
            $table->string('foto')->nullable()->after('volume');
        });

        // 2. Salin data dari produk_dimensi ke tabel produk
        if (Schema::hasTable('produk_dimensi')) {
            $dimensions = DB::table('produk_dimensi')->get();
            foreach ($dimensions as $dim) {
                DB::table('produk')->where('id', $dim->produk_id)->update([
                    'panjang' => $dim->panjang,
                    'lebar' => $dim->lebar,
                    'tinggi' => $dim->tinggi,
                    'volume' => $dim->volume,
                ]);
            }
        }

        // 3. Salin data foto utama dari produk_foto ke tabel produk
        if (Schema::hasTable('produk_foto')) {
            $photos = DB::table('produk_foto')
                ->orderBy('is_primary', 'desc')
                ->orderBy('id', 'asc')
                ->get()
                ->groupBy('produk_id');

            foreach ($photos as $produkId => $prodPhotos) {
                $primaryPhoto = $prodPhotos->first();
                if ($primaryPhoto) {
                    DB::table('produk')->where('id', $produkId)->update([
                        'foto' => $primaryPhoto->path,
                    ]);
                }
            }
        }

        // 4. Hapus tabel lama
        Schema::dropIfExists('produk_dimensi');
        Schema::dropIfExists('produk_foto');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Buat kembali tabel produk_dimensi
        Schema::create('produk_dimensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->unique()->constrained('produk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('panjang', 12, 2);
            $table->decimal('lebar', 12, 2);
            $table->decimal('tinggi', 12, 2);
            $table->decimal('volume', 15, 2);
        });

        // 2. Buat kembali tabel produk_foto
        Schema::create('produk_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
        });

        // 3. Salin kembali data dari produk ke tabel lama sebelum kolom dihapus
        $products = DB::table('produk')->get();
        foreach ($products as $prod) {
            if ($prod->panjang !== null || $prod->lebar !== null || $prod->tinggi !== null || $prod->volume !== null) {
                DB::table('produk_dimensi')->insert([
                    'produk_id' => $prod->id,
                    'panjang' => $prod->panjang ?? 0,
                    'lebar' => $prod->lebar ?? 0,
                    'tinggi' => $prod->tinggi ?? 0,
                    'volume' => $prod->volume ?? 0,
                ]);
            }

            if ($prod->foto !== null) {
                DB::table('produk_foto')->insert([
                    'produk_id' => $prod->id,
                    'path' => $prod->foto,
                    'is_primary' => true,
                ]);
            }
        }

        // 4. Hapus kolom baru dari tabel produk
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['panjang', 'lebar', 'tinggi', 'volume', 'foto']);
        });
    }
};
