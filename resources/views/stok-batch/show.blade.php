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
            <p class="font-mono text-sm text-slate-400">SKU: {{ $produk->sku ?? '-' }} | Kategori: {{ $produk->kategori->nama ?? '-' }}</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('stok-batch.index') }}">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7 7l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="overview-grid mb-6">
        <article class="panel-card p-5 border-l-4 border-l-blue-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Stok Tersedia</p>
            <h2 class="text-3xl font-extrabold text-slate-800 m-0">{{ number_format($summary['total_stok'], 0, ',', '.') }} <span class="text-sm font-normal text-slate-400 uppercase">Unit</span></h2>
        </article>

        <article class="panel-card p-5 border-l-4 border-l-orange-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jumlah Batch Aktif</p>
            <h2 class="text-3xl font-extrabold text-slate-800 m-0">{{ $summary['total_batch'] }} <span class="text-sm font-normal text-slate-400 uppercase">Batch</span></h2>
        </article>
    </div>

    {{-- ACTIVE BATCHES --}}
    <article class="panel-card mb-6">
        <div class="panel-head">
            <div>
                <h2 class="m-0 font-bold text-lg">Rincian Batch Aktif</h2>
                <p class="text-xs text-slate-500">Daftar stok berdasarkan tanggal masuk dan harga beli.</p>
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
                            <td class="font-mono text-xs font-bold text-slate-400">#{{ $batch->id }}</td>
                            <td>{{ $batch->tanggal_masuk ? $batch->tanggal_masuk->format('d M Y') : '-' }}</td>
                            <td class="font-bold text-slate-700">Rp {{ number_format($batch->harga_beli, 0, ',', '.') }}</td>
                            <td class="text-slate-400">{{ $batch->detailBarangMasuk->jumlah ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-16 bg-slate-100 rounded-full overflow-hidden">
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
                            <td colspan="6" class="text-center text-slate-400 py-12">Tidak ada batch aktif untuk produk ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    {{-- STOCK LOGS --}}
    <article class="panel-card">
        <div class="panel-head">
            <div>
                <h2 class="m-0 font-bold text-lg">Log Pergerakan Terbaru</h2>
                <p class="text-xs text-slate-500">Mutasi masuk dan keluar dari semua transaksi.</p>
            </div>
            <span class="tag neutral">History</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Referens Document</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockLogs as $log)
                        <tr>
                            <td class="text-sm text-slate-600">{{ $log->tanggal ?? '-' }}</td>
                            <td>
                                <span class="status-pill {{ $log->tipe === 'masuk' ? 'success' : 'danger' }}">
                                    {{ ucfirst($log->tipe) }}
                                </span>
                            </td>
                            <td class="font-bold {{ $log->tipe === 'masuk' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $log->tipe === 'masuk' ? '+' : '-' }}{{ $log->jumlah }}
                            </td>
                            <td class="text-sm text-slate-500">{{ $log->keterangan }}</td>
                            <td>
                                @if($log->transaksi)
                                    <a href="{{ route('transaksi.show', $log->transaksi) }}" class="text-xs underline text-slate-400">Trx #{{ $log->transaksi->kode }}</a>
                                @elseif($log->barangMasuk)
                                    <a href="{{ route('barang-masuk.show', $log->barangMasuk) }}" class="text-xs underline text-slate-400">BM #{{ $log->barangMasuk->kode }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-400 py-12">Belum ada riwayat pergerakan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-50 text-right">
            <a href="{{ route('log-stok.index', ['produk_id' => $produk->id]) }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua Log Produk Ini &rarr;</a>
        </div>
    </article>
</section>
@endsection
