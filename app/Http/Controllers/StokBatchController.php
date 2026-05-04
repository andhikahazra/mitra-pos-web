<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use App\Models\StokBatch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StokBatchController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();

        // Query produk yang memiliki stok atau batch aktif
        $products = Produk::query()
            ->with(['kategori:id,nama'])
            ->withSum('stokBatch as total_qty_sisa', 'qty_sisa')
            ->withCount(['stokBatch as total_batch_aktif' => function ($q) {
                $q->where('qty_sisa', '>', 0);
            }])
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderByDesc('total_qty_sisa')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('stok-batch._table', compact('products'));
        }

        // Summary Global
        $summary = [
            'total_modal' => (float) StokBatch::where('qty_sisa', '>', 0)->sum(\DB::raw('qty_sisa * harga_beli')),
            'total_aset'  => (float) StokBatch::where('qty_sisa', '>', 0)
                ->join('produk', 'stok_batch.produk_id', '=', 'produk.id')
                ->sum(\DB::raw('stok_batch.qty_sisa * produk.harga')),
        ];

        return view('stok-batch.index', compact('products', 'search', 'summary'));
    }

    public function show(Produk $produk): View
    {
        // Ambil batch yang masih ada isinya (FIFO)
        $activeBatches = StokBatch::query()
            ->where('produk_id', $produk->id)
            ->where('qty_sisa', '>', 0)
            ->with(['detailBarangMasuk.barangMasuk'])
            ->orderBy('tanggal_masuk')
            ->get();

        // Ambil log pergerakan terbaru khusus produk ini
        $stockLogs = LogStok::query()
            ->where('produk_id', $produk->id)
            ->with(['transaksi', 'barangMasuk'])
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // Summary data
        $summary = [
            'total_stok' => $activeBatches->sum('qty_sisa'),
            'total_batch' => $activeBatches->count(),
        ];

        return view('stok-batch.show', compact('produk', 'activeBatches', 'stockLogs', 'summary'));
    }
}
