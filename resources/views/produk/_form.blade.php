@php
    $isEdit = isset($produk) && $produk;
    $selectedType = old('tipe_produk', $isEdit ? $produk->tipe_produk : 'stock');
    $hasDimension = old('has_dimension', $isEdit && ($produk->panjang !== null || $produk->lebar !== null || $produk->tinggi !== null) ? '1' : '0') === '1';
    $hasPhoto = old('has_photo', $isEdit && $produk->foto ? '1' : '0') === '1';
@endphp

<form id="productForm" class="product-form-layout" method="POST" enctype="multipart/form-data" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <article class="product-form-card">
        <div class="product-form-head">
            <h3>Informasi Produk</h3>
            <p>Isi data inti produk untuk identitas master.</p>
        </div>

        <div class="product-form-grid">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Nama Produk</label>
                <input class="field" name="nama" id="productName" placeholder="Contoh: Sabun Mandi Lifebuoy..." value="{{ old('nama', $isEdit ? $produk->nama : '') }}" required>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">SKU (Stock Keeping Unit)</label>
                <input class="field font-mono" name="sku" id="productSku" value="{{ $isEdit ? $produk->sku : '[Otomatis setelah disimpan]' }}" readonly>
            </div>



            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Kategori</label>
                <select class="field" name="kategori_id" id="productCategory" onchange="toggleNewCategory(this.value)" required>
                    <option value="">Pilih kategori...</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->id }}" @selected((string) old('kategori_id', $isEdit ? $produk->kategori_id : '') === (string) $item->id)>{{ $item->nama }}</option>
                    @endforeach
                    <option value="new" @selected(old('kategori_id') === 'new')>-- Tambah Kategori Baru --</option>
                </select>
            </div>

            <div class="flex flex-col gap-2" id="newKategoriWrapper" style="display: {{ old('kategori_id') === 'new' ? 'block' : 'none' }}">
                <label class="text-sm font-medium text-slate-700">Nama Kategori Baru</label>
                <input class="field" name="new_kategori" id="newKategoriName" placeholder="Masukkan nama kategori baru..." value="{{ old('new_kategori') }}">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Tipe Produk</label>
                <select class="field" name="tipe_produk" id="productType" required>
                    <option value="stock" @selected($selectedType === 'stock')>Stock</option>
                    <option value="non-stock" @selected($selectedType === 'non-stock')>Non Stock</option>
                </select>
            </div>
        </div>
    </article>

    <script>
        function toggleNewCategory(value) {
            const wrapper = document.getElementById('newKategoriWrapper');
            const input = document.getElementById('newKategoriName');
            
            if (value === 'new') {
                wrapper.style.display = 'block';
                input.setAttribute('required', 'required');
                input.focus();
            } else {
                wrapper.style.display = 'none';
                input.removeAttribute('required');
            }
        }
    </script>

    <article class="product-form-card">
        <div class="product-form-head">
            <h3>Harga, Stok, dan Status</h3>
            <p>Atur komponen komersial dan status aktif produk.</p>
        </div>

        <div class="product-form-grid">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Harga Jual (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">Rp</span>
                    <input class="field pl-10" name="harga" id="productPrice" type="text" placeholder="0" value="{{ old('harga', $isEdit ? number_format($produk->harga, 0, ',', '.') : '') }}" required>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Stok Tersedia</label>
                <div class="relative">
                    <input class="field" name="stok_disabled" id="productStock" type="text" value="{{ old('stok', $isEdit ? number_format($produk->stok, 0, ',', '.') : 0) }}" disabled readonly>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Locked</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-1 leading-tight">Dikelola otomatis via dokumen Barang Masuk & Penjualan.</p>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Status Produk</label>
                <select class="field" name="status" id="productStatus" required>
                    <option value="1" @selected((string) old('status', $isEdit ? (int) $produk->status : '1') === '1')>Aktif</option>
                    <option value="0" @selected((string) old('status', $isEdit ? (int) $produk->status : '1') === '0')>Nonaktif</option>
                </select>
            </div>
        </div>
    </article>

    <article class="product-form-card">
        <div class="product-form-toggle">
            <label class="inline-flex items-center gap-2">
                <input id="productHasDimension" name="has_dimension" value="1" type="checkbox" @checked($hasDimension)>
                <span>Produk memiliki dimensi</span>
            </label>
            <span class="toggle-state-badge {{ $hasDimension ? 'on' : 'off' }}" id="dimensionToggleState">{{ $hasDimension ? 'Enabled' : 'Disabled' }}</span>
        </div>

        <fieldset class="product-form-grid feature-fieldset {{ $hasDimension ? 'is-enabled' : 'is-disabled' }}" id="productDimensionFields" @disabled(!$hasDimension)>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Panjang (cm)</label>
                <input class="field" name="panjang" id="dimensionLength" type="number" min="0" step="0.01" placeholder="0" value="{{ old('panjang', $isEdit ? $produk->panjang : '') }}">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Lebar (cm)</label>
                <input class="field" name="lebar" id="dimensionWidth" type="number" min="0" step="0.01" placeholder="0" value="{{ old('lebar', $isEdit ? $produk->lebar : '') }}">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Tinggi (cm)</label>
                <input class="field" name="tinggi" id="dimensionHeight" type="number" min="0" step="0.01" placeholder="0" value="{{ old('tinggi', $isEdit ? $produk->tinggi : '') }}">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-slate-700">Volume (cm3)</label>
                <input class="field" name="volume" id="dimensionVolume" type="number" min="0" step="0.01" placeholder="0" value="{{ old('volume', $isEdit ? $produk->volume : '') }}">
            </div>
        </fieldset>
    </article>

    <article class="product-form-card full">
        <div class="product-form-toggle">
            <label class="inline-flex items-center gap-2">
                <input id="productHasPhoto" name="has_photo" value="1" type="checkbox" @checked($hasPhoto)>
                <span>Produk memiliki foto</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="toggle-state-badge {{ $hasPhoto ? 'on' : 'off' }}" id="photoToggleState">{{ $hasPhoto ? 'Enabled' : 'Disabled' }}</span>
                <small class="text-slate-500" id="productPhotoCurrent">{{ $isEdit && $produk->foto ? 1 : 0 }} foto tersimpan.</small>
            </div>
        </div>

        <fieldset class="feature-fieldset {{ $hasPhoto ? 'is-enabled' : 'is-disabled' }}" id="productPhotoFields" @disabled(!$hasPhoto)>
            <input class="hidden" name="foto" id="productPhotoFile" type="file" accept="image/*">
            <div class="product-photo-list" id="productPhotoList">
                @if ($isEdit && $produk->foto)
                    <div class="product-photo-item transition-opacity duration-300" id="existingPhotoItem">
                        <div class="product-photo-thumb cursor-zoom-in transition-all duration-300 hover:scale-[1.03]" data-zoomable>
                            <img src="{{ Str::startsWith($produk->foto, ['http://', 'https://']) ? $produk->foto : asset('storage/' . $produk->foto) }}" alt="{{ $produk->foto }}">
                        </div>
                        <div class="product-photo-meta">
                            <p class="product-photo-name">{{ basename($produk->foto) }}</p>
                        </div>
                        <div class="product-photo-actions">
                            <label class="product-photo-primary">
                                <input type="checkbox" name="remove_photo" id="removePhotoCheckbox" value="1">
                                Hapus Foto Saat Ini
                            </label>
                        </div>
                    </div>
                @endif
                <label class="product-photo-add" for="productPhotoFile">
                    <span>{{ $isEdit && $produk->foto ? 'Ganti Foto' : '+ Upload Foto' }}</span>
                </label>
            </div>
            <small class="text-slate-500">Pilih berkas foto produk untuk diunggah (maksimal 5 MB).</small>
        </fieldset>
    </article>

    <div class="form-actions full">
        <a class="btn btn-ghost px-5" href="{{ route('produk.index') }}">Batal</a>
        <button class="btn btn-primary px-6" type="submit">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan Produk
        </button>
    </div>
</form>
