@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-log-stok-show">
    <div class="section-head mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="tag neutral">Log Mutasi #{{ $logStok->id }}</span>
                <span class="status-pill {{ $logStok->tipe === 'masuk' ? 'success' : 'danger' }}">
                    Metode: {{ ucfirst($logStok->tipe) }}
                </span>
            </div>
            <h1>Detail Pergerakan Stok</h1>
            <p>Audit rincian audit mutasi stok barang untuk transparansi inventori.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('log-stok.index') }}">
            <svg viewBox="0 0 24 24" class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7 7l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Hero Highlight: Mutation Amount --}}
    <article class="panel-card mb-6 overflow-hidden">
        <div class="p-6 border-b border-slate-50">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="h-16 w-16 rounded-2xl flex items-center justify-center {{ $logStok->tipe === 'masuk' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                        @if($logStok->tipe === 'masuk')
                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14m7-7l-7 7-7-7"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5m7 7l-7-7-7 7"/></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Jumlah Mutasi</p>
                        <h2 class="text-4xl font-black {{ $logStok->tipe === 'masuk' ? 'text-green-600' : 'text-red-500' }} m-0">
                            {{ $logStok->tipe === 'masuk' ? '+' : '-' }}{{ number_format($logStok->jumlah, 0, ',', '.') }}
                            <span class="text-lg font-normal text-slate-400 ml-1">Unit</span>
                        </h2>
                    </div>
                </div>
                <div class="bg-slate-50 rounded-xl px-5 py-3 border border-slate-100 min-w-[200px]">
                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Status Pencatatan</p>
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></div>
                        <span class="text-sm font-bold text-slate-700">Terverifikasi Sistem</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-slate-100/30 px-6 py-3 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>{{ $logStok->keterangan }}</span>
        </div>
    </article>

    <div class="overview-grid mb-6">
        {{-- Card 1: Related Item --}}
        <article class="panel-card p-5">
            <div class="flex items-start gap-4">
                <div class="h-10 w-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg>
                </div>
                <div>
                    <h3 class="m-0 text-sm font-bold text-slate-800 uppercase tracking-tight">Informasi Barang</h3>
                    <p class="text-xl font-bold text-slate-700 mt-1 mb-0 leading-tight">{{ $logStok->produk->nama ?? '-' }}</p>
                    <div class="flex items-center gap-3 mt-4 text-xs font-mono">
                        <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded">SKU: {{ $logStok->produk->sku ?? '-' }}</span>
                        <span class="bg-slate-100 text-slate-500 px-2 py-1 rounded">{{ $logStok->produk->kategori->nama ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </article>

        {{-- Card 2: Logistics & Time --}}
        <article class="panel-card p-5">
            <div class="flex items-start gap-4">
                <div class="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="m-0 text-sm font-bold text-slate-800 uppercase tracking-tight">Waktu & Personel</h3>
                    <p class="text-xl font-bold text-slate-700 mt-1 mb-0 leading-tight">
                        {{ $logStok->tanggal ? $logStok->tanggal->format('d M Y, H:i') : '-' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-2">Diverifikasi oleh: 
                        <span class="font-bold text-slate-600">
                            {{ $logStok->transaksi->user->nama ?? ($logStok->barangMasuk->user->nama ?? 'Sistem') }}
                        </span>
                    </p>
                </div>
            </div>
        </article>
    </div>

    {{-- Source Document Card --}}
    @if($logStok->barangMasuk || $logStok->transaksi)
        <article class="panel-card overflow-hidden">
            <div class="panel-head border-b border-slate-50">
                <h2 class="m-0 text-base font-bold">Dokumen Sumber</h2>
                <span class="tag neutral">Reference ID</span>
            </div>
            
            <div class="p-6 bg-slate-50/50">
                <div class="flex flex-wrap items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="h-16 w-12 bg-white rounded border border-slate-100 shadow-sm flex items-center justify-center text-slate-300">
                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                                {{ $logStok->transaksi ? 'Invoice Penjualan' : 'Dokumen Barang Masuk' }}
                            </p>
                            <h4 class="text-2xl font-mono font-black text-slate-800 m-0">
                                #{{ $logStok->transaksi->kode ?? ($logStok->barangMasuk->kode ?? '-') }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $logStok->transaksi ? 'Pelanggan: ' . ($logStok->transaksi->nama_pelanggan ?? 'Umum') : 'Supplier: ' . ($logStok->barangMasuk->supplier->nama ?? '-') }}
                            </p>
                        </div>
                    </div>
                    
                    <a href="{{ $logStok->transaksi ? route('transaksi.show', $logStok->transaksi) : route('barang-masuk.show', $logStok->barangMasuk) }}" 
                       class="btn btn-primary px-8 group">
                       Lihat Dokumen
                       <svg viewBox="0 0 24 24" class="h-4 w-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 7l5 5-5 5M6 12h12"/></svg>
                    </a>
                </div>
            </div>
        </article>
    @endif
</section>
@endsection
