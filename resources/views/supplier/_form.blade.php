@php $isEdit = isset($supplier); @endphp

<form class="product-form-layout" method="POST"
      action="{{ $isEdit ? route('supplier.update', $supplier) : route('supplier.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <article class="product-form-card full">
        <div class="product-form-head">
            <h3>Konfigurasi Identitas Supplier</h3>
            <p>Pastikan nama dan nomor telepon aktif untuk kemudahan koordinasi pengadaan barang.</p>
        </div>

        <div class="product-form-grid">
            <label class="full-field">Nama Resmi Supplier
                <input class="field" name="nama" required placeholder="Contoh: PT. Sumber Makmur Abadi"
                       value="{{ old('nama', $isEdit ? $supplier->nama : '') }}">
            </label>

            <label>Nomor Telepon / WhatsApp
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.81 12.81 0 00.6 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.6A2 2 0 0122 16.92z"/></svg>
                    </span>
                    <input class="field pl-10" name="no_telp" placeholder="0812..."
                           value="{{ old('no_telp', $isEdit ? $supplier->no_telp : '') }}">
                </div>
            </label>

            <label class="full-field">Alamat Kantor / Gudang
                <textarea class="field min-h-[120px]" name="alamat" placeholder="Tuliskan alamat lengkap untuk keperluan pengiriman data atau penjemputan barang...">{{ old('alamat', $isEdit ? $supplier->alamat : '') }}</textarea>
            </label>
        </div>
    </article>

    <div class="form-actions full mt-8">
        <a class="btn btn-ghost px-6" href="{{ route('supplier.index') }}">Batal</a>
        <button class="btn btn-primary px-8" type="submit">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Supplier' }}
        </button>
    </div>
</form>
