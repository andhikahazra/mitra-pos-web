@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" id="section-supplier-show">
    <div class="section-head">
        <div>
            <h1>Detail Supplier</h1>
            <p>Informasi lengkap dan riwayat transaksi pengadaan dari **{{ $supplier->nama }}**.</p>
        </div>
        <div class="header-actions">
            <a class="btn btn-ghost" href="{{ route('supplier.index') }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <a class="btn btn-primary" href="{{ route('supplier.edit', $supplier) }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Supplier
            </a>
        </div>
    </div>

    <!-- Informasi Supplier & Statistik (Gabungan) -->
    <article class="panel-card">
        <div class="panel-head">
            <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-200">Informasi Supplier</h2>
            <span class="status-pill success inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a7 7 0 11-2.38 13.55 1 1 0 01-.11-1.83 2 2 0 002.73-3.3A5 5 0 105 9a1 1 0 01-2 0 7 7 0 017-7z" clip-rule="evenodd"/>
                </svg>
                Mitra
            </span>
        </div>
        <div class="p-5">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Nama Supplier -->
                <div class="md:col-span-2 lg:col-span-2">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V19a2 2 0 00-2-2H7a2 2 0 00-2 2v2m14-6h-8m4-4h-4m12-8V5a2 2 0 00-2-2H7a2 2 0 00-2 2v2"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide mb-1">Nama Supplier</p>
                            <p class="m-0 text-base font-bold text-slate-800 dark:text-zinc-200">{{ $supplier->nama }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Pengadaan -->
                <div class="text-center p-4 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border border-blue-100 dark:border-blue-800">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="m-0 text-xs font-medium text-blue-700 dark:text-blue-300 uppercase tracking-wide">Total Pengadaan</p>
                    <p class="m-0 text-2xl font-bold text-blue-900 dark:text-blue-200">{{ $supplier->barangMasuk->count() }}</p>
                    <p class="m-0 mt-1 text-xs text-blue-600 dark:text-blue-400">Dokumen</p>
                </div>

                <!-- Terakhir Pengadaan -->
                <div class="text-center p-4 rounded-lg bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/30 dark:to-green-900/30 border border-emerald-100 dark:border-emerald-800">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-8 4h16m-8 4v14m-6-6l2 2 4-4"/>
                        </svg>
                    </div>
                    <p class="m-0 text-xs font-medium text-emerald-700 dark:text-emerald-300 uppercase tracking-wide">Terakhir Pengadaan</p>
                    <p class="m-0 text-lg font-bold text-emerald-900 dark:text-emerald-200">
                        {{ $supplier->barangMasuk->count() > 0 && $supplier->barangMasuk->first()? ($supplier->barangMasuk->first()->tanggal_terima ? $supplier->barangMasuk->first()->tanggal_terima->format('d M Y') : '-') : '-' }}
                    </p>
                    <p class="m-0 mt-1 text-xs text-emerald-600 dark:text-emerald-400">tercatat</p>
                </div>
            </div>

            <div class="h-px bg-slate-200 dark:bg-zinc-800 my-6"></div>

            <!-- Kontak & Lokasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-slate-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Kontak</p>
                        <p class="m-0 text-sm text-slate-700 dark:text-zinc-300">{{ $supplier->no_telp ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-slate-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.994 1.994 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="m-0 text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Lokasi</p>
                        <p class="m-0 text-sm text-slate-700 dark:text-zinc-300">{{ $supplier->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Riwayat Barang Masuk -->
    <article class="panel-card mt-6">
        <div class="panel-head">
            <div>
                <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-200">Riwayat 10 Pengadaan Terakhir</h2>
                <p class="m-0 mt-1 text-xs text-slate-500 dark:text-zinc-400">Semua transaksi pengadaan dari supplier ini</p>
            </div>
            <span class="status-pill success inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 7a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm1 4a1 1 0 100 2h.01a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
                Barang Masuk
            </span>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">No. Dokumen</th>
                            <th class="pb-3 text-left text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Tanggal Terima</th>
                            <th class="pb-3 text-left text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Diterima Oleh</th>
                            <th class="pb-3 text-left text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Status</th>
                            <th class="pb-3 text-right text-xs font-medium text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-zinc-800">
                        @forelse($supplier->barangMasuk as $bm)
                            <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-slate-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <span class="font-mono text-sm font-medium text-slate-800 dark:text-zinc-200">{{ $bm->kode }}</span>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-8 4h16m-8 4v14m-6-6l2 2 4-4"/>
                                        </svg>
                                        <span class="text-sm text-slate-700 dark:text-zinc-300">{{ $bm->tanggal_terima ? $bm->tanggal_terima->format('d M Y') : '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center">
                                            <span class="text-xs font-medium text-slate-600 dark:text-zinc-400">{{ substr($bm->user->nama ?? '-', 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm text-slate-700 dark:text-zinc-300">{{ $bm->user->nama ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-4">
                                    @if(strtolower($bm->status) === 'disetujui')
                                        <span class="status-pill success inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Disetujui
                                        </span>
                                    @elseif($bm->status === 'menunggu')
                                        <span class="status-pill warning inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 102 0V6zm-1 6a1 1 0 100 2h.01a1 1 0 100-2H10z" clip-rule="evenodd"/>
                                            </svg>
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="status-pill danger inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip_clip-rule="evenodd"/>
                                            </svg>
                                            {{ ucfirst($bm->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 text-right">
                                    <a class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800/50 transition-colors" href="{{ route('barang-masuk.show', $bm) }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.295.955-.977 1.836-1.906 2.556M12 17v.01"/>
                                        </svg>
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9h8M8 13h8m-4-6v12"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="m-0 text-sm font-medium text-slate-600 dark:text-zinc-400">Belum ada riwayat pengadaan dari supplier ini.</p>
                                            <p class="m-0 text-xs text-slate-500 dark:text-zinc-500 mt-1">Transaksi pengadaan akan muncul di sini setelah diproses</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        
            @if($supplier->barangMasuk->count() > 0)
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="m-0 text-sm font-medium text-blue-900 dark:text-blue-200">Tips!</p>
                            <p class="m-0 text-xs text-blue-700 dark:text-blue-300 mt-0.5">Klik "Lihat Detail" untuk melihat informasi lengkap pengadaan, termasuk item yang diterima dan jumlah.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </article>
</section>
@endsection

