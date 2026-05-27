<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogStokController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        $typeFilter = $request->string('type', 'all')->value();
        $productFilter = $request->string('produk_id', 'all')->value();

        // 1. Kueri data penjualan (Keluar)
        $salesQuery = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->join('produk', 'detail_transaksi.produk_id', '=', 'produk.id')
            ->select(
                'transaksi.tanggal as tanggal',
                'produk.nama as produk_nama',
                'produk.sku as produk_sku',
                'produk.id as produk_id',
                DB::raw("'Keluar' as tipe"),
                DB::raw("-detail_transaksi.jumlah as jumlah"),
                DB::raw("CONCAT('Penjualan: ', transaksi.kode) as keterangan"),
                'transaksi.kode as doc_ref',
                'transaksi.id as doc_id',
                DB::raw("'transaksi' as doc_type")
            );

        // 2. Kueri data barang masuk (Masuk) - Hanya yang statusnya Disetujui
        $incomingQuery = DB::table('detail_barang_masuk')
            ->join('barang_masuk', 'detail_barang_masuk.barang_masuk_id', '=', 'barang_masuk.id')
            ->join('produk', 'detail_barang_masuk.produk_id', '=', 'produk.id')
            ->where('barang_masuk.status', '=', 'Disetujui')
            ->select(
                'barang_masuk.tanggal_terima as tanggal',
                'produk.nama as produk_nama',
                'produk.sku as produk_sku',
                'produk.id as produk_id',
                DB::raw("'Masuk' as tipe"),
                'detail_barang_masuk.jumlah as jumlah',
                DB::raw("CONCAT('Penambahan stok dari Barang Masuk #', barang_masuk.kode) as keterangan"),
                'barang_masuk.kode as doc_ref',
                'barang_masuk.id as doc_id',
                DB::raw("'barang_masuk' as doc_type")
            );

        // 3. Union kueri dan buat subquery untuk filtering & sorting
        $unionQuery = $salesQuery->unionAll($incomingQuery);
        $subquery = DB::query()->fromSub($unionQuery, 'movements');

        // Filtering
        if ($search) {
            $subquery->where(function ($q) use ($search) {
                $q->where('produk_nama', 'like', "%{$search}%")
                  ->orWhere('produk_sku', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('doc_ref', 'like', "%{$search}%");
            });
        }

        if ($typeFilter !== 'all') {
            $subquery->where('tipe', '=', $typeFilter);
        }

        if ($productFilter !== 'all') {
            $subquery->where('produk_id', '=', $productFilter);
        }

        // Ambil data movements terpaginasi
        $movements = $subquery->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Konversi tanggal string hasil Union ke objek Carbon untuk kemudahan di View
        $movements->getCollection()->transform(function ($item) {
            $item->tanggal = \Carbon\Carbon::parse($item->tanggal);
            return $item;
        });

        // Ambil data produk untuk dropdown filter
        $products = Produk::orderBy('nama', 'asc')->get(['id', 'nama']);

        // Jika request via AJAX (dari pencarian dinamis)
        if ($request->ajax()) {
            return view('log-stok._table', compact('movements'))->render();
        }

        return view('log-stok.index', [
            'movements' => $movements,
            'products' => $products,
            'search' => $search,
            'typeFilter' => $typeFilter,
            'productFilter' => $productFilter
        ]);
    }
}
