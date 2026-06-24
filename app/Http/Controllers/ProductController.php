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
        return view('produk.create', [
            'kategori' => Kategori::query()->orderBy('nama')->get(['id', 'nama']),
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
            $kategoriNama = '';
            
            if ($kategoriId === 'new') {
                $newKategori = Kategori::query()->create([
                    'nama' => $request->new_kategori
                ]);
                $kategoriId = $newKategori->id;
                $kategoriNama = $request->new_kategori;
            } else {
                $kategori = Kategori::query()->find($kategoriId);
                $kategoriNama = $kategori ? $kategori->nama : '';
            }

            // Generate SKU otomatis dengan format seeder-like
            $sku = $this->generateSmartSku($request->nama, $kategoriNama);

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
            'panjang' => ['nullable', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
    }

    private function generateSmartSku(string $namaProduk, string $namaKategori): string
    {
        // 1. Dapatkan Kode Kategori (3 Huruf)
        $kat = strtolower(trim($namaKategori));
        $categoryMappings = [
            'kardus' => 'KRD',
            'bubble wrap' => 'BBL',
            'lakban' => 'LKB',
            'karung plastik' => 'KRN',
            'karung' => 'KRN',
            'alat packing' => 'ALT',
            'alat' => 'ALT',
        ];

        if (isset($categoryMappings[$kat])) {
            $prefix = $categoryMappings[$kat];
        } else {
            // Aturan konsonan untuk kategori baru
            $cleanKat = preg_replace('/[^a-zA-Z0-9]/', '', $namaKategori);
            if (strlen($cleanKat) < 3) {
                $prefix = str_pad(strtoupper($cleanKat), 3, 'X');
            } else {
                $firstLetter = strtoupper(substr($cleanKat, 0, 1));
                $rest = substr($cleanKat, 1);
                preg_match_all('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ0-9]/', $rest, $matches);
                $consonants = implode('', $matches[0]);
                $prefix = $firstLetter . strtoupper($consonants);
                if (strlen($prefix) > 3) {
                    $prefix = substr($prefix, 0, 3);
                } elseif (strlen($prefix) < 3) {
                    // Jika konsonan tidak cukup, ambil 3 huruf pertama dari nama kategori bersih
                    $prefix = substr(strtoupper($cleanKat), 0, 3);
                }
            }
        }

        // 2. Proses nama produk untuk mengekstrak atribut/suffix
        // Hapus kata kategori dari nama produk agar tidak duplikat
        $kategoriWords = explode(' ', strtolower(trim($namaKategori)));
        $productNameLower = strtolower(trim($namaProduk));
        foreach ($kategoriWords as $word) {
            if (strlen($word) > 2) {
                $productNameLower = str_replace($word, '', $productNameLower);
            }
        }

        // Bersihkan spasi ganda
        $productNameClean = preg_replace('/\s+/', ' ', trim($productNameLower));
        
        $suffixParts = [];

        // Deteksi dimensi (seperti 20x20x20 atau 15x10x10)
        if (preg_match('/(\d+x\d+(?:x\d+)?)/i', $namaProduk, $dimMatches)) {
            $dimensions = str_replace('x', '', strtolower($dimMatches[1]));
            $suffixParts[] = $dimensions;
            
            // Hapus dimensi dari teks agar tidak diproses lagi
            $dimPattern = '/' . preg_quote($dimMatches[1], '/') . '/i';
            $productNameClean = preg_replace($dimPattern, '', $productNameClean);
            $productNameClean = preg_replace('/\s+/', ' ', trim($productNameClean));
        }

        // Tokenisasi sisa nama produk
        $tokens = array_filter(explode(' ', $productNameClean));
        
        foreach ($tokens as $token) {
            $token = trim($token);
            if (empty($token)) {
                continue;
            }

            // Kata-kata yang diabaikan (karena kata hubung/preposisi umum)
            $ignores = ['dan', 'dengan', 'untuk', 'yang', 'atau', 'in', 'of', 'and', 'the'];
            if (in_array(strtolower($token), $ignores)) {
                continue;
            }

            // Deteksi angka dengan unit (misal: 50kg, 1m, 5cm, 50m)
            if (preg_match('/^(\d+)(?:kg|m|cm|g|pcs|l|ml)?$/i', $token, $numMatches)) {
                $suffixParts[] = $numMatches[1];
                continue;
            }

            // Singkatan atribut/warna khusus
            $lowerToken = strtolower($token);
            $specialMappings = [
                'pria' => 'P',
                'wanita' => 'W',
                'kain' => 'KIN',
                'hitam' => 'HTM',
                'putih' => 'PTH',
                'bening' => 'BNG',
                'coklat' => 'CKL',
                'fragile' => 'FRG',
                'sepatu' => 'SPT',
                'gunting' => 'GNT',
                'cutter' => 'CTR',
                'dispenser' => 'DSP',
                'roll' => 'ROL',
                'bag' => 'BAG',
                'die' => 'DC',
                'cut' => '',
                'besar' => 'BSR',
                'kecil' => 'KCL',
                'sedang' => 'SDG',
                'polos' => 'PLS',
            ];

            if (isset($specialMappings[$lowerToken])) {
                if ($specialMappings[$lowerToken] !== '') {
                    $suffixParts[] = $specialMappings[$lowerToken];
                }
            } else {
                // Aturan konsonan untuk kata umum
                $cleanToken = preg_replace('/[^a-zA-Z0-9]/', '', $token);
                if (strlen($cleanToken) > 0) {
                    if (strlen($cleanToken) <= 3) {
                        $abbr = strtoupper($cleanToken);
                    } else {
                        $first = strtoupper(substr($cleanToken, 0, 1));
                        $rest = substr($cleanToken, 1);
                        preg_match_all('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ0-9]/', $rest, $matches);
                        $consonants = implode('', $matches[0]);
                        $abbr = $first . strtoupper($consonants);
                        if (strlen($abbr) > 3) {
                            $abbr = substr($abbr, 0, 3);
                        }
                    }
                    $suffixParts[] = $abbr;
                }
            }
        }

        $suffixParts = array_filter($suffixParts);

        if (!empty($suffixParts)) {
            $baseSku = $prefix . '-' . implode('-', $suffixParts);
        } else {
            $baseSku = $prefix . '-PRD';
        }

        // Pastikan unik
        $sku = $baseSku;
        $counter = 1;
        while (Produk::query()->where('sku', $sku)->exists()) {
            $sku = $baseSku . '-' . $counter;
            $counter++;
        }

        return strtoupper($sku);
    }
}
