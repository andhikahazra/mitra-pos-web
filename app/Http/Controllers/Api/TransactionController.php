<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\StokBatch;
use App\Models\Produk;
use App\Models\LogStok;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pelanggan' => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|string',
            'biaya_admin' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Generate Kode Transaksi: TKMP-BBB-SSS (BBB=Batch, SSS=Sequence)
                $lastEntry = Transaksi::where('kode', 'like', 'TKMP-%')
                    ->orderBy('kode', 'desc')
                    ->first();

                if ($lastEntry) {
                    $parts = explode('-', $lastEntry->kode);
                    if (count($parts) === 3) {
                        $batch = (int) $parts[1];
                        $sequence = (int) $parts[2];

                        if ($sequence >= 999) {
                            $batch++;
                            $sequence = 1;
                        } else {
                            $sequence++;
                        }
                    } else {
                        $batch = 1;
                        $sequence = 1;
                    }
                } else {
                    $batch = 1;
                    $sequence = 1;
                }
                
                $kodeTrx = 'TKMP-' . 
                           str_pad($batch, 3, '0', STR_PAD_LEFT) . '-' . 
                           str_pad($sequence, 3, '0', STR_PAD_LEFT);

                $isInternal = $request->metode_pembayaran === 'Internal';

                // 2. Buat Header Transaksi
                $transaksi = Transaksi::create([
                    'kode' => $kodeTrx,
                    'tanggal' => now(),
                    'user_id' => $request->user()->id,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'catatan' => $request->catatan,
                    'total_harga' => 0, // Akan diupdate setelah detail selesai
                    'biaya_admin' => $isInternal ? 0 : ($request->biaya_admin ?? 0),
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'status' => $request->metode_pembayaran === 'Piutang' ? 'Tertunda' : 'Selesai',
                ]);

                $totalHargaTransaksi = 0;

                foreach ($request->items as $item) {
                    $produk = Produk::lockForUpdate()->find($item['produk_id']);
                    $jumlahDiminta = $item['jumlah'];

                    // Cek total stok
                    if ($produk->stok < $jumlahDiminta) {
                        throw new \Exception("Stok produk '{$produk->nama}' tidak mencukupi. Sisa stok: {$produk->stok}");
                    }

                    // 3. Ambil Batch (Termurah & Terkecil)
                    $batches = StokBatch::where('produk_id', $produk->id)
                        ->where('qty_sisa', '>', 0)
                        ->orderBy('harga_beli', 'asc') // Termurah dulu
                        ->orderBy('qty_sisa', 'asc')  // Paling sedikit dulu
                        ->get();

                    $sisaPerluDiambil = $jumlahDiminta;

                    foreach ($batches as $batch) {
                        if ($sisaPerluDiambil <= 0) break;

                        $ambil = min($batch->qty_sisa, $sisaPerluDiambil);
                        
                        // Detail Transaksi per Batch
                        DetailTransaksi::create([
                            'transaksi_id' => $transaksi->id,
                            'produk_id' => $produk->id,
                            'batch_id' => $batch->id,
                            'jumlah' => $ambil,
                            'harga' => $isInternal ? 0 : $item['harga'],
                            'harga_modal' => $batch->harga_beli,
                            'subtotal' => $isInternal ? 0 : ($ambil * $item['harga']),
                        ]);

                        // Update Batch
                        $batch->qty_sisa -= $ambil;
                        $batch->save();

                        $totalHargaTransaksi += ($isInternal ? 0 : ($ambil * $item['harga']));
                        $sisaPerluDiambil -= $ambil;
                    }

                    if ($sisaPerluDiambil > 0) {
                        throw new \Exception("Gagal memenuhi pesanan '{$produk->nama}'. Batch stok tidak sinkron.");
                    }

                    // Update Stok Produk
                    $produk->stok -= $jumlahDiminta;
                    $produk->save();

                    // Log Stok
                    LogStok::create([
                        'produk_id' => $produk->id,
                        'tipe' => 'Keluar',
                        'jumlah' => $jumlahDiminta,
                        'keterangan' => ($isInternal ? 'Pemakaian Sendiri: ' : 'Penjualan: ') . $kodeTrx,
                        'transaksi_id' => $transaksi->id,
                    ]);
                }

                // Update Total Harga di Header (Murni Harga Barang)
                $transaksi->update(['total_harga' => $totalHargaTransaksi]);

                // Reload with relations for response
                $transaksi->load(['user:id,nama', 'detail_transaksi.produk']);
                
                // Group details by product for a cleaner response
                $groupedDetails = $transaksi->detail_transaksi->groupBy('produk_id')->map(function ($details) {
                    $first = $details->first();
                    return [
                        'produk_id' => $first->produk_id,
                        'nama_produk' => $first->produk->nama ?? '-',
                        'jumlah' => $details->sum('jumlah'),
                        'harga' => $first->harga,
                        'subtotal' => $details->sum('subtotal'),
                        'produk' => $first->produk, // Keep the product object if needed
                    ];
                })->values();

                // Replace the original collection with the grouped array for the response
                $responseData = $transaksi->toArray();
                $responseData['detail_transaksi'] = $groupedDetails;

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan',
                    'data' => $responseData
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit', 15);
        $range = $request->get('range', 'all'); // all, hari, minggu, bulan
        $date = $request->get('date'); // specific date Y-m-d

        $query = Transaksi::with(['user:id,nama', 'detail_transaksi.produk'])
            ->orderBy('id', 'desc');

        if ($date) {
            $query->whereDate('tanggal', $date);
        } elseif ($range === 'hari') {
            $query->whereDate('tanggal', now()->toDateString());
        } elseif ($range === 'minggu') {
            $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'bulan') {
            $query->whereMonth('tanggal', now()->month)
                  ->whereYear('tanggal', now()->year);
        }

        $transaksi = $query->paginate($limit);

        // Grouping details for each transaction to avoid duplication in list view if needed, 
        // but here we just need the summary for the history list.
        $transaksi->getCollection()->transform(function ($trx) {
            $trx->total_items = $trx->detail_transaksi->sum('jumlah');
            $trx->total_sku = $trx->detail_transaksi->count();
            return $trx;
        });

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['user:id,nama', 'detail_transaksi.produk', 'detail_transaksi.batch'])
            ->find($id);

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ]);
    }
}
