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

        // Ambil data penjualan harian 30 hari terakhir untuk "Audit"
        $startDate = now()->subDays(30)->format('Y-m-d');
        $salesHistory = LogStok::where('produk_id', $produk->id)
            ->where('tipe', 'Keluar')
            ->whereHas('transaksi', function ($q) use ($startDate) {
                $q->where('tanggal', '>=', $startDate);
            })
            ->with('transaksi:id,tanggal')
            ->get()
            ->groupBy(function ($item) {
                return $item->transaksi->tanggal;
            })
            ->map(function ($day) {
                return $day->sum('jumlah');
            });

        // Pastikan ada data 30 hari
        $dailyData = [];
        $hasRealData = false;
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $qty = $salesHistory[$date] ?? 0;
            if ($qty > 0) $hasRealData = true;
            $dailyData[$date] = $qty;
        }

        // Jika tidak ada data real, buat simulasi agar "Bagus" untuk presentasi
        $isSample = false;
        if (!$hasRealData) {
            $isSample = true;
            // Buat angka acak yang masuk akal berdasarkan rata_penjualan di tabel ROP
            $baseVal = $rataJual > 0 ? $rataJual : 5;
            foreach ($dailyData as $date => $qty) {
                // Randomize sedikit agar terlihat alami (base +/- 30%)
                $dailyData[$date] = max(0, round($baseVal + rand(-($baseVal*0.5), ($baseVal*0.5))));
            }
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
            'dailyData'       => array_reverse($dailyData),
            'isSample'        => $isSample
        ]);
    }
}
