<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogStokController extends Controller
{
    public function index(Request $request): View
    {
        $search   = $request->string('search')->trim()->value();
        $tipe     = $request->string('tipe', 'all')->value();
        $produkId = $request->string('produk_id', 'all')->value();

        $logs = LogStok::query()
            ->with(['produk:id,nama', 'transaksi:id,kode,tanggal', 'barangMasuk:id,kode,tanggal_terima'])
            ->when($tipe !== 'all', fn ($q) => $q->where('tipe', $tipe))
            ->when($produkId !== 'all', fn ($q) => $q->where('produk_id', $produkId))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('produk', fn ($pq) => $pq->where('nama', 'like', "%{$search}%"))
                  ->orWhere('keterangan', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $products = Produk::orderBy('nama')->get(['id', 'nama']);

        if ($request->ajax()) {
            return view('log-stok._table', compact('logs'));
        }

        return view('log-stok.index', compact('logs', 'products', 'search', 'tipe', 'produkId'));
    }

    public function show(LogStok $logStok): View
    {
        $logStok->load(['produk', 'transaksi.user', 'barangMasuk.supplier', 'barangMasuk.user']);
        
        return view('log-stok.show', compact('logStok'));
    }
}
