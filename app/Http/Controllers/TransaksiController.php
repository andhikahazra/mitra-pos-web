<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->string('start_date')->trim()->value();
        $endDate   = $request->string('end_date')->trim()->value();

        // Default: hari ini jika tidak ada filter
        if (!$startDate && !$endDate) {
            $startDate = now()->toDateString();
            $endDate   = now()->toDateString();
        }

        $query = Transaksi::query()
            ->with(['user:id,nama', 'detail_transaksi.produk:id,nama'])
            ->where('metode_pembayaran', '!=', 'Internal');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        if ($request->filled('method')) {
            $query->where('metode_pembayaran', $request->method);
        }

        $transaksi = $query->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // Summary Data (Filtered Range)
        $summaryQuery = Transaksi::query()->where('metode_pembayaran', '!=', 'Internal');
        if ($startDate && $endDate) {
            $summaryQuery->whereBetween('tanggal', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $summaryQuery->whereDate('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $summaryQuery->whereDate('tanggal', '<=', $endDate);
        }

        if ($request->filled('method')) {
            $summaryQuery->where('metode_pembayaran', $request->method);
        }

        $summaryIds = $summaryQuery->pluck('id');
        
        $summary = [
            'total_transaksi'  => $summaryQuery->count(),
            'total_item'       => (int) \App\Models\DetailTransaksi::whereIn('transaksi_id', $summaryIds)->sum('jumlah'),
            'total_omzet'      => (float) $summaryQuery->sum('total_harga'),
            'total_pendapatan' => (float) $summaryQuery->clone()->where('metode_pembayaran', '!=', 'Piutang')->sum('total_harga'),
            'total_piutang'    => (float) $summaryQuery->clone()->where('metode_pembayaran', 'Piutang')->sum('total_harga'),
            'total_admin_qris' => (float) $summaryQuery->clone()->where('metode_pembayaran', 'QRIS')->sum('biaya_admin'),
            'pembayaran'       => [
                'Tunai'    => (float) $summaryQuery->clone()->where('metode_pembayaran', 'Tunai')->sum('total_harga'),
                'QRIS'     => (float) $summaryQuery->clone()->where('metode_pembayaran', 'QRIS')->sum('total_harga'),
                'Transfer' => (float) $summaryQuery->clone()->where('metode_pembayaran', 'like', 'Transfer%')->sum('total_harga'),
            ]
        ];

        return view('transaksi.index', [
            'transaksi' => $transaksi,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'summary'   => $summary,
        ]);
    }

    public function show(Transaksi $transaksi): View
    {
        $transaksi->load(['user:id,nama', 'detail_transaksi.produk:id,nama']);

        $items = $transaksi->detail_transaksi->groupBy('produk_id')->map(function ($details) {
            $first = $details->first();
            return [
                'name'     => $first->produk->nama ?? '-',
                'qty'      => (int) $details->sum('jumlah'),
                'unit'     => 'Pcs',
                'price'    => (float) $first->harga,
                'subtotal' => (float) $details->sum('subtotal'),
            ];
        })->values();

        return view('transaksi.show', [
            'transaksi' => $transaksi,
            'items'     => $items,
        ]);
    }
}
