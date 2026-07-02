<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Setting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        // 1. Tentukan rentang tanggal untuk periode aktif dan sebelumnya
        $now = Carbon::now();
        
        if ($period === '7d') {
            $startDate = $now->copy()->subDays(6)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            
            $prevStartDate = $now->copy()->subDays(13)->startOfDay();
            $prevEndDate = $now->copy()->subDays(7)->endOfDay();
            $chartDays = 6;
        } elseif ($period === '30d') {
            $startDate = $now->copy()->subDays(29)->startOfDay();
            $endDate = $now->copy()->endOfDay();
            
            $prevStartDate = $now->copy()->subDays(59)->startOfDay();
            $prevEndDate = $now->copy()->subDays(30)->endOfDay();
            $chartDays = 29;
        } else { // default: today
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
            
            $prevStartDate = $now->copy()->subDay()->startOfDay();
            $prevEndDate = $now->copy()->subDay()->endOfDay();
            $chartDays = 6; // Tetap tampilkan chart 7 hari terakhir demi estetika visual chart
        }

        // 2. Query data riil periode aktif
        // - Transaksi
        $currentTransactionsCount = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where('metode_pembayaran', '!=', 'Internal')
            ->count();
            
        // - Aktivitas (Visits): Diwakili oleh jumlah item transaksi yang terdaftar di DetailTransaksi
        $currentActivityCount = DetailTransaksi::whereHas('transaksi', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate])
                  ->where('metode_pembayaran', '!=', 'Internal');
        })->count();
        
        // - Item Terjual (Orders)
        $currentItemsSold = (int) DetailTransaksi::whereHas('transaksi', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate])
                  ->where('status', 'Selesai')
                  ->where('metode_pembayaran', '!=', 'Internal');
        })->sum('jumlah');
        
        // - Omzet (Revenue)
        $currentRevenue = (double) Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where('status', 'Selesai')
            ->where('metode_pembayaran', '!=', 'Internal')
            ->selectRaw('SUM(total_harga + biaya_admin) as total')
            ->value('total') ?? 0.0;

        // 3. Query data riil periode sebelumnya (untuk pertumbuhan / growth)
        $prevTransactionsCount = Transaksi::whereBetween('tanggal', [$prevStartDate, $prevEndDate])
            ->where('metode_pembayaran', '!=', 'Internal')
            ->count();
            
        $prevActivityCount = DetailTransaksi::whereHas('transaksi', function ($query) use ($prevStartDate, $prevEndDate) {
            $query->whereBetween('tanggal', [$prevStartDate, $prevEndDate])
                  ->where('metode_pembayaran', '!=', 'Internal');
        })->count();
        
        $prevItemsSold = (int) DetailTransaksi::whereHas('transaksi', function ($query) use ($prevStartDate, $prevEndDate) {
            $query->whereBetween('tanggal', [$prevStartDate, $prevEndDate])
                  ->where('status', 'Selesai')
                  ->where('metode_pembayaran', '!=', 'Internal');
        })->sum('jumlah');
        
        $prevRevenue = (double) Transaksi::whereBetween('tanggal', [$prevStartDate, $prevEndDate])
            ->where('status', 'Selesai')
            ->where('metode_pembayaran', '!=', 'Internal')
            ->selectRaw('SUM(total_harga + biaya_admin) as total')
            ->value('total') ?? 0.0;

        // 4. Hitung persentase pertumbuhan (growth)
        $calculateGrowth = function ($current, $prev) {
            if ($prev == 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $prev) / $prev) * 100, 1);
        };

        $stats = [
            'views' => $currentTransactionsCount,
            'viewsGrowth' => $calculateGrowth($currentTransactionsCount, $prevTransactionsCount),
            'visits' => $currentActivityCount,
            'visitsGrowth' => $calculateGrowth($currentActivityCount, $prevActivityCount),
            'orders' => $currentItemsSold,
            'ordersGrowth' => $calculateGrowth($currentItemsSold, $prevItemsSold),
            'revenue' => $currentRevenue,
            'revenueGrowth' => $calculateGrowth($currentRevenue, $prevRevenue),
        ];

        // 5. Data grafik penjualan harian berdasarkan periode
        $performanceData = [];
        for ($i = $chartDays; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            
            $dailyRevenue = (double) Transaksi::whereDate('tanggal', $dateString)
                ->where('status', 'Selesai')
                ->where('metode_pembayaran', '!=', 'Internal')
                ->selectRaw('SUM(total_harga + biaya_admin) as total')
                ->value('total') ?? 0.0;
                
            $performanceData[] = [
                'label' => ($period === '30d') ? $date->format('d M') : $date->format('D'),
                'value' => $dailyRevenue,
                'date' => $dateString,
            ];
        }

        // 6. Ambil info toko dasar dari pengaturan
        $setting = Setting::first();
        $storeInfo = [
            'name' => $setting->nama_toko ?? 'Toko MitraPOS',
            'username' => $request->user()->nama ?? 'Kasir',
            'category' => 'Operasional POS',
            'totalProducts' => Produk::count(),
            'activeProducts' => Produk::where('status', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'stats' => $stats,
                'performanceData' => $performanceData,
                'storeInfo' => $storeInfo,
            ]
        ], 200);
    }
}
