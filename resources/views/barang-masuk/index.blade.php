@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-incoming-goods">
    <div class="section-head mb-6">
        <div>
            <h1>Barang Masuk</h1>
        </div>
    </div>

    {{-- Ringkasan Modal --}}
    <div class="kpi-strip mb-6 !grid-cols-2">
        <div class="kpi-card">
            <span class="kpi-label">Total Uang Modal Keseluruhan</span>
            <div class="flex items-baseline gap-1">
                <h3>Rp {{ number_format($totalModalOverall, 0, ',', '.') }}</h3>
            </div>
            <p class="kpi-trend info mt-1 text-slate-800 dark:text-slate-200">Total akumulasi modal disetujui</p>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Modal Bulan Ini ({{ \Carbon\Carbon::create(null, $selectedMonth)->translatedFormat('F') }} {{ $selectedYear }})</span>
            <div class="flex items-baseline gap-1">
                <h3>Rp {{ number_format($totalModalMonth, 0, ',', '.') }}</h3>
            </div>
            <p class="kpi-trend warning mt-1 text-slate-800 dark:text-slate-200">Pengeluaran modal periode dipilih</p>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <form action="{{ route('barang-masuk.index') }}" method="GET" class="flex flex-wrap items-end gap-4 mb-6">
        <div class="flex-1 min-w-[200px]">
            <label class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-2 block">Status Approval</label>
            <select name="status" class="field" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="w-[180px]">
            <label class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-2 block">Bulan</label>
            <select name="month" class="field" onchange="this.form.submit()">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="w-[120px]">
            <label class="text-[11px] font-bold text-slate-400 dark:text-zinc-500 uppercase tracking-wider mb-2 block">Tahun</label>
            <select name="year" class="field" onchange="this.form.submit()">
                @php $currentYear = now()->year; @endphp
                @for($y = $currentYear; $y >= $currentYear - 3; $y--)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="flex flex-col">
            <label class="text-[11px] mb-2 block opacity-0">&nbsp;</label>
            <a href="{{ route('barang-masuk.index') }}" class="btn btn-ghost !h-11 w-11 !p-0 flex items-center justify-center" title="Reset Filter">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            </a>
        </div>
    </form>

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
                                    <span class="font-semibold text-slate-900 dark:text-zinc-100">#{{ $item->kode }}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">{{ $item->tanggal_pesan ? $item->tanggal_pesan->format('d M Y, H:i') : '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-400 dark:text-zinc-500">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <span class="font-semibold text-slate-700 dark:text-zinc-300">{{ $item->supplier->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-700 dark:text-zinc-300">{{ $totalItems }} Produk</span>
                                    <span class="text-[10px] text-slate-400 dark:text-zinc-500 truncate max-w-[200px]">
                                        {{ $firstItem?->produk?->nama ?? '-' }} {{ $totalItems > 1 ? '...' : '' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="font-bold text-slate-900 dark:text-zinc-100 text-sm">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
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
                            <a href="{{ route('barang-masuk.show', $item->id) }}" class="btn btn-ghost btn-sm hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-900/20 dark:hover:text-blue-300">
                                Review
                                <svg viewBox="0 0 24 24" class="h-3 w-3 inline ml-1" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 7l5 5-5 5M6 12h12"/></svg>
                            </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 13.875V7.5" /></svg>
                                    </div>
                                    <p class="empty-state-title">Belum ada data</p>
                                    <p class="empty-state-desc">Belum ada data barang masuk yang perlu ditinjau.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <div class="mt-4">
        {{ $incomingGoods->links() }}
    </div>
</section>
@endsection
