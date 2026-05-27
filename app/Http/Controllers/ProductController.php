<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(): View
    {
        $produk = Produk::query()
            ->with(['kategori:id,nama'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('produk.index', [
            'produk' => $produk,
        ]);
    }

    public function create(): View
    {
        $lastProduk = Produk::query()->latest('id')->first();
        $nextId = $lastProduk ? $lastProduk->id + 1 : 1;
        $suggestedSku = 'PRD-' . str_pad((string)$nextId, 4, '0', STR_PAD_LEFT);

        return view('produk.create', [
            'kategori' => Kategori::query()->orderBy('nama')->get(['id', 'nama']),
            'suggestedSku' => $suggestedSku,
        ]);
    }

    public function show(Produk $produk): View
    {
        $produk->load(['kategori:id,nama']);

        return view('produk.show', [
            'produk' => $produk,
        ]);
    }

    public function edit(Produk $produk): View
    {
        return view('produk.edit', [
            'produk' => $produk,
            'kategori' => Kategori::query()->orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validatePayload($request);

        DB::transaction(function () use ($request): void {
            $kategoriId = $request->kategori_id;
            
            if ($kategoriId === 'new') {
                $newKategori = Kategori::query()->create([
                    'nama' => $request->new_kategori
                ]);
                $kategoriId = $newKategori->id;
            }

            // Generate SKU otomatis jika tidak diisi (untuk produk baru)
            $sku = $this->generateUniqueSku($request->nama);

            // Handle file upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('produk', 'public');
            }

            Produk::query()->create([
                'nama' => $request->nama,
                'sku' => $sku,
                'kategori_id' => (int) $kategoriId,
                'harga' => (float) $request->harga,
                'stok' => 0,
                'tipe_produk' => $request->tipe_produk,
                'status' => (bool) $request->status,
                'panjang' => $request->filled('panjang') ? (float) $request->panjang : null,
                'lebar' => $request->filled('lebar') ? (float) $request->lebar : null,
                'tinggi' => $request->filled('tinggi') ? (float) $request->tinggi : null,
                'volume' => $request->filled('volume') ? (float) $request->volume : null,
                'foto' => $fotoPath,
            ]);
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $this->validatePayload($request, $produk->id);

        DB::transaction(function () use ($produk, $request): void {
            $kategoriId = $request->kategori_id;
            
            if ($kategoriId === 'new') {
                $newKategori = Kategori::query()->create([
                    'nama' => $request->new_kategori
                ]);
                $kategoriId = $newKategori->id;
            }

            $updateData = [
                'nama' => $request->nama,
                'sku' => $request->sku,
                'kategori_id' => (int) $kategoriId,
                'harga' => (float) $request->harga,
                'tipe_produk' => $request->tipe_produk,
                'status' => (bool) $request->status,
                'panjang' => $request->filled('panjang') ? (float) $request->panjang : null,
                'lebar' => $request->filled('lebar') ? (float) $request->lebar : null,
                'tinggi' => $request->filled('tinggi') ? (float) $request->tinggi : null,
                'volume' => $request->filled('volume') ? (float) $request->volume : null,
            ];

            // Handle file upload
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($produk->foto) {
                    Storage::disk('public')->delete($produk->foto);
                }
                $updateData['foto'] = $request->file('foto')->store('produk', 'public');
            } elseif ($request->boolean('remove_photo')) {
                // Hapus foto saat ini
                if ($produk->foto) {
                    Storage::disk('public')->delete($produk->foto);
                }
                $updateData['foto'] = null;
            }

            $produk->update($updateData);
        });

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $blockingRelations = [];

        if ($produk->detailBarangMasuk()->exists()) {
            $blockingRelations[] = 'detail barang masuk';
        }

        if ($produk->stokBatch()->exists()) {
            $blockingRelations[] = 'stok batch';
        }

        if ($produk->detailTransaksi()->exists()) {
            $blockingRelations[] = 'detail transaksi';
        }

        if ($blockingRelations !== []) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk tidak bisa dihapus karena masih dipakai pada: ' . implode(', ', $blockingRelations) . '.');
        }

        try {
            DB::transaction(function () use ($produk): void {
                if ($produk->foto) {
                    Storage::disk('public')->delete($produk->foto);
                }
                $produk->delete();
            });
        } catch (QueryException $exception) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk gagal dihapus karena masih terhubung dengan data lain.');
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('produk', 'sku')->ignore($ignoreId),
            ],
            'kategori_id' => ['required', 'string'],
            'new_kategori' => [
                'required_if:kategori_id,new',
                'nullable',
                'string',
                'max:255',
                'unique:kategori,nama'
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'tipe_produk' => ['required', 'in:stock,non-stock'],
            'status' => ['required', 'boolean'],
            'panjang' => ['nullable', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
    }

    private function generateUniqueSku(string $name): string
    {
        // Bersihkan nama dan ambil 3 huruf pertama
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        do {
            // Suffix acak 4 karakter
            $suffix = strtoupper(substr(md5(uniqid()), 0, 4));
            $sku = $prefix . '-' . $suffix;
            
            // Cek keunikan di database
            $exists = Produk::query()->where('sku', $sku)->exists();
        } while ($exists);

        return $sku;
    }
}
