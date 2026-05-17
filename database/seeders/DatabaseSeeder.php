<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\Produk;
use App\Models\BarangMasuk;
use App\Models\DetailBarangMasuk;
use App\Models\StokBatch;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\LogStok;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai pembersihan database (Wipe)...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('detail_transaksi')->truncate();
        DB::table('transaksi')->truncate();
        DB::table('log_stok')->truncate();
        DB::table('stok_batch')->truncate();
        DB::table('detail_barang_masuk')->truncate();
        DB::table('barang_masuk')->truncate();
        DB::table('rop')->truncate();
        DB::table('produk')->truncate();
        DB::table('kategori')->truncate();
        DB::table('supplier')->truncate();
        // Hanya hapus jika mau benar-benar bersih, tapi kita pastikan minimal hapus admin lama
        DB::table('users')->whereIn('email', ['pemilik@mitrapos.com', 'karyawan@mitrapos.com', 'owner@mitrapos.com'])->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Membangun Master Data (Owner, Karyawan, 20 Produk)...');

        // 1. Akun Autentik
        $owner = User::firstOrCreate(
            ['email' => 'pemilik@mitrapos.com'],
            ['nama' => 'Pemilik', 'password' => bcrypt('password'), 'role' => User::ROLE_PEMILIK]
        );

        $karyawan = User::firstOrCreate(
            ['email' => 'karyawan@mitrapos.com'],
            ['nama' => 'Karyawan', 'password' => bcrypt('password'), 'role' => User::ROLE_KARYAWAN]
        );

        // 2. Kategori Tepat Sasaran
        $katKardus = Kategori::create(['nama' => 'Kardus']);
        $katBubble = Kategori::create(['nama' => 'Bubble Wrap']);
        $katAlat = Kategori::create(['nama' => 'Alat Packing']);
        $katKarung = Kategori::create(['nama' => 'Karung Plastik']);
        $katLakban = Kategori::create(['nama' => 'Lakban']);

        // 3. Supplier
        $sup1 = Supplier::create(['nama' => 'PT Kemasan Nusantara', 'no_telp' => '081234567890', 'alamat' => 'Kawasan Industri Cikarang']);
        $sup2 = Supplier::create(['nama' => 'CV Mitra Plastik', 'no_telp' => '081298765432', 'alamat' => 'Kopo, Bandung']);

        // 4. 20 Produk Varian
        $productsData = [
            // KARDUS (High - Med Demand)
            ['nama' => 'Kardus Polos 20x20x20', 'sku' => 'KRD-202020', 'kat' => $katKardus->id, 'hrg' => 2500, 'demand' => 'high'],
            ['nama' => 'Kardus Polos 30x30x30', 'sku' => 'KRD-303030', 'kat' => $katKardus->id, 'hrg' => 4500, 'demand' => 'high'],
            ['nama' => 'Kardus Polos 15x10x10', 'sku' => 'KRD-151010', 'kat' => $katKardus->id, 'hrg' => 1500, 'demand' => 'med'],
            ['nama' => 'Kardus Sepatu Pria', 'sku' => 'KRD-SPT-P', 'kat' => $katKardus->id, 'hrg' => 3000, 'demand' => 'med'],
            ['nama' => 'Kardus Sepatu Wanita', 'sku' => 'KRD-SPT-W', 'kat' => $katKardus->id, 'hrg' => 2800, 'demand' => 'med'],
            ['nama' => 'Kardus Die Cut Kecil', 'sku' => 'KRD-DC-S', 'kat' => $katKardus->id, 'hrg' => 2000, 'demand' => 'low'],
            
            // BUBBLE WRAP (Med - Low Demand)
            ['nama' => 'Bubble Wrap Hitam 1m', 'sku' => 'BBL-HTM-1', 'kat' => $katBubble->id, 'hrg' => 3500, 'demand' => 'med'],
            ['nama' => 'Bubble Wrap Putih 1m', 'sku' => 'BBL-PTH-1', 'kat' => $katBubble->id, 'hrg' => 3000, 'demand' => 'med'],
            ['nama' => 'Bubble Wrap Roll 50m', 'sku' => 'BBL-ROL-50', 'kat' => $katBubble->id, 'hrg' => 125000, 'demand' => 'low'],
            ['nama' => 'Air Bubble Bag 10pcs', 'sku' => 'BBL-BAG-10', 'kat' => $katBubble->id, 'hrg' => 15000, 'demand' => 'low'],
            
            // LAKBAN (High - Med Demand)
            ['nama' => 'Lakban Bening 5cm Daimaru', 'sku' => 'LKB-BNG-5', 'kat' => $katLakban->id, 'hrg' => 10000, 'demand' => 'high'],
            ['nama' => 'Lakban Coklat 5cm Daimaru', 'sku' => 'LKB-CKL-5', 'kat' => $katLakban->id, 'hrg' => 10000, 'demand' => 'high'],
            ['nama' => 'Lakban Fragile Merah', 'sku' => 'LKB-FRG-M', 'kat' => $katLakban->id, 'hrg' => 12000, 'demand' => 'med'],
            ['nama' => 'Lakban Kain Hitam', 'sku' => 'LKB-KIN-H', 'kat' => $katLakban->id, 'hrg' => 15000, 'demand' => 'low'],
            
            // KARUNG PLASTIK (Med Demand)
            ['nama' => 'Karung Plastik 50kg Putih', 'sku' => 'KRN-50-P', 'kat' => $katKarung->id, 'hrg' => 3000, 'demand' => 'med'],
            ['nama' => 'Karung Plastik 25kg Putih', 'sku' => 'KRN-25-P', 'kat' => $katKarung->id, 'hrg' => 2000, 'demand' => 'med'],
            ['nama' => 'Karung Beras 10kg', 'sku' => 'KRN-10-B', 'kat' => $katKarung->id, 'hrg' => 1500, 'demand' => 'low'],
            
            // ALAT PACKING (Low Demand)
            ['nama' => 'Gunting Packing Joyko', 'sku' => 'ALT-GNT-J', 'kat' => $katAlat->id, 'hrg' => 12000, 'demand' => 'low'],
            ['nama' => 'Cutter Besar Kenko', 'sku' => 'ALT-CTR-K', 'kat' => $katAlat->id, 'hrg' => 15000, 'demand' => 'low'],
            ['nama' => 'Dispenser Lakban Besi', 'sku' => 'ALT-DSP-L', 'kat' => $katAlat->id, 'hrg' => 35000, 'demand' => 'low'],
        ];

        $products = [];
        foreach ($productsData as $pd) {
            $p = Produk::create([
                'nama' => $pd['nama'],
                'sku' => $pd['sku'],
                'kategori_id' => $pd['kat'],
                'harga' => $pd['hrg'],
                'stok' => 0,
                'tipe_produk' => 'stock', // Tampilkan semua di ROP
                'status' => true
            ]);
            $p->demand_profile = $pd['demand'];
            $products[] = $p;
        }

        $this->command->info('Memulai simulasi waktu riil untuk 60 hari...');
        
        $startDate = Carbon::now()->subDays(60)->startOfDay();
        
        $bmBatch = 1;
        $bmSeq = 1;
        $trxBatch = 1;
        $trxSeq = 1;

        DB::beginTransaction();
        try {
            // ========================================================
            // INJEKSI STOK AWAL MAKSIMAL 150-200
            // ========================================================
            $waktuAwal = $startDate->copy()->addHours(8); // Jam 8 pagi
            
            $bmAwal = BarangMasuk::create([
                'kode' => 'BM-' . str_pad($bmBatch, 3, '0', STR_PAD_LEFT) . '-' . str_pad($bmSeq++, 3, '0', STR_PAD_LEFT),
                'tanggal_pesan' => $waktuAwal->copy()->subDays(3),
                'tanggal_terima' => $waktuAwal,
                'supplier_id' => $sup1->id,
                'user_id' => $owner->id,
                'status' => 'Disetujui',
                'disetujui_oleh' => $owner->id,
                'catatan' => 'Modal awal toko'
            ]);

            foreach ($products as $prod) {
                // Modal awal cukup untuk ~25 hari
                $qty = ($prod->demand_profile === 'high') ? rand(250, 320) : (($prod->demand_profile === 'med') ? rand(100, 150) : rand(25, 45));
                
                // Variasi harga modal sedikit (sekitar 70% dari harga jual)
                $hargaModal = round(($prod->harga * 0.7) / 100) * 100;

                $dbm = DetailBarangMasuk::create([
                    'barang_masuk_id' => $bmAwal->id,
                    'produk_id' => $prod->id,
                    'jumlah' => $qty,
                    'harga' => $hargaModal
                ]);

                // Buat Batch
                StokBatch::create([
                    'produk_id' => $prod->id,
                    'detail_barang_masuk_id' => $dbm->id,
                    'qty_sisa' => $qty,
                    'harga_beli' => $hargaModal,
                    'tanggal_masuk' => $waktuAwal
                ]);

                // Catat Log Stok Masuk
                LogStok::create([
                    'produk_id' => $prod->id,
                    'barang_masuk_id' => $bmAwal->id,
                    'tipe' => 'masuk',
                    'jumlah' => $qty,
                    'keterangan' => "Penambahan stok awal dari BM {$bmAwal->kode}"
                ]);

                $prod->increment('stok', $qty);
            }

            // ========================================================
            // LOOPING HARIAN (61 Hari, termasuk hari ini)
            // ========================================================
            for ($i = 0; $i <= 60; $i++) {
                $currentDay = $startDate->copy()->addDays($i);
                
                // Refresh batch count each day
                $trxBatch = 1;
                $trxSeq = 1;
                $bmSeq = 1;

                // 1. SIMULASI RESTOCK (Hari ke-20 dan ke-40)
                if ($i > 0 && $i % 20 === 0 && $i < 60) {
                    $bmWaktu = $currentDay->copy()->addHours(10); // Barang datang jam 10 pagi
                    
                    $bmRestock = BarangMasuk::create([
                        'kode' => 'BM-' . str_pad($bmBatch, 3, '0', STR_PAD_LEFT) . '-' . str_pad($bmSeq++, 3, '0', STR_PAD_LEFT),
                        'tanggal_pesan' => $bmWaktu->copy()->subDays(rand(2, 4)), // Lead time 2-4 hari
                        'tanggal_terima' => $bmWaktu,
                        'supplier_id' => $sup2->id,
                        'user_id' => $owner->id,
                        'status' => 'Disetujui',
                        'disetujui_oleh' => $owner->id,
                        'catatan' => 'Restock rutin bulanan'
                    ]);

                    foreach ($products as $prod) {
                        $multiplier = 1.0;
                        
                        // Rekayasa pada restock terakhir (hari ke-40) agar status ROP bervariasi di hari ke-60
                        // Prediksi ROP untuk target stok di hari ke-60
                        // ROP estimasi: High ~70, Med ~28, Low ~6
                        $estDemand20Days = ($prod->demand_profile === 'high') ? 240 : (($prod->demand_profile === 'med') ? 100 : 20);
                        $estRop = ($prod->demand_profile === 'high') ? 75 : (($prod->demand_profile === 'med') ? 30 : 7);

                        if ($i === 40) {
                            $redProducts = ['Kardus Polos 20x20x20', 'Lakban Bening 5cm Daimaru', 'Bubble Wrap Hitam 1m'];
                            $yellowProducts = ['Kardus Polos 30x30x30', 'Karung Plastik 50kg Putih', 'Kardus Sepatu Pria', 'Lakban Fragile Merah'];
                            
                            $targetS60 = 0;
                            if (in_array($prod->nama, $redProducts)) {
                                $targetS60 = $estRop - rand(4, 10); // Target merah: 4-10 di bawah ROP
                            } elseif (in_array($prod->nama, $yellowProducts)) {
                                $targetS60 = $estRop + rand(2, 6);  // Target kuning: pas di ROP / sedikit di atas
                            } else {
                                $targetS60 = $estRop + rand(20, 40); // Target hijau: aman di atas ROP
                            }
                            
                            // Rumus: Stok Sekarang + Restock - Demand = Target
                            $qtyRestock = $targetS60 + $estDemand20Days - $prod->stok;
                            if ($qtyRestock < 20) $qtyRestock = 20; // Minimal restock
                        } else {
                            $qtyRestock = ($prod->demand_profile === 'high') ? rand(230, 260) : (($prod->demand_profile === 'med') ? rand(90, 110) : rand(20, 30));
                        }
                        
                        $hargaModal = round(($prod->harga * 0.72) / 100) * 100; // Harga modal sedikit naik

                        $dbm = DetailBarangMasuk::create([
                            'barang_masuk_id' => $bmRestock->id,
                            'produk_id' => $prod->id,
                            'jumlah' => $qtyRestock,
                            'harga' => $hargaModal
                        ]);

                        StokBatch::create([
                            'produk_id' => $prod->id,
                            'detail_barang_masuk_id' => $dbm->id,
                            'qty_sisa' => $qtyRestock,
                            'harga_beli' => $hargaModal, // Ini lebih mahal dari awal
                            'tanggal_masuk' => $bmWaktu
                        ]);

                        LogStok::create([
                            'produk_id' => $prod->id,
                            'barang_masuk_id' => $bmRestock->id,
                            'tipe' => 'masuk',
                            'jumlah' => $qtyRestock,
                            'keterangan' => "Restock rutin dari BM {$bmRestock->kode}"
                        ]);

                        $prod->increment('stok', $qtyRestock);
                    }
                    $bmBatch++;
                }

                // 2. SIMULASI PENJUALAN (Jam 09:00 - 17:00)
                $numPelanggan = rand(5, 12); // Ditingkatkan agar ROP lebih besar dan bervariasi
                
                for ($p = 0; $p < $numPelanggan; $p++) {
                    $jamTrans = rand(9, 16);
                    $menitTrans = rand(0, 59);
                    $trxWaktu = $currentDay->copy()->setHour($jamTrans)->setMinute($menitTrans);

                    $kodeTrx = 'TKMP-' . str_pad($trxBatch, 3, '0', STR_PAD_LEFT) . '-' . str_pad($trxSeq++, 3, '0', STR_PAD_LEFT);
                    
                    $trx = Transaksi::create([
                        'kode' => $kodeTrx,
                        'tanggal' => $trxWaktu,
                        'user_id' => $karyawan->id,
                        'nama_pelanggan' => 'Pelanggan Umum',
                        'total_harga' => 0,
                        'metode_pembayaran' => 'Tunai',
                        'status' => 'Selesai'
                    ]);

                    $totalBelanja = 0;
                    
                    // Pelanggan beli 2-4 tipe barang acak
                    $beliItems = collect($products)->random(rand(2, 4));

                    foreach ($beliItems as $prod) {
                        // Tentukan jumlah beli berdasarkan demand yang dinaikkan agar nilai d (rata-rata) besar
                        $qtyBeli = 1;
                        if ($prod->demand_profile === 'high') $qtyBeli = rand(4, 15);
                        elseif ($prod->demand_profile === 'med') $qtyBeli = rand(2, 6);
                        else {
                            if (rand(1, 100) > 40) continue; // 60% chance tidak jadi beli jika low demand
                            $qtyBeli = rand(1, 2);
                        }

                        // Cek stok cukup tidak
                        if ($prod->stok < $qtyBeli) continue;

                        // ========================================================
                        // CUSTOM BATCH LOGIC (TERMURAH & TERDIKIT) SESUAI CONTROLLER
                        // ========================================================
                        $batches = StokBatch::where('produk_id', $prod->id)
                            ->where('qty_sisa', '>', 0)
                            ->orderBy('harga_beli', 'asc') // Utamakan modal paling murah!
                            ->orderBy('qty_sisa', 'asc')   // Lalu habiskan batch yang tinggal sedikit
                            ->get();

                        $sisaPerluDiambil = $qtyBeli;
                        
                        foreach ($batches as $batch) {
                            if ($sisaPerluDiambil <= 0) break;
                            
                            $ambil = min($batch->qty_sisa, $sisaPerluDiambil);
                            $batch->decrement('qty_sisa', $ambil);
                            $sisaPerluDiambil -= $ambil;

                            $subtotalItem = $ambil * $prod->harga;
                            $totalBelanja += $subtotalItem;

                            DetailTransaksi::create([
                                'transaksi_id' => $trx->id,
                                'produk_id' => $prod->id,
                                'batch_id' => $batch->id,
                                'jumlah' => $ambil,
                                'harga' => $prod->harga,
                                'harga_modal' => $batch->harga_beli,
                                'subtotal' => $subtotalItem
                            ]);
                        }

                        // Update stok global
                        $prod->decrement('stok', $qtyBeli);

                        // Log Stok Keluar
                        LogStok::create([
                            'produk_id' => $prod->id,
                            'transaksi_id' => $trx->id,
                            'tipe' => 'keluar',
                            'jumlah' => $qtyBeli,
                            'keterangan' => "Penjualan via POS ({$trx->kode})"
                        ]);
                    }

                    if ($totalBelanja > 0) {
                        $trx->update(['total_harga' => $totalBelanja]);
                    } else {
                        $trx->delete(); // Hapus transaksi kosong
                    }
                }
                
                // Di akhir hari, ganti hari = increment batch transaksi
                $trxBatch++;
            }

            DB::commit();

            $this->command->info('Menjalankan Kalkulasi ROP Otomatis...');
            Artisan::call('rop:calculate');
            
            $this->command->info('DatabaseSeeder Berhasil Dieksekusi Secara Sempurna!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
