@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-produk-show">
        <div class="section-head">
            <div>
                <h1>Detail Produk</h1>
                <p>Ringkasan lengkap data produk, dimensi, dan foto.</p>
            </div>
            <div class="header-actions">
                <a class="btn btn-ghost" href="{{ route('produk.index') }}">Kembali</a>
                <a class="btn btn-primary" href="{{ route('produk.edit', $produk) }}">Edit Produk</a>
            </div>
        </div>

        <div class="overview-grid">
            <article class="panel-card p-4">
                <h2 class="m-0 text-base font-semibold text-slate-800">Informasi Utama</h2>
                <ul class="metric-list mt-3">
                    <li><span>Nama</span><strong>{{ $produk->nama }}</strong></li>
                    <li><span>SKU</span><strong>{{ $produk->sku }}</strong></li>
                    <li><span>Kategori</span><strong>{{ $produk->kategori->nama ?? '-' }}</strong></li>
                    <li><span>Tipe</span><strong>{{ $produk->tipe_produk }}</strong></li>
                    <li><span>Status</span><strong>{{ $produk->status ? 'Aktif' : 'Nonaktif' }}</strong></li>
                </ul>
            </article>

            <article class="panel-card p-4">
                <h2 class="m-0 text-base font-semibold text-slate-800">Harga dan Stok</h2>
                <ul class="metric-list mt-3">
                    <li><span>Harga</span><strong>Rp {{ number_format((float) $produk->harga, 0, ',', '.') }}</strong></li>
                    <li><span>Stok</span><strong>{{ (int) $produk->stok }}</strong></li>
                </ul>
            </article>

            <article class="panel-card p-4">
                <h2 class="m-0 text-base font-semibold text-slate-800">Dimensi</h2>
                @if ($produk->dimensi)
                    <ul class="metric-list mt-3">
                        <li><span>Panjang</span><strong>{{ $produk->dimensi->panjang }}</strong></li>
                        <li><span>Lebar</span><strong>{{ $produk->dimensi->lebar }}</strong></li>
                        <li><span>Tinggi</span><strong>{{ $produk->dimensi->tinggi }}</strong></li>
                        <li><span>Volume</span><strong>{{ $produk->dimensi->volume }}</strong></li>
                    </ul>
                @else
                    <p class="mt-3 text-sm text-slate-500">Produk ini tidak memiliki data dimensi.</p>
                @endif
            </article>
        </div>

        <article class="panel-card p-4">
            <h2 class="m-0 text-base font-semibold text-slate-800">Foto Produk</h2>
            @if ($produk->foto->isEmpty())
                <p class="mt-3 text-sm text-slate-500">Belum ada foto produk.</p>
            @else
                <div class="product-photo-list mt-4">
                    @foreach ($produk->foto as $photo)
                        <div class="product-photo-item">
                            <div class="product-photo-thumb">
                                <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->path }}">
                            </div>
                            <div class="product-photo-meta">
                                <p class="product-photo-name">{{ basename($photo->path) }}</p>
                            </div>
                            <div class="product-photo-actions">
                                @if ($photo->is_primary)
                                    <span class="status-pill success">Primary</span>
                                @else
                                    <span class="status-pill info">Foto</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    </section>
@endsection
