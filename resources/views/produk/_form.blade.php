@php
    $isEdit = isset($produk) && $produk;
    $selectedType = old('tipe_produk', $isEdit ? $produk->tipe_produk : 'stock');
    $hasDimension = old('has_dimension', $isEdit && $produk->dimensi ? '1' : '0') === '1';
    $hasPhoto = old('has_photo', $isEdit && $produk->foto->isNotEmpty() ? '1' : '0') === '1';
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
            <label>Nama Produk
                <input class="field" name="nama" id="productName" placeholder="Contoh: Sabun Mandi Lifebuoy..." value="{{ old('nama', $isEdit ? $produk->nama : '') }}" required>
            </label>

            <label>SKU (Stock Keeping Unit)
                <input class="field font-mono" name="sku" id="productSku" placeholder="Contoh: PRD-0001..." value="{{ old('sku', $isEdit ? $produk->sku : ($suggestedSku ?? '')) }}" required>
            </label>



            <label>Kategori
                <select class="field" name="kategori_id" id="productCategory" onchange="toggleNewCategory(this.value)" required>
                    <option value="">Pilih kategori...</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->id }}" @selected((string) old('kategori_id', $isEdit ? $produk->kategori_id : '') === (string) $item->id)>{{ $item->nama }}</option>
                    @endforeach
                    <option value="new" @selected(old('kategori_id') === 'new')>-- Tambah Kategori Baru --</option>
                </select>
            </label>

            <label id="newKategoriWrapper" style="display: {{ old('kategori_id') === 'new' ? 'block' : 'none' }}">Nama Kategori Baru
                <input class="field" name="new_kategori" id="newKategoriName" placeholder="Masukkan nama kategori baru..." value="{{ old('new_kategori') }}">
            </label>

            <label>Tipe Produk
                <select class="field" name="tipe_produk" id="productType" required>
                    <option value="stock" @selected($selectedType === 'stock')>Stock</option>
                    <option value="non-stock" @selected($selectedType === 'non-stock')>Non Stock</option>
                </select>
            </label>
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
            <label>Harga Jual (Rp)
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">Rp</span>
                    <input class="field pl-10" name="harga" id="productPrice" type="number" min="0" placeholder="0" value="{{ old('harga', $isEdit ? $produk->harga : 0) }}" required>
                </div>
            </label>

            <label>Stok Tersedia
                <div class="relative">
                    <input class="field bg-slate-50 cursor-not-allowed" name="stok_disabled" id="productStock" type="number" value="{{ old('stok', $isEdit ? $produk->stok : 0) }}" disabled readonly>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Locked</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-1 leading-tight">Dikelola otomatis via dokumen Barang Masuk & Penjualan.</p>
            </label>

            <label>Status Produk
                <select class="field" name="status" id="productStatus" required>
                    <option value="1" @selected((string) old('status', $isEdit ? (int) $produk->status : '1') === '1')>Aktif</option>
                    <option value="0" @selected((string) old('status', $isEdit ? (int) $produk->status : '1') === '0')>Nonaktif</option>
                </select>
            </label>
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
            <label>Panjang (cm)
                <input class="field" name="panjang" id="dimensionLength" type="number" min="0" step="0.01" placeholder="0" value="{{ old('panjang', $isEdit && $produk->dimensi ? $produk->dimensi->panjang : '') }}">
            </label>

            <label>Lebar (cm)
                <input class="field" name="lebar" id="dimensionWidth" type="number" min="0" step="0.01" placeholder="0" value="{{ old('lebar', $isEdit && $produk->dimensi ? $produk->dimensi->lebar : '') }}">
            </label>

            <label>Tinggi (cm)
                <input class="field" name="tinggi" id="dimensionHeight" type="number" min="0" step="0.01" placeholder="0" value="{{ old('tinggi', $isEdit && $produk->dimensi ? $produk->dimensi->tinggi : '') }}">
            </label>

            <label>Volume (cm3)
                <input class="field" name="volume" id="dimensionVolume" type="number" min="0" step="0.01" placeholder="0" value="{{ old('volume', $isEdit && $produk->dimensi ? $produk->dimensi->volume : '') }}">
            </label>
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
                <small class="text-slate-500" id="productPhotoCurrent">{{ $isEdit ? $produk->foto->count() : 0 }} foto tersimpan.</small>
            </div>
        </div>

        <fieldset class="feature-fieldset {{ $hasPhoto ? 'is-enabled' : 'is-disabled' }}" id="productPhotoFields" @disabled(!$hasPhoto)>
            <input class="hidden" name="photos[]" id="productPhotoFile" type="file" accept="image/*" multiple>
            <div class="product-photo-list" id="productPhotoList">
                @foreach (($isEdit ? $produk->foto : collect()) as $photo)
                    <div class="product-photo-item">
                        <div class="product-photo-thumb">
                            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->path }}">
                        </div>
                        <div class="product-photo-meta">
                            <p class="product-photo-name">{{ basename($photo->path) }}</p>
                        </div>
                        <div class="product-photo-actions">
                            <label class="product-photo-primary">
                                <input type="radio" name="primary_existing_photo" value="{{ $photo->id }}" @checked($photo->is_primary)>
                                Primary
                            </label>
                            <label class="product-photo-primary">
                                <input type="checkbox" name="remove_photo_ids[]" value="{{ $photo->id }}">
                                Hapus
                            </label>
                        </div>
                    </div>
                @endforeach
                <label class="product-photo-add" for="productPhotoFile">
                    <span>+ Tambah Foto</span>
                </label>
            </div>
            <small class="text-slate-500">Pilih foto utama dengan radio dan tambahkan foto baru lewat kotak tambah.</small>
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
