@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-product-stock-card">
    <div class="section-head mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="tag blue">Kartu Stok Inventori</span>
                <span class="status-pill {{ ($summary['total_stok'] ?? 0) > 0 ? 'success' : 'danger' }}">
                    {{ ($summary['total_stok'] ?? 0) > 0 ? 'In Stock' : 'Out of Stock' }}
                </span>
            </div>
            <h1>{{ $produk->nama }}</h1>
            <p class="font-mono text-sm text-slate-400 dark:text-zinc-500">SKU: {{ $produk->sku ?? '-' }} | Kategori: {{ $produk->kategori->nama ?? '-' }}</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('stok-batch.index') }}">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7 7l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="overview-grid mb-6">
        <article class="panel-card p-5 border-l-4 border-l-blue-500">
            <p class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Total Stok Tersedia</p>
            <h2 class="text-3xl font-extrabold text-slate-800 dark:text-zinc-200 m-0">{{ number_format($summary['total_stok'], 0, ',', '.') }} <span class="text-sm font-normal text-slate-400 dark:text-zinc-500 uppercase">Unit</span></h2>
        </article>

        <article class="panel-card p-5 border-l-4 border-l-orange-500">
            <p class="text-xs font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Jumlah Batch Aktif</p>
            <h2 class="text-3xl font-extrabold text-slate-800 dark:text-zinc-200 m-0">{{ $summary['total_batch'] }} <span class="text-sm font-normal text-slate-400 dark:text-zinc-500 uppercase">Batch</span></h2>
        </article>
    </div>

    {{-- ACTIVE BATCHES --}}
    <article class="panel-card mb-6">
        <div class="panel-head">
            <div>
                <h2 class="m-0 font-bold text-lg">Rincian Batch Aktif</h2>
                <p class="text-xs text-slate-500 dark:text-zinc-400">Daftar stok berdasarkan tanggal masuk dan harga beli.</p>
            </div>
            <span class="tag green">Available</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Tanggal Masuk</th>
                        <th>Harga Beli</th>
                        <th>Qty Awal</th>
                        <th>Sisa Stok</th>
                        <th>Doc Ref.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeBatches as $batch)
                        <tr>
                            <td class="font-mono text-xs font-bold text-slate-400 dark:text-zinc-500">#{{ $batch->id }}</td>
                            <td>{{ $batch->tanggal_masuk ? $batch->tanggal_masuk->format('d M Y') : '-' }}</td>
                            <td class="font-bold text-slate-700 dark:text-zinc-300">Rp {{ number_format($batch->harga_beli, 0, ',', '.') }}</td>
                            <td class="text-slate-400 dark:text-zinc-500">{{ $batch->detailBarangMasuk->jumlah ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 bg-slate-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        @php 
                                            $awal = $batch->detailBarangMasuk->jumlah ?? 1;
                                            $persen = ($batch->qty_sisa / $awal) * 100;
                                        @endphp
                                        <div class="h-full bg-green-500" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <span class="font-extrabold text-green-600">{{ $batch->qty_sisa }}</span>
                                </div>
                            </td>
                            <td>
                                @if($batch->detailBarangMasuk && $batch->detailBarangMasuk->barangMasuk)
                                    <a href="{{ route('barang-masuk.show', $batch->detailBarangMasuk->barangMasuk) }}" class="underline decoration-slate-200 text-blue-600">
                                        #{{ $batch->detailBarangMasuk->barangMasuk->kode }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-400 dark:text-zinc-500 py-12">Tidak ada batch aktif untuk produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

</section>
@endsection
