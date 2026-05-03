<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        // Simulasi data statistik berdasarkan periode
        // Di masa depan, ini akan mengambil data dari tabel Transaksi/Order
        $stats = [
            'views' => rand(50, 200),
            'viewsGrowth' => 12.5,
            'visits' => rand(100, 500),
            'visitsGrowth' => 8.2,
            'orders' => rand(20, 100),
            'ordersGrowth' => -2.4,
            'revenue' => rand(1500000, 5000000),
            'revenueGrowth' => 15.8,
        ];

        // Simulasi data grafik (7 hari terakhir)
        $performanceData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $performanceData[] = [
                'label' => $date->format('D'),
                'value' => (double) rand(1000000, 4000000),
                'date' => $date->toDateString(),
            ];
        }

        // Ambil info toko dasar
        $storeInfo = [
            'name' => 'Toko MitraPOS',
            'username' => $request->user()->name,
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
