<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->string('date')->trim()->value();

        $transaksi = Transaksi::query()
            ->with(['user:id,nama', 'detail_transaksi.produk:id,nama'])
            ->when($date, fn ($q) => $q->whereDate('tanggal', $date))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('transaksi.index', [
            'transaksi' => $transaksi,
            'date'      => $date,
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
