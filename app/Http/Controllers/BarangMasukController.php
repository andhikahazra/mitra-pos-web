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
    public function index(Request $request): View
    {
        $query = BarangMasuk::query()
            ->with([
                'user:id,nama',
                'supplier:id,nama',
                'detail.produk:id,nama',
            ]);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Bulan/Tahun (berdasarkan tanggal_pesan)
        if ($request->filled('month')) {
            $query->whereMonth('tanggal_pesan', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('tanggal_pesan', $request->year);
        }

        $incomingGoods = $query->orderByDesc('tanggal_pesan')
            ->orderByDesc('id')
            ->get();

        // Hitung Modal Keseluruhan (Hanya yang Disetujui)
        $totalModalOverall = DB::table('barang_masuk')
            ->join('detail_barang_masuk', 'barang_masuk.id', '=', 'detail_barang_masuk.barang_masuk_id')
            ->where('barang_masuk.status', 'Disetujui')
            ->sum(DB::raw('detail_barang_masuk.jumlah * detail_barang_masuk.harga'));

        // Hitung Modal Bulan Ini (Hanya yang Disetujui)
        // Jika ada filter month/year, gunakan filter tersebut. Jika tidak, gunakan bulan saat ini.
        $targetMonth = $request->month ?? now()->month;
        $targetYear = $request->year ?? now()->year;

        $totalModalMonth = DB::table('barang_masuk')
            ->join('detail_barang_masuk', 'barang_masuk.id', '=', 'detail_barang_masuk.barang_masuk_id')
            ->where('barang_masuk.status', 'Disetujui')
            ->whereMonth('barang_masuk.tanggal_pesan', $targetMonth)
            ->whereYear('barang_masuk.tanggal_pesan', $targetYear)
            ->sum(DB::raw('detail_barang_masuk.jumlah * detail_barang_masuk.harga'));

        return view('barang-masuk.index', [
            'incomingGoods' => $incomingGoods,
            'totalModalOverall' => $totalModalOverall,
            'totalModalMonth' => $totalModalMonth,
            'selectedMonth' => $targetMonth,
            'selectedYear' => $targetYear
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
