<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date', now()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        // 1. Ambil Data Summary
        $summary = Transaksi::query()
            ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Internal')
            ->select(
                DB::raw('COUNT(id) as total_transaksi'),
                DB::raw('SUM(total_harga) as total_omset')
            )
            ->first();

        // 2. Hitung Total Modal dari Detail
        $totalModal = DetailTransaksi::whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                  ->where('metode_pembayaran', '!=', 'Internal');
            })
            ->select(DB::raw('SUM(harga_modal * jumlah) as total_modal'))
            ->value('total_modal') ?? 0;

        $totalOmset = $summary->total_omset ?? 0;
        $labaKotor = $totalOmset - $totalModal;

        // 3. Data per hari untuk Chart/Table (jika rentang > 1 hari)
        $dailyStats = Transaksi::query()
            ->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('metode_pembayaran', '!=', 'Internal')
            ->select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('COUNT(id) as count'),
                DB::raw('SUM(total_harga) as omset')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Top Products dalam rentang tersebut
        $topProducts = DetailTransaksi::whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                  ->where('metode_pembayaran', '!=', 'Internal');
            })
            ->with('produk:id,nama')
            ->select(
                'produk_id',
                DB::raw('SUM(jumlah) as total_qty'),
                DB::raw('SUM(subtotal) as total_sales')
            )
            ->groupBy('produk_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('laporan.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalTransaksi' => $summary->total_transaksi ?? 0,
            'totalOmset' => $totalOmset,
            'totalModal' => $totalModal,
            'labaKotor' => $labaKotor,
            'dailyStats' => $dailyStats,
            'topProducts' => $topProducts,
        ]);
    }
}
