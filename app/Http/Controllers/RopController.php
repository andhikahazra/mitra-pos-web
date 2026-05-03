<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RopController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $statusFilter = $request->string('status', 'all')->value();
        $sort = $request->string('sort', 'name')->value();

        $products = Produk::query()
            ->with(['rop:id,produk_id,safety_stock,lead_time,reorder_point'])
            ->when($search, fn ($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->get(['id', 'nama', 'stok', 'kategori_id']);

        $rows = $products->map(function (Produk $product): array {
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
                'name'        => $product->nama,
                'stock'       => $stock,
                'safetyStock' => $safetyStock,
                'leadTime'    => (float) ($product->rop->lead_time ?? 0),
                'rop'         => $ropValue,
                'status'      => $status,
            ];
        });

        // Filter status
        if ($statusFilter !== 'all') {
            $rows = $rows->filter(fn ($r) => $r['status'] === $statusFilter)->values();
        }

        // Sort
        $rows = match ($sort) {
            'stockAsc' => $rows->sortBy('stock')->values(),
            'ropDesc'  => $rows->sortByDesc('rop')->values(),
            default    => $rows->sortBy('name')->values(),
        };

        return view('rop.index', [
            'rows'         => $rows,
            'search'       => $search,
            'statusFilter' => $statusFilter,
            'sort'         => $sort,
        ]);
    }
}
