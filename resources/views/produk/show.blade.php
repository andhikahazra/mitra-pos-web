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

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Informasi Utama -->
            <article class="panel-card">
                <div class="panel-head">
                    <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-200">Informasi Utama</h2>
                    <span class="status-pill {{ $produk->status ? 'success' : 'danger' }} text-xs">
                        {{ $produk->status ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Nama Produk</p>
                                <h3 class="m-0 mt-1 text-base font-bold text-slate-800 dark:text-zinc-200">{{ $produk->nama }}</h3>
                            </div>
                            <div class="text-right">
                                <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">SKU</p>
                                <p class="m-0 mt-1 text-sm font-mono text-slate-600 dark:text-zinc-300">{{ $produk->sku }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Kategori</p>
                                <p class="m-0 mt-1 text-sm text-slate-700 dark:text-zinc-300">{{ $produk->kategori->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Tipe Produk</p>
                                <p class="m-0 mt-1 text-sm text-slate-700 dark:text-zinc-300">{{ $produk->tipe_produk }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Harga dan Stok -->
            <article class="panel-card">
                <div class="panel-head">
                    <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-200">Harga dan Stok</h2>
                </div>
                <div class="p-5">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide mb-2">Harga Satuan</p>
                                <div class="w-20 h-20 mx-auto mb-2 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp</span>
                                </div>
                                <p class="m-0 mt-1 text-xl font-bold text-slate-800 dark:text-zinc-200">Rp {{ number_format((float) $produk->harga, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide mb-2">Stok Tersedia</p>
                                @php
                                    $stok = (int) $produk->stok;
                                    $stokPct = min((float) $stok, 100);
                                @endphp
                                <div class="flex items-center justify-center">
                                    <div class="relative w-20 h-20">
                                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                            <path class="text-slate-200 dark:text-zinc-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.8"/>
                                            <path class="{{ $stok > 10 ? 'text-green-500 dark:text-green-400' : ($stok > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-red-500 dark:text-red-400') }}" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.8" stroke-dasharray="{{ $stokPct }},{{ 100 - $stokPct }}" stroke-linecap="round"/>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-lg font-bold {{ $stok > 10 ? 'text-green-600 dark:text-green-400' : ($stok > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $stok }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="m-0 mt-2 text-xs text-center text-slate-500 dark:text-zinc-400">
                                    {{ $stok > 10 ? 'Stok Aman' : ($stok > 0 ? 'Stok Rendah' : 'Stok Habis') }}
                                </p>
                            </div>
                        </div>
                </div>
            </article>

            <!-- Dimensi -->
            <article class="panel-card lg:col-span-2">
                <div class="panel-head">
                    <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-200">Dimensi & Ukuran</h2>
                </div>
                <div class="p-5">
                    @if ($produk->panjang !== null || $produk->lebar !== null || $produk->tinggi !== null)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 rounded-lg bg-slate-50 dark:bg-zinc-800/50 transition-all hover:shadow-md">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h5M4 8l5 5m13-5v16a2 2 0 01-2 2H5a2 2 0 01-2-2V8m14 0h-5m5 0v9M4 8h16"/>
                                    </svg>
                                </div>
                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Panjang</p>
                                <p class="m-0 mt-1 text-lg font-bold text-slate-800 dark:text-zinc-200">{{ $produk->panjang }} <span class="text-xs font-normal text-slate-500 dark:text-zinc-400">cm</span></p>
                            </div>
                            <div class="text-center p-4 rounded-lg bg-slate-50 dark:bg-zinc-800/50 transition-all hover:shadow-md">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h5M4 8l5 5m13-5v16a2 2 0 01-2 2H5a2 2 0 01-2-2V8m14 0h-5m5 0v9M4 8h16"/>
                                    </svg>
                                </div>
                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Lebar</p>
                                <p class="m-0 mt-1 text-lg font-bold text-slate-800 dark:text-zinc-200">{{ $produk->lebar }} <span class="text-xs font-normal text-slate-500 dark:text-zinc-400">cm</span></p>
                            </div>
                            <div class="text-center p-4 rounded-lg bg-slate-50 dark:bg-zinc-800/50 transition-all hover:shadow-md">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h5M4 8l5 5m13-5v16a2 2 0 01-2 2H5a2 2 0 01-2-2V8m14 0h-5m5 0v9M4 8h16"/>
                                    </svg>
                                </div>
                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Tinggi</p>
                                <p class="m-0 mt-1 text-lg font-bold text-slate-800 dark:text-zinc-200">{{ $produk->tinggi }} <span class="text-xs font-normal text-slate-500 dark:text-zinc-400">cm</span></p>
                            </div>
                            <div class="text-center p-4 rounded-lg bg-slate-50 dark:bg-zinc-800/50 transition-all hover:shadow-md">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Volume</p>
                                <p class="m-0 mt-1 text-lg font-bold text-slate-800 dark:text-zinc-200">{{ $produk->volume }} <span class="text-xs font-normal text-slate-500 dark:text-zinc-400">kg</span></p>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h5M4 8l5 5m13-5v16a2 2 0 01-2 2H5a2 2 0 01-2-2V8m14 0h-5m5 0v9M4 8h16"/>
                                </svg>
                            </div>
                            <p class="m-0 text-sm text-slate-500 dark:text-zinc-400">Produk ini tidak memiliki data dimensi.</p>
                        </div>
                    @endif
                </div>
            </article>

            <!-- Foto Produk -->
            <article class="panel-card lg:col-span-2">
                <div class="panel-head">
                    <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-200">Foto Produk</h2>
                </div>
                <div class="p-5">
                    @if (!$produk->foto)
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.293-1.293a1 1 0 012.414 2.414L6 20H4a2 2 0 01-2-2V6a2 2 0 012-2h2.586a1 1 0 112.414 0L5.5 5.5"/>
                                </svg>
                            </div>
                            <p class="m-0 text-base font-medium text-slate-600 dark:text-zinc-400">Belum ada foto produk</p>
                            <p class="m-0 mt-1 text-sm text-slate-500 dark:text-zinc-500">Unggah foto produk untuk ditampilkan di katalog</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group relative">
                                <div class="relative overflow-hidden rounded-xl bg-white dark:bg-zinc-800 shadow-sm border border-slate-200 dark:border-zinc-700 transition-all duration-300 hover:shadow-lg">
                                    <div class="aspect-video bg-slate-50 dark:bg-zinc-700 flex items-center justify-center">
                                        <img src="{{ Str::startsWith($produk->foto, ['http://', 'https://']) ? $produk->foto : asset('storage/' . $produk->foto) }}" 
                                             alt="{{ $produk->foto }}" 
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="m-0 text-sm font-medium text-slate-700 dark:text-zinc-200 truncate max-w-[200px]">{{ basename($produk->foto) }}</p>
                                                <p class="m-0 mt-1 text-xs text-slate-500 dark:text-zinc-400">Ukuran: {{ $produk->foto ? 'Tersimpan' : 'Tidak ada' }}</p>
                                            </div>
                                            <span class="status-pill success inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Primary
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-lg p-2 shadow-md">
                                        <button onclick="window.open('{{ Str::startsWith($produk->foto, ['http://', 'https://']) ? $produk->foto : asset('storage/' . $produk->foto) }}', '_blank')" class="p-2 text-slate-600 dark:text-zinc-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.295.955-.977 1.836-1.906 2.556M12 17v.01"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-slate-50 dark:bg-zinc-800/50 border border-slate-200 dark:border-zinc-700">
                                    <h3 class="m-0 text-sm font-semibold text-slate-700 dark:text-zinc-200 mb-3">Deskripsi Foto</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.293-1.293a1 1 0 012.414 2.414L6 20H4a2 2 0 01-2-2V6a2 2 0 012-2h2.586a1 1 0 112.414 0L5.5 5.5"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="m-0 text-xs font-medium text-slate-600 dark:text-zinc-300">Path File</p>
                                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400 font-mono">{{ $produk->foto }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="m-0 text-xs font-medium text-slate-600 dark:text-zinc-300">Tipe File</p>
                                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400">{{ pathinfo($produk->foto)['extension'] ?? 'Unknown' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="m-0 text-xs font-medium text-blue-900 dark:text-blue-200">Tips!</p>
                                            <p class="m-0 mt-1 text-xs text-blue-700 dark:text-blue-300">Klik ikon mata untuk melihat foto dalam ukuran penuh</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </article>
        </div>
    </section>
@endsection

