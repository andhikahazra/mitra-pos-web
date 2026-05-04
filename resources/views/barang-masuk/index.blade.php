@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-incoming-goods">
    <div class="section-head mb-6">
        <div>
            <h1>Approval Barang Masuk</h1>
        </div>
    </div>

    <article class="panel-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Dokumen</th>
                        <th>Supplier</th>
                        <th>Ringkasan Barang</th>
                        <th>Total Nilai</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingGoods as $item)
                        @php
                            $totalItems = $item->detail->count();
                            $totalValue = $item->detail->sum(fn($d) => $d->jumlah * $d->harga);
                            $firstItem = $item->detail->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800">#{{ $item->kode }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $item->tanggal_pesan ? $item->tanggal_pesan->format('d M Y, H:i') : '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <span class="font-medium text-slate-700">{{ $item->supplier->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700">{{ $totalItems }} Produk</span>
                                    <span class="text-[11px] text-slate-400 truncate max-w-[200px]">
                                        {{ $firstItem?->produk?->nama ?? '-' }} {{ $totalItems > 1 ? '...' : '' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="font-extrabold text-slate-800 text-sm">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if (strtolower($item->status) === 'disetujui')
                                    <span class="status-pill success">Disetujui</span>
                                @elseif (strtolower($item->status) === 'ditolak')
                                    <span class="status-pill danger">Ditolak</span>
                                @else
                                    <span class="status-pill info">Menunggu</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('barang-masuk.show', $item->id) }}" class="btn btn-ghost btn-sm">
                                    Review
                                    <svg viewBox="0 0 24 24" class="h-3 w-3 inline ml-1" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 7l5 5-5 5M6 12h12"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-500 py-12">Belum ada data barang masuk yang perlu ditinjau.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>
@endsection
