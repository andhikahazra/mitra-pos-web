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
            ->with(['kategori:id,nama', 'dimensi', 'foto'])
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
        $produk->load(['kategori:id,nama', 'dimensi', 'foto']);

        return view('produk.show', [
            'produk' => $produk,
        ]);
    }

    public function edit(Produk $produk): View
    {
        $produk->load(['dimensi', 'foto']);

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

            $produk = Produk::query()->create([
                'nama' => $request->nama,
                'sku' => $sku,
                'kategori_id' => (int) $kategoriId,
                'harga' => (float) $request->harga,
                'stok' => 0,
                'tipe_produk' => $request->tipe_produk,
                'status' => (bool) $request->status,
            ]);

            $validated = $request->all();
            $this->syncDimension($produk, $validated);
            $this->syncPhotos($produk, $request, $validated, false);
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

            $produk->update([
                'nama' => $request->nama,
                'sku' => $request->sku,
                'kategori_id' => (int) $kategoriId,
                'harga' => (float) $request->harga,
                'tipe_produk' => $request->tipe_produk,
                'status' => (bool) $request->status,
            ]);

            $validated = $request->all();
            $this->syncDimension($produk, $validated);
            $this->syncPhotos($produk, $request, $validated, true);
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

        if ($produk->logStok()->exists()) {
            $blockingRelations[] = 'log stok';
        }

        if ($blockingRelations !== []) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk tidak bisa dihapus karena masih dipakai pada: ' . implode(', ', $blockingRelations) . '.');
        }

        try {
            DB::transaction(function () use ($produk): void {
                foreach ($produk->foto as $foto) {
                    Storage::disk('public')->delete($foto->path);
                }

                $produk->foto()->delete();
                $produk->dimensi()->delete();
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
                'nullable',
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
            'has_dimension' => ['nullable', 'boolean'],
            'panjang' => ['nullable', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'has_photo' => ['nullable', 'boolean'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'max:5120'],
            'primary_existing_photo' => ['nullable', 'integer'],
            'remove_photo_ids' => ['nullable', 'array'],
            'remove_photo_ids.*' => ['integer'],
        ]);
    }

    private function syncDimension(Produk $produk, array $validated): void
    {
        if ((bool) ($validated['has_dimension'] ?? false)) {
            $produk->dimensi()->updateOrCreate(
                ['produk_id' => $produk->id],
                [
                    'panjang' => (float) ($validated['panjang'] ?? 0),
                    'lebar' => (float) ($validated['lebar'] ?? 0),
                    'tinggi' => (float) ($validated['tinggi'] ?? 0),
                    'volume' => (float) ($validated['volume'] ?? 0),
                ]
            );

            return;
        }

        $produk->dimensi()->delete();
    }

    private function syncPhotos(Produk $produk, Request $request, array $validated, bool $isUpdate): void
    {
        if (!(bool) ($validated['has_photo'] ?? false)) {
            foreach ($produk->foto as $foto) {
                Storage::disk('public')->delete($foto->path);
            }
            $produk->foto()->delete();
            return;
        }

        if ($isUpdate && !empty($validated['remove_photo_ids'])) {
            $photoIdsToRemove = $produk->foto()
                ->whereIn('id', $validated['remove_photo_ids'])
                ->pluck('id')
                ->all();

            if ($photoIdsToRemove) {
                $photosToRemove = $produk->foto()->whereIn('id', $photoIdsToRemove)->get();
                foreach ($photosToRemove as $photo) {
                    Storage::disk('public')->delete($photo->path);
                }
                $produk->foto()->whereIn('id', $photoIdsToRemove)->delete();
            }
        }

        $uploaded = $request->file('photos', []);
        $hasPrimary = $produk->foto()->where('is_primary', true)->exists();

        foreach ($uploaded as $index => $file) {
            if (!$file) {
                continue;
            }

            $storedPath = $file->store('produk', 'public');
            $isPrimary = !$hasPrimary && $index === 0;

            $produk->foto()->create([
                'path' => $storedPath,
                'is_primary' => $isPrimary,
            ]);

            if ($isPrimary) {
                $hasPrimary = true;
            }
        }

        $primaryId = (int) ($validated['primary_existing_photo'] ?? 0);
        if ($primaryId > 0) {
            $exists = $produk->foto()->whereKey($primaryId)->exists();
            if ($exists) {
                $produk->foto()->update(['is_primary' => false]);
                $produk->foto()->whereKey($primaryId)->update(['is_primary' => true]);
            }
        }

        $fallbackPrimary = $produk->foto()->where('is_primary', true)->first();
        if (!$fallbackPrimary) {
            $firstPhoto = $produk->foto()->orderBy('id')->first();
            if ($firstPhoto) {
                $firstPhoto->update(['is_primary' => true]);
            }
        }
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
