<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\Rop;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RopService
{
    /**
     * Z-Score untuk tingkat pelayanan (Service Level) 95%.
     * 95% adalah standar industri ritel yang umum untuk meminimalkan stockout.
     */
    const Z_SCORE_95 = 1.65;
    
    /**
     * Waktu tunggu (Lead Time) default dalam hari jika produk belum pernah dipesan.
     */
    const DEFAULT_LEAD_TIME = 1.0; 
    
    /**
     * Periode perhitungan dalam hari (default 30 hari).
     */
    const DEFAULT_PERIODE = 30; 

    /**
     * Menghitung dan memperbarui ROP untuk semua produk aktif.
     */
    public function calculateAll()
    {
        // Hanya menghitung ROP untuk produk berstatus aktif dan bertipe 'stock'
        $products = Produk::where('status', true)
            ->where('tipe_produk', 'stock')
            ->get();
            
        foreach ($products as $product) {
            $this->calculateForProduct($product);
        }
    }

    /**
     * Menghitung dan memperbarui ROP untuk satu produk tertentu.
     */
    public function calculateForProduct(Produk $product, int $periodeHari = self::DEFAULT_PERIODE)
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays($periodeHari - 1)->startOfDay();

        // 1. Hitung Waktu Tunggu Rata-rata ($L) secara dinamis dan bulatkan menjadi hari penuh
        $leadTime = round($this->calculateAverageLeadTime($product->id));

        // 2. Ambil data penjualan harian selama periode yang ditentukan
        $dailySales = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->where('detail_transaksi.produk_id', $product->id)
            ->where('transaksi.status', 'selesai') // Hanya hitung transaksi yang sudah selesai
            ->whereBetween('transaksi.tanggal', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->select(DB::raw('DATE(transaksi.tanggal) as date_tanggal'), DB::raw('SUM(detail_transaksi.jumlah) as total_terjual'))
            ->groupBy(DB::raw('DATE(transaksi.tanggal)'))
            ->pluck('total_terjual', 'date_tanggal')
            ->toArray();

        // 3. Isi array penjualan dengan 0 untuk hari-hari tanpa penjualan
        $salesArray = [];
        for ($i = 0; $i < $periodeHari; $i++) {
            $dateKey = $startDate->copy()->addDays($i)->toDateString();
            $salesArray[] = isset($dailySales[$dateKey]) ? (int)$dailySales[$dateKey] : 0;
        }

        // 4. Hitung Rata-rata Penjualan Harian ($d)
        $totalSales = array_sum($salesArray);
        $averageDemand = $totalSales / $periodeHari;

        // 5. Hitung Standar Deviasi (\sigma) dari penjualan harian
        $sumOfSquares = 0;
        foreach ($salesArray as $sale) {
            $sumOfSquares += pow($sale - $averageDemand, 2);
        }
        // Menggunakan formula sample variance (n-1)
        $variance = $periodeHari > 1 ? ($sumOfSquares / ($periodeHari - 1)) : 0; 
        $standardDeviation = sqrt($variance);

        // 6. Hitung Safety Stock (SS)
        // Rumus: SS = Z * \sigma * \sqrt{L}
        $safetyStockRaw = self::Z_SCORE_95 * $standardDeviation * sqrt($leadTime);
        $safetyStock = (int) ceil($safetyStockRaw); // Selalu dibulatkan ke atas untuk stok pengaman

        // 7. Hitung Reorder Point (ROP)
        // Rumus: ROP = (d * L) + SS
        $reorderPointRaw = ($averageDemand * $leadTime) + $safetyStock;
        $reorderPoint = (int) ceil($reorderPointRaw);

        // 8. Simpan atau perbarui nilai di database tabel 'rop'
        Rop::updateOrCreate(
            ['produk_id' => $product->id],
            [
                'rata_penjualan' => round($averageDemand, 2),
                'standar_deviasi' => round($standardDeviation, 2),
                'lead_time' => round($leadTime, 2),
                'safety_stock' => $safetyStock,
                'reorder_point' => $reorderPoint,
                'periode' => $periodeHari,
            ]
        );
    }

    /**
     * Secara dinamis menghitung rata-rata lead time ($L) berdasarkan riwayat penerimaan barang masuk
     */
    private function calculateAverageLeadTime(int $productId): float
    {
        $history = DB::table('detail_barang_masuk')
            ->join('barang_masuk', 'detail_barang_masuk.barang_masuk_id', '=', 'barang_masuk.id')
            ->where('detail_barang_masuk.produk_id', $productId)
            ->whereNotNull('barang_masuk.tanggal_pesan')
            ->whereNotNull('barang_masuk.tanggal_terima')
            ->get(['barang_masuk.tanggal_pesan', 'barang_masuk.tanggal_terima']);

        if ($history->isEmpty()) {
            return self::DEFAULT_LEAD_TIME;
        }

        $totalDays = 0;
        $validRecords = 0;

        foreach ($history as $record) {
            $pesan = Carbon::parse($record->tanggal_pesan)->startOfDay();
            $terima = Carbon::parse($record->tanggal_terima)->startOfDay();
            
            // Menghitung selisih hari secara absolut
            $diffDays = abs($terima->diffInDays($pesan, false));
            
            // Jika barang dipesan dan diterima di hari yang sama (selisih 0), 
            // kita asumsikan 1 hari karena nilai L=0 akan membuat rumus rusak (SS menjadi 0).
            if ($diffDays == 0) {
                $diffDays = 1;
            }
            
            $totalDays += $diffDays;
            $validRecords++;
        }

        if ($validRecords == 0) {
            return self::DEFAULT_LEAD_TIME;
        }

        return $totalDays / $validRecords;
    }
}
