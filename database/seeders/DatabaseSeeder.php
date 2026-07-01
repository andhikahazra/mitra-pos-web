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

        // 4. Produk Varian
        $productsData = [
            // DUS ROKOK (Kategori Kardus)
            ['nama' => 'Gudang Garam Surya 16', 'sku' => 'GGM-SRY-16', 'kat' => $katKardus->id, 'hrg' => 4500, 'demand' => 'high', 'p' => 46.0, 'l' => 43.0, 't' => 40.0, 'vol' => 13.19],
            ['nama' => 'Gudang Garam Surya 12', 'sku' => 'GGM-SRY-12', 'kat' => $katKardus->id, 'hrg' => 4500, 'demand' => 'high', 'p' => 52.0, 'l' => 37.5, 't' => 40.0, 'vol' => 13.00],
            ['nama' => 'Gudang Garam International', 'sku' => 'GGM-INT-12', 'kat' => $katKardus->id, 'hrg' => 4000, 'demand' => 'high', 'p' => 52.5, 'l' => 38.0, 't' => 36.0, 'vol' => 11.97],
            ['nama' => 'Gudang Garam Signature', 'sku' => 'GGM-SIG-12', 'kat' => $katKardus->id, 'hrg' => 4000, 'demand' => 'med', 'p' => 52.5, 'l' => 38.0, 't' => 38.0, 'vol' => 12.64],
            ['nama' => 'Djarum Super 12', 'sku' => 'DJM-SUP-12', 'kat' => $katKardus->id, 'hrg' => 3500, 'demand' => 'med', 'p' => 40.0, 'l' => 39.0, 't' => 53.0, 'vol' => 13.78],
            ['nama' => 'Djarum Super Mild 16', 'sku' => 'DJM-MLD-16', 'kat' => $katKardus->id, 'hrg' => 3800, 'demand' => 'med', 'p' => 50.0, 'l' => 40.0, 't' => 54.0, 'vol' => 18.00],
            ['nama' => 'Djarum Super Mild 20', 'sku' => 'DJM-MLD-20', 'kat' => $katKardus->id, 'hrg' => 4000, 'demand' => 'med', 'p' => 44.0, 'l' => 40.0, 't' => 53.0, 'vol' => 15.55],
            ['nama' => 'Djarum 76', 'sku' => 'DJM-760-12', 'kat' => $katKardus->id, 'hrg' => 3000, 'demand' => 'med', 'p' => 45.0, 'l' => 38.0, 't' => 45.0, 'vol' => 12.83],
            ['nama' => 'Sampoerna A Mild', 'sku' => 'SAM-MLD-16', 'kat' => $katKardus->id, 'hrg' => 4200, 'demand' => 'med', 'p' => 56.0, 'l' => 36.5, 't' => 49.0, 'vol' => 16.69],
            ['nama' => 'Dji Sam Soe (HM Sampoerna)', 'sku' => 'SAM-DSS-12', 'kat' => $katKardus->id, 'hrg' => 4500, 'demand' => 'low', 'p' => 55.0, 'l' => 36.5, 't' => 50.0, 'vol' => 16.73],
            ['nama' => 'Sampoerna U Bold', 'sku' => 'SAM-UBL-16', 'kat' => $katKardus->id, 'hrg' => 4000, 'demand' => 'low', 'p' => 56.0, 'l' => 40.0, 't' => 40.0, 'vol' => 14.93],
            ['nama' => 'Marlboro Red', 'sku' => 'MAR-RED-20', 'kat' => $katKardus->id, 'hrg' => 5000, 'demand' => 'low', 'p' => 45.5, 'l' => 24.5, 't' => 57.0, 'vol' => 10.59],
            ['nama' => 'Marlboro Gold', 'sku' => 'MAR-GLD-20', 'kat' => $katKardus->id, 'hrg' => 5000, 'demand' => 'low', 'p' => 45.5, 'l' => 24.5, 't' => 57.0, 'vol' => 10.59],
            ['nama' => 'LA Lights 16', 'sku' => 'LAL-LGT-16', 'kat' => $katKardus->id, 'hrg' => 3800, 'demand' => 'low', 'p' => 52.0, 'l' => 38.0, 't' => 38.0, 'vol' => 12.51],
            
            // BUBBLE WRAP
            ['nama' => 'Bubble Wrap Hitam 1m', 'sku' => 'BBL-HTM-1', 'kat' => $katBubble->id, 'hrg' => 3500, 'demand' => 'med'],
            ['nama' => 'Bubble Wrap Putih 1m', 'sku' => 'BBL-PTH-1', 'kat' => $katBubble->id, 'hrg' => 3000, 'demand' => 'med'],
            ['nama' => 'Bubble Wrap Roll 50m', 'sku' => 'BBL-ROL-50', 'kat' => $katBubble->id, 'hrg' => 125000, 'demand' => 'low'],
            ['nama' => 'Air Bubble Bag', 'sku' => 'BBL-BAG-10', 'kat' => $katBubble->id, 'hrg' => 15000, 'demand' => 'low'],
            
            // LAKBAN
            ['nama' => 'Daimaru Bening 2" 90 Yard', 'sku' => 'LKB-DM-BNG', 'kat' => $katLakban->id, 'hrg' => 10000, 'demand' => 'high'],
            ['nama' => 'Daimaru Cokelat 2" 90 Yard', 'sku' => 'LKB-DM-COK', 'kat' => $katLakban->id, 'hrg' => 10000, 'demand' => 'high'],
            ['nama' => 'Daimaru Fragile Merah 2" 100 Yard', 'sku' => 'LKB-DM-FRG', 'kat' => $katLakban->id, 'hrg' => 12000, 'demand' => 'med'],
            ['nama' => 'Nachi Tape Bening 2" 72 Yard', 'sku' => 'LKB-NC-BNG', 'kat' => $katLakban->id, 'hrg' => 8500, 'demand' => 'med'],
            ['nama' => 'Nachi Tape Cokelat 2" 72 Yard', 'sku' => 'LKB-NC-COK', 'kat' => $katLakban->id, 'hrg' => 8500, 'demand' => 'med'],
            ['nama' => 'Joyko Cloth Tape Hitam 2" 12m', 'sku' => 'LKB-JY-CLT', 'kat' => $katLakban->id, 'hrg' => 15000, 'demand' => 'low'],
            
            // KARUNG PLASTIK
            ['nama' => 'Karung Plastik 50kg Putih', 'sku' => 'KRN-50-P', 'kat' => $katKarung->id, 'hrg' => 3000, 'demand' => 'med'],
            ['nama' => 'Karung Plastik 25kg Putih', 'sku' => 'KRN-25-P', 'kat' => $katKarung->id, 'hrg' => 2000, 'demand' => 'med'],
            ['nama' => 'Karung Beras 10kg', 'sku' => 'KRN-10-B', 'kat' => $katKarung->id, 'hrg' => 1500, 'demand' => 'low'],
            
            // ALAT PACKING
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
                'status' => true,
                'panjang' => $pd['p'] ?? null,
                'lebar' => $pd['l'] ?? null,
                'tinggi' => $pd['t'] ?? null,
                'volume' => $pd['vol'] ?? null,
                'foto' => ($pd['kat'] === $katKardus->id) 
                    ? 'https://images.unsplash.com/photo-1656543802898-41c8c46683a7?q=80&w=600' 
                    : null,
            ]);
            $p->demand_profile = $pd['demand'];
            $products[] = $p;
        }

        $this->command->info('Memulai simulasi waktu riil untuk 60 hari...');
        
        $startDate = Carbon::now()->subDays(60)->startOfDay();
        
        $bmBatch = 1;
        $bmSeq = 0;
        $generateBmKode = function () use (&$bmBatch, &$bmSeq) {
            if ($bmSeq >= 999) {
                $bmBatch++;
                $bmSeq = 1;
            } else {
                $bmSeq++;
            }
            return 'BM-' . str_pad($bmBatch, 3, '0', STR_PAD_LEFT) . '-' . str_pad($bmSeq, 3, '0', STR_PAD_LEFT);
        };

        $trxBatch = 1;
        $trxSeq = 0;
        $generateTrxKode = function () use (&$trxBatch, &$trxSeq) {
            if ($trxSeq >= 999) {
                $trxBatch++;
                $trxSeq = 1;
            } else {
                $trxSeq++;
            }
            return 'TKMP-' . str_pad($trxBatch, 3, '0', STR_PAD_LEFT) . '-' . str_pad($trxSeq, 3, '0', STR_PAD_LEFT);
        };

        DB::beginTransaction();
        try {
            // ========================================================
            // INJEKSI STOK AWAL (MODAL AWAL) - 1 DOKUMEN BESAR
            // ========================================================
            $waktuAwal = $startDate->copy()->addHours(8); // Jam 8 pagi
            $initialLT = rand(2, 4);

            $bmAwal = BarangMasuk::create([
                'kode' => $generateBmKode(),
                'tanggal_pesan' => $waktuAwal->copy()->subDays($initialLT),
                'tanggal_terima' => $waktuAwal,
                'supplier_id' => $sup1->id,
                'user_id' => $owner->id,
                'status' => 'Disetujui',
                'disetujui_oleh' => $owner->id,
                'catatan' => 'Modal awal seluruh produk'
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

                $prod->increment('stok', $qty);
            }

            // ========================================================
            // LOOPING HARIAN (61 Hari, termasuk hari ini)
            // ========================================================
            for ($i = 0; $i <= 60; $i++) {
                $currentDay = $startDate->copy()->addDays($i);

                // 1. SIMULASI RESTOCK (Hari ke-20 dan ke-40)
                if ($i > 0 && $i % 20 === 0 && $i < 60) {
                    $bmWaktu = $currentDay->copy()->addHours(10); // Barang datang jam 10 pagi
                    $ltDays = rand(1, 5); // Waktu tunggu bervariasi secara realistis (1-5 hari)

                    // Buat dokumen restock Supplier 1
                    $bmRestock1 = BarangMasuk::create([
                        'kode' => $generateBmKode(),
                        'tanggal_pesan' => $bmWaktu->copy()->subDays($ltDays),
                        'tanggal_terima' => $bmWaktu,
                        'supplier_id' => $sup1->id,
                        'user_id' => $owner->id,
                        'status' => 'Disetujui',
                        'disetujui_oleh' => $owner->id,
                        'catatan' => 'Restock rutin Supplier 1'
                    ]);

                    // Buat dokumen restock Supplier 2
                    $bmRestock2 = BarangMasuk::create([
                        'kode' => $generateBmKode(),
                        'tanggal_pesan' => $bmWaktu->copy()->subDays($ltDays),
                        'tanggal_terima' => $bmWaktu,
                        'supplier_id' => $sup2->id,
                        'user_id' => $owner->id,
                        'status' => 'Disetujui',
                        'disetujui_oleh' => $owner->id,
                        'catatan' => 'Restock rutin Supplier 2'
                    ]);
                    
                    foreach ($products as $prod) {
                        // Rekayasa pada restock terakhir (hari ke-40) agar status ROP bervariasi di hari ke-60
                        $estDemand20Days = ($prod->demand_profile === 'high') ? 240 : (($prod->demand_profile === 'med') ? 100 : 20);
                        $estRop = ($prod->demand_profile === 'high') ? 75 : (($prod->demand_profile === 'med') ? 30 : 7);

                        if ($i === 40) {
                            $redProducts = ['Gudang Garam Surya 16', 'Daimaru Bening 2" 90 Yard', 'Bubble Wrap Hitam 1m'];
                            $yellowProducts = ['Gudang Garam Surya 12', 'Karung Plastik 50kg Putih', 'Djarum Super Mild 16', 'Daimaru Fragile Merah 2" 100 Yard'];
                            
                            $targetS60 = 0;
                            if (in_array($prod->nama, $redProducts)) {
                                $targetS60 = $estRop - rand(4, 10);
                            } elseif (in_array($prod->nama, $yellowProducts)) {
                                $targetS60 = $estRop + rand(2, 6);
                            } else {
                                $targetS60 = $estRop + rand(20, 40);
                            }
                            
                            $qtyRestock = $targetS60 + $estDemand20Days - $prod->stok;
                            if ($qtyRestock < 20) $qtyRestock = 20;
                        } else {
                            $qtyRestock = ($prod->demand_profile === 'high') ? rand(230, 260) : (($prod->demand_profile === 'med') ? rand(90, 110) : rand(20, 30));
                        }
                        
                        $hargaModal = round(($prod->harga * 0.72) / 100) * 100;

                        // Tentukan dokumen berdasarkan supplier_id
                        $isSup1 = ($prod->id % 2 === 0);
                        $targetBm = $isSup1 ? $bmRestock1 : $bmRestock2;

                        $dbm = DetailBarangMasuk::create([
                            'barang_masuk_id' => $targetBm->id,
                            'produk_id' => $prod->id,
                            'jumlah' => $qtyRestock,
                            'harga' => $hargaModal
                        ]);

                        StokBatch::create([
                            'produk_id' => $prod->id,
                            'detail_barang_masuk_id' => $dbm->id,
                            'qty_sisa' => $qtyRestock,
                            'harga_beli' => $hargaModal,
                            'tanggal_masuk' => $bmWaktu
                        ]);

                        $prod->increment('stok', $qtyRestock);
                    }
                }

                // 2. SIMULASI PENJUALAN (Jam 09:00 - 17:00)
                $numPelanggan = rand(5, 12); // Ditingkatkan agar ROP lebih besar dan bervariasi
                $dailyTransactions = [];

                for ($p = 0; $p < $numPelanggan; $p++) {
                    $jamTrans = rand(9, 16);
                    $menitTrans = rand(0, 59);
                    $dailyTransactions[] = $currentDay->copy()->setHour($jamTrans)->setMinute($menitTrans);
                }

                // Urutkan transaksi harian berdasarkan waktu secara ASC (kronologis)
                usort($dailyTransactions, function ($a, $b) {
                    return $a->timestamp <=> $b->timestamp;
                });

                foreach ($dailyTransactions as $trxWaktu) {
                    $kodeTrx = $generateTrxKode();
                    
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

                    }

                    if ($totalBelanja > 0) {
                        $trx->update(['total_harga' => $totalBelanja]);
                    } else {
                        $trx->delete(); // Hapus transaksi kosong
                    }
                }
            }

            DB::commit();

            $this->command->info('Menjalankan Kalkulasi ROP Otomatis...');
            Artisan::call('rop:calculate');
            
            $this->command->info('Menyesuaikan stok semua produk agar ROP bervariasi secara bergantian...');
            $targetStatuses = [
                // Dus Rokok (14 items)
                'Gudang Garam Surya 16'         => 'harus restock',
                'Gudang Garam Surya 12'         => 'hampir habis',
                'Gudang Garam International'    => 'aman',
                'Gudang Garam Signature'        => 'aman',
                'Djarum Super 12'               => 'harus restock',
                'Djarum Super Mild 16'          => 'hampir habis',
                'Djarum Super Mild 20'          => 'aman',
                'Djarum 76'                     => 'harus restock',
                'Sampoerna A Mild'              => 'hampir habis',
                'Dji Sam Soe (HM Sampoerna)'    => 'aman',
                'Sampoerna U Bold'              => 'harus restock',
                'Marlboro Red'                  => 'hampir habis',
                'Marlboro Gold'                 => 'aman',
                'LA Lights 16'                  => 'aman',

                // Lakban (6 items)
                'Daimaru Bening 2" 90 Yard'     => 'hampir habis',
                'Daimaru Cokelat 2" 90 Yard'    => 'aman',
                'Daimaru Fragile Merah 2" 100 Yard' => 'harus restock',
                'Nachi Tape Bening 2" 72 Yard'  => 'aman',
                'Nachi Tape Cokelat 2" 72 Yard' => 'hampir habis',
                'Joyko Cloth Tape Hitam 2" 12m' => 'aman',

                // Bubble Wrap (4 items)
                'Air Bubble Bag'                => 'harus restock',
                'Bubble Wrap Hitam 1m'          => 'hampir habis',
                'Bubble Wrap Putih 1m'          => 'aman',
                'Bubble Wrap Roll 50m'          => 'harus restock',

                // Karung (3 items)
                'Karung Beras 10kg'             => 'hampir habis',
                'Karung Plastik 25kg Putih'     => 'aman',
                'Karung Plastik 50kg Putih'     => 'harus restock',

                // Alat (3 items)
                'Gunting Packing Joyko'         => 'harus restock',
                'Cutter Besar Kenko'            => 'hampir habis',
                'Dispenser Lakban Besi'         => 'aman',
            ];

            foreach ($targetStatuses as $name => $status) {
                $p = Produk::with('rop')->where('nama', $name)->first();
                if ($p && $p->rop) {
                    $ropValue    = (int) $p->rop->reorder_point;
                    $safetyStock = (int) $p->rop->safety_stock;
                    
                    if ($status === 'harus restock') {
                        $newStock = max(2, $ropValue - rand(2, 5));
                    } elseif ($status === 'hampir habis') {
                        $newStock = $ropValue + max(1, (int) ceil($safetyStock * 0.25));
                    } else { // aman
                        $newStock = $ropValue + $safetyStock + rand(15, 30);
                    }
                    
                    $p->update(['stok' => $newStock]);
                    
                    // Sinkronisasi dengan batch stok agar data konsisten
                    StokBatch::where('produk_id', $p->id)->update(['qty_sisa' => 0]);
                    $latestBatch = StokBatch::where('produk_id', $p->id)->orderBy('id', 'desc')->first();
                    if ($latestBatch) {
                        $latestBatch->update(['qty_sisa' => $newStock]);
                    }
                }
            }

            $this->command->info('Membuat 2 dokumen barang masuk yang menunggu ACC...');
            
            // Pending BM 1
            $bmPending1 = BarangMasuk::create([
                'kode' => $generateBmKode(),
                'tanggal_pesan' => Carbon::now(),
                'tanggal_terima' => null,
                'supplier_id' => $sup1->id,
                'user_id' => $karyawan->id,
                'status' => 'menunggu',
                'catatan' => 'Restock kardus tambahan'
            ]);
            
            // Detail BM 1
            DetailBarangMasuk::create([
                'barang_masuk_id' => $bmPending1->id,
                'produk_id' => Produk::where('nama', 'Gudang Garam Surya 16')->first()->id,
                'jumlah' => 100,
                'harga' => 1800
            ]);

            // Pending BM 2
            $bmPending2 = BarangMasuk::create([
                'kode' => $generateBmKode(),
                'tanggal_pesan' => Carbon::now()->subMinutes(30),
                'tanggal_terima' => null,
                'supplier_id' => $sup2->id,
                'user_id' => $karyawan->id,
                'status' => 'menunggu',
                'catatan' => 'Restock bubble wrap'
            ]);
            
            // Detail BM 2
            DetailBarangMasuk::create([
                'barang_masuk_id' => $bmPending2->id,
                'produk_id' => Produk::where('nama', 'Bubble Wrap Hitam 1m')->first()->id,
                'jumlah' => 50,
                'harga' => 2500
            ]);
            
            $this->command->info('DatabaseSeeder Berhasil Dieksekusi Secara Sempurna!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
