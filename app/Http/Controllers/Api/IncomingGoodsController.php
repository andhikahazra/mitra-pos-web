<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\DetailBarangMasuk;
use App\Models\Produk;
use App\Models\LogStok;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomingGoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $query = BarangMasuk::with(['supplier', 'user', 'detail.produk'])
            ->orderBy('tanggal_terima', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $barangMasuk = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Daftar barang masuk berhasil diambil',
            'data' => $barangMasuk
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:supplier,id',
            'tanggal_terima' => 'required|date',
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
            DB::beginTransaction();

            // 1. Generate Kode Otomatis: BM-BBB-SSS (BBB=Batch, SSS=Sequence)
            $lastEntry = BarangMasuk::query()
                ->where('kode', 'like', 'BM-%')
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
            
            $newKode = 'BM-' . 
                       str_pad($batch, 3, '0', STR_PAD_LEFT) . '-' . 
                       str_pad($sequence, 3, '0', STR_PAD_LEFT);

            // 2. Buat Header Barang Masuk
            $barangMasuk = BarangMasuk::create([
                'kode' => $newKode,
                'tanggal_pesan' => now(),
                'tanggal_terima' => $request->tanggal_terima,
                'supplier_id' => $request->supplier_id,
                'user_id' => $request->user()->id,
                'status' => 'Menunggu',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                // 3. Simpan Detail
                DetailBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk berhasil disimpan, menunggu persetujuan',
                'data' => $barangMasuk
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan barang masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['supplier', 'user', 'detail.produk', 'disetujuiOleh'])
            ->find($id);

        if (!$barangMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang masuk berhasil diambil',
            'data' => $barangMasuk
        ], 200);
    }

    /**
     * Approve the specified resource.
     */
    public function approve(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::with('detail')->find($id);

        if (!$barangMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Barang masuk tidak ditemukan',
            ], 404);
        }

        if ($barangMasuk->status !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Barang masuk sudah diproses',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update status barang masuk
            $barangMasuk->update(['status' => 'Diterima']);

            foreach ($barangMasuk->detail as $item) {
                // Update stok produk
                $produk = Produk::find($item->produk_id);
                $produk->stok += $item->jumlah;
                $produk->save();

                // Catat log stok
                LogStok::create([
                    'produk_id' => $item->produk_id,
                    'tipe' => 'Masuk',
                    'jumlah' => $item->jumlah,
                    'keterangan' => 'Barang Masuk: ' . $barangMasuk->kode,
                    'barang_masuk_id' => $barangMasuk->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk berhasil disetujui dan stok diperbarui',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui barang masuk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
