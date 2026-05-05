<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\LogStok;
use Illuminate\Http\Request;

class RopController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->value();
        $statusFilter = $request->string('status', 'all')->value();
        $sort = $request->string('sort', 'name')->value();

        $products = Produk::with('rop')->get();

        $rows = $products->map(function ($product) {
            $ropValue   = (int) ($product->rop->reorder_point ?? 0);
            $safetyStock = (int) ($product->rop->safety_stock ?? 0);
            $stock       = (int) $product->stok;

            if ($stock <= $ropValue) {
                $status = 'harus restock';
            } elseif ($stock <= $ropValue + ($safetyStock * 0.5)) {
                $status = 'hampir habis';
            } else {
                $status = 'aman';
            }

            return [
                'id'          => $product->id,
                'name'        => $product->nama,
                'stock'       => $stock,
                'safetyStock' => $safetyStock,
                'leadTime'    => (float) ($product->rop->lead_time ?? 0),
                'rataPenjualan' => (float) ($product->rop->rata_penjualan ?? 0),
                'rop'         => $ropValue,
                'status'      => $status,
                'calculatedAt' => $product->rop->waktu_penghitungan ? $product->rop->waktu_penghitungan->translatedFormat('d M Y, H:i') : '-'
            ];
        });

        if ($search) {
            $rows = $rows->filter(function ($r) use ($search) {
                return str_contains(strtolower($r['name']), strtolower($search));
            });
        }

        if ($statusFilter !== 'all') {
            $rows = $rows->filter(function ($r) use ($statusFilter) {
                return $r['status'] === $statusFilter;
            });
        }

        if ($sort === 'stockAsc') {
            $rows = $rows->sortBy('stock');
        } elseif ($sort === 'ropDesc') {
            $rows = $rows->sortByDesc('rop');
        } else {
            $rows = $rows->sortBy('name');
        }

        $rows = $rows->values();

        return view('rop.index', [
            'rows' => $rows,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'sort' => $sort
        ]);
    }

    public function show(Produk $produk)
    {
        $produk->load('rop');
        
        $ropValue    = (int) ($produk->rop->reorder_point ?? 0);
        $safetyStock = (int) ($produk->rop->safety_stock ?? 0);
        $rataJual    = (float) ($produk->rop->rata_penjualan ?? 0);
        $leadTime    = (float) ($produk->rop->lead_time ?? 0);
        $stock       = (int) $produk->stok;

        if ($stock <= $ropValue) {
            $status = 'harus restock';
        } elseif ($stock <= $ropValue + ($safetyStock * 0.5)) {
            $status = 'hampir habis';
        } else {
            $status = 'aman';
        }

        // Tentukan titik referensi waktu (Waktu Kalkulasi), jika belum pernah dihitung pakai 'now'
        $calculationTime = $produk->rop->waktu_penghitungan ?? now();
        $periode = $produk->rop->periode ?? 30;

        // Ambil data penjualan harian selama periode tersebut (berdasarkan waktu kalkulasi)
        $startDate = $calculationTime->copy()->subDays($periode - 1)->startOfDay();
        $endDate   = $calculationTime->copy()->endOfDay();
        
        $salesHistory = LogStok::where('produk_id', $produk->id)
            ->whereIn('tipe', ['Keluar', 'keluar'])
            ->whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->with('transaksi:id,tanggal')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->transaksi->tanggal)->format('Y-m-d');
            })
            ->map(function ($day) {
                return $day->sum('jumlah');
            });

        // Buat urutan kronologis (Dari Terlama ke Terbaru)
        $dailyData = [];
        for ($i = $periode - 1; $i >= 0; $i--) {
            $date = $calculationTime->copy()->subDays($i)->format('Y-m-d');
            $dailyData[$date] = $salesHistory[$date] ?? 0;
        }

        return view('rop.show', [
            'produk'          => $produk,
            'stock'           => $stock,
            'safetyStock'     => $safetyStock,
            'rataPenjualan'   => $rataJual,
            'leadTime'        => $leadTime,
            'standarDeviasi'  => (float) ($produk->rop->standar_deviasi ?? 0),
            'rop'             => $ropValue,
            'status'          => $status,
            'usageLT'         => $rataJual * $leadTime,
            'sqrtLT'          => sqrt($leadTime),
            'zScore'          => 1.65, 
            'dailyData'       => $dailyData,
            'isSample'        => false, // Simulasi dimatikan untuk integritas data
            'periode'         => $periode,
            'calculatedAt'    => $produk->rop->waktu_penghitungan ? $produk->rop->waktu_penghitungan->translatedFormat('l, d F Y H:i') : '-'
        ]);
    }
}
