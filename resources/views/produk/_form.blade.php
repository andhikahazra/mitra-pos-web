@php
    $isEdit = isset($produk) && $produk;
    $selectedType = old('tipe_produk', $isEdit ? $produk->tipe_produk : 'stock');
    $hasDimension = old('has_dimension', $isEdit && ($produk->panjang !== null || $produk->lebar !== null || $produk->tinggi !== null) ? '1' : '0') === '1';
    $hasPhoto = old('has_photo', $isEdit && $produk->foto ? '1' : '0') === '1';
@endphp

<form id="productForm" method="POST" enctype="multipart/form-data" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <article class="product-form-card">

        {{-- Section 1: Informasi Produk --}}
        <div class="p-5">
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="m-0 text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase tracking-wide">Informasi Produk</p>
                    <p class="m-0 text-[11px] text-slate-400 dark:text-zinc-500">Data inti untuk identitas master produk.</p>
                </div>
            </div>

            <div class="product-form-grid">
                <div class="flex flex-col gap-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Nama Produk <span class="text-red-500">*</span></label>
                    <input class="field" name="nama" id="productName" placeholder="Contoh: Sabun Mandi Lifebuoy..." value="{{ old('nama', $isEdit ? $produk->nama : '') }}" required>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kategori <span class="text-red-500">*</span></label>
                    <select class="field" name="kategori_id" id="productCategory" onchange="toggleNewCategory(this.value)" required>
                        <option value="">Pilih kategori...</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}" @selected((string) old('kategori_id', $isEdit ? $produk->kategori_id : '') === (string) $item->id)>{{ $item->nama }}</option>
                        @endforeach
                        <option value="new" @selected(old('kategori_id') === 'new')>-- Tambah Kategori Baru --</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tipe Produk <span class="text-red-500">*</span></label>
                    <select class="field" name="tipe_produk" id="productType" required>
                        <option value="stock" @selected($selectedType === 'stock')>Stock</option>
                        <option value="non-stock" @selected($selectedType === 'non-stock')>Non Stock</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5 sm:col-span-2" id="newKategoriWrapper" style="display: {{ old('kategori_id') === 'new' ? 'block' : 'none' }}">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Nama Kategori Baru <span class="text-red-500">*</span></label>
                    <input class="field" name="new_kategori" id="newKategoriName" placeholder="Masukkan nama kategori baru..." value="{{ old('new_kategori') }}">
                </div>

                <div class="flex flex-col gap-1.5 sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">SKU (Stock Keeping Unit)</label>
                    <input class="field font-mono bg-slate-50 dark:bg-zinc-900/50" name="sku" id="productSku" value="{{ $isEdit ? $produk->sku : '[Otomatis setelah disimpan]' }}" readonly>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200 dark:bg-zinc-800"></div>

        {{-- Section 2: Harga, Stok, Status --}}
        <div class="p-5">
            <div class="flex items-center gap-2.5 mb-4">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9V6a5 5 0 0110 0v3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="m-0 text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase tracking-wide">Harga, Stok & Status</p>
                    <p class="m-0 text-[11px] text-slate-400 dark:text-zinc-500">Komponen komersial dan status aktif produk.</p>
                </div>
            </div>

            <div class="grid gap-5 grid-cols-1 sm:grid-cols-3">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 dark:text-zinc-500">Rp</span>
                        <input class="field pl-12 font-semibold text-slate-800 dark:text-zinc-100" name="harga" id="productPrice" type="text" placeholder="0" value="{{ old('harga', $isEdit ? number_format($produk->harga, 0, ',', '.') : '') }}" required>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Stok Tersedia</label>
                    <div class="relative">
                        <input class="field bg-slate-50 dark:bg-zinc-900/50" name="stok_disabled" id="productStock" type="text" value="{{ old('stok', $isEdit ? number_format($produk->stok, 0, ',', '.') : 0) }}" disabled readonly>
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider bg-slate-100 dark:bg-zinc-800">
                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            Locked
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5 flex items-center gap-1.5">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Otomatis dikelola via Barang Masuk & Penjualan.
                    </p>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status Produk <span class="text-red-500">*</span></label>
                    <select class="field" name="status" id="productStatus" required>
                        <option value="1" @selected((string) old('status', $isEdit ? (int) $produk->status : '1') === '1')>Aktif</option>
                        <option value="0" @selected((string) old('status', $isEdit ? (int) $produk->status : '1') === '0')>Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200 dark:bg-zinc-800"></div>

        {{-- Section 3: Dimensi --}}
        <div class="p-5">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h5M4 8l5 5m13-5v16a2 2 0 01-2 2H5a2 2 0 01-2-2V8m14 0h-5m5 0v9M4 8h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="m-0 text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase tracking-wide">Dimensi & Ukuran</p>
                        <p class="m-0 text-[11px] text-slate-400 dark:text-zinc-500">Atur ukuran fisik untuk perhitungan volumetrik.</p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input id="productHasDimension" name="has_dimension" value="1" type="checkbox" @checked($hasDimension) class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="toggle-state-badge {{ $hasDimension ? 'on' : 'off' }}" id="dimensionToggleState">{{ $hasDimension ? 'Enabled' : 'Disabled' }}</span>
                </label>
            </div>

            <fieldset class="product-form-grid feature-fieldset {{ $hasDimension ? 'is-enabled' : 'is-disabled' }}" id="productDimensionFields" @disabled(!$hasDimension)>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Panjang (cm)</label>
                    <input class="field" name="panjang" id="dimensionLength" type="number" min="0" step="0.01" placeholder="0" value="{{ old('panjang', $isEdit ? $produk->panjang : '') }}">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Lebar (cm)</label>
                    <input class="field" name="lebar" id="dimensionWidth" type="number" min="0" step="0.01" placeholder="0" value="{{ old('lebar', $isEdit ? $produk->lebar : '') }}">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tinggi (cm)</label>
                    <input class="field" name="tinggi" id="dimensionHeight" type="number" min="0" step="0.01" placeholder="0" value="{{ old('tinggi', $isEdit ? $produk->tinggi : '') }}">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Volume (kg)</label>
                    <input class="field" name="volume" id="dimensionVolume" type="number" min="0" step="0.01" placeholder="0" value="{{ old('volume', $isEdit ? $produk->volume : '') }}">
                </div>
            </fieldset>
        </div>

        <div class="h-px bg-slate-200 dark:bg-zinc-800"></div>

        {{-- Section 4: Foto --}}
        <div class="p-5">
            <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.293-1.293a1 1 0 012.414 2.414L6 20H4a2 2 0 01-2-2V6a2 2 0 012-2h2.586a1 1 0 112.414 0L5.5 5.5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="m-0 text-xs font-semibold text-slate-600 dark:text-zinc-300 uppercase tracking-wide">Foto Produk</p>
                        <p class="m-0 text-[11px] text-slate-400 dark:text-zinc-500">Unggah foto representatif untuk katalog & identifikasi.</p>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input id="productHasPhoto" name="has_photo" value="1" type="checkbox" @checked($hasPhoto) class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="toggle-state-badge {{ $hasPhoto ? 'on' : 'off' }}" id="photoToggleState">{{ $hasPhoto ? 'Enabled' : 'Disabled' }}</span>
                </label>
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
                <small class="text-slate-500 dark:text-zinc-500">Pilih berkas foto produk untuk diunggah (maksimal 5 MB).</small>
            </fieldset>
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

    <div class="form-actions flex items-center justify-end gap-3 mt-6">
        <a class="btn btn-ghost px-5" href="{{ route('produk.index') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Batal
        </a>
        <button class="btn btn-primary px-6" type="submit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $isEdit ? 'Update Produk' : 'Simpan Produk' }}
        </button>
    </div>
</form>
