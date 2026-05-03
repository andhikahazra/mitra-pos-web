<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\LogStok;
use App\Models\Produk;
use App\Models\StokBatch;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index(): View
    {
        $incomingGoods = BarangMasuk::query()
            ->with([
                'user:id,nama',
                'supplier:id,nama',
                'detail.produk:id,nama',
            ])
            ->orderByDesc('tanggal_pesan')
            ->orderByDesc('id')
            ->get();

        return view('barang-masuk.index', [
            'incomingGoods' => $incomingGoods
        ]);
    }

    public function show($id): View
    {
        $incoming = BarangMasuk::with([
            'user:id,nama',
            'supplier:id,nama',
            'detail.produk:id,nama,sku',
        ])->findOrFail($id);

        return view('barang-masuk.show', [
            'incoming' => $incoming
        ]);
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan' => 'nullable|string',
        ]);

        $incoming = BarangMasuk::with('detail')->findOrFail($id);

        // Proteksi: Jika sudah disetujui, tidak boleh diubah lagi statusnya untuk menghindari double stock
        if ($incoming->isDisetujui()) {
            return redirect()->back()->with('error', 'Dokumen ini sudah disetujui dan stok telah diperbarui.');
        }

        DB::transaction(function () use ($request, $incoming): void {
            $updateData = [
                'status' => $request->status,
                'disetujui_oleh' => Auth::id(),
                'catatan' => $request->catatan,
            ];

            // Jika disetujui, set tanggal_terima jika masih kosong
            if ($request->status === 'Disetujui' && !$incoming->tanggal_terima) {
                $updateData['tanggal_terima'] = now();
            }

            $incoming->update($updateData);

            // Jika status menjadi Disetujui, update stok dan log
            if ($request->status === 'Disetujui') {
                foreach ($incoming->detail as $item) {
                    $produk = Produk::lockForUpdate()->find($item->produk_id);
                    
                    if (!$produk) continue;

                    // 1. Tambah Stok Global Produk
                    $produk->increment('stok', $item->jumlah);

                    // 2. Buat Stok Batch baru (FIFO)
                    StokBatch::create([
                        'produk_id' => $produk->id,
                        'detail_barang_masuk_id' => $item->id,
                        'qty_sisa' => $item->jumlah,
                        'harga_beli' => $item->harga,
                        'tanggal_masuk' => $incoming->tanggal_terima ?? now(),
                    ]);

                    // 3. Catat Log Stok
                    LogStok::create([
                        'produk_id' => $produk->id,
                        'barang_masuk_id' => $incoming->id,
                        'tipe' => 'masuk',
                        'jumlah' => $item->jumlah,
                        'keterangan' => "Penambahan stok dari Barang Masuk #{$incoming->kode}",
                    ]);
                }
            }
        });

        $message = $request->status === 'Disetujui' 
            ? 'Barang masuk disetujui dan stok telah diperbarui.' 
            : 'Barang masuk telah ditolak.';

        return redirect()->route('barang-masuk.index')->with('success', $message);
    }
}
