<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Setting;

class ReceiptController extends Controller
{
    public function show(string $kode)
    {
        $transaksi = Transaksi::with(['user:id,nama', 'detail_transaksi.produk:id,nama'])
            ->where('kode', $kode)
            ->firstOrFail();

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

        $setting = Setting::first();
        $alamat = collect($setting->alamat_toko ?? [])->filter()->implode(', ');

        $subtotal   = (float) $transaksi->total_harga;
        $admin      = (float) $transaksi->biaya_admin;
        $method     = $transaksi->metode_pembayaran;
        $grandTotal = $subtotal + $admin;

        $title        = "Nota Digital - {$transaksi->kode}";
        $storeName    = $setting->nama_toko ?? 'MitraPOS';
        $ogTitle      = "Nota {$transaksi->kode} | {$storeName}";
        $ogDescription = "Total: Rp " . number_format($grandTotal, 0, ',', '.') . " | {$method}";
        $ogImage      = asset('favicon.png');
        $ogUrl        = config('app.url') . '/nota/' . $transaksi->kode;

        $totals = [
            'subtotal'    => $subtotal,
            'admin'       => $admin,
            'method'      => $method,
            'grand_total' => $grandTotal,
        ];

        return view('nota.show', compact(
            'transaksi', 'items', 'setting', 'alamat', 'totals',
            'title', 'ogTitle', 'ogDescription', 'ogImage', 'ogUrl'
        ));
    }
}