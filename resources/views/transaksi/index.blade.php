@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-transaksi">
    <div class="section-head">
        <div>
            <h1>Riwayat Transaksi</h1>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="kpi-strip mb-4">
        <article class="kpi-card border-t-2 border-t-indigo-500">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Total Omzet</p>
                <svg class="w-4 h-4 text-indigo-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">{{ $summary['total_transaksi'] }} transaksi &middot; {{ $summary['total_item'] }} item</p>
        </article>

        <article class="kpi-card border-t-2 border-t-emerald-500">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Pendapatan</p>
                <svg class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Total Lunas</p>
        </article>

        <article class="kpi-card border-t-2 border-t-amber-500">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Piutang</p>
                <svg class="w-4 h-4 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Belum Terbayar</p>
        </article>

        <article class="kpi-card border-t-2 border-t-slate-400">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Rincian Metode</p>
                <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </div>
            <div class="mt-2 grid grid-cols-3 gap-2 text-xs">
                <div>
                    <p class="text-slate-400 dark:text-zinc-500">Tunai</p>
                    <p class="font-semibold text-slate-800 dark:text-zinc-200">Rp {{ number_format($summary['pembayaran']['Tunai'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-slate-400 dark:text-zinc-500">QRIS</p>
                    <p class="font-semibold text-slate-800 dark:text-zinc-200">Rp {{ number_format($summary['pembayaran']['QRIS'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-slate-400 dark:text-zinc-500">Transfer</p>
                    <p class="font-semibold text-slate-800 dark:text-zinc-200">Rp {{ number_format($summary['pembayaran']['Transfer'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </article>
    </div>

    <form class="toolbar" method="GET" action="{{ route('transaksi.index') }}">
        <div class="flex items-center gap-2">
            <input class="field" name="start_date" type="date" value="{{ $startDate }}" placeholder="Mulai">
            <span class="text-slate-400 dark:text-zinc-500">s/d</span>
            <input class="field" name="end_date" type="date" value="{{ $endDate }}" placeholder="Akhir">
            <select name="method" class="field">
                <option value="">Semua Metode</option>
                <option value="Tunai" {{ request('method') === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="QRIS" {{ request('method') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                <option value="Transfer" {{ request('method') === 'Transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="Piutang" {{ request('method') === 'Piutang' ? 'selected' : '' }}>Piutang</option>
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
            @if(request('start_date') || request('end_date') || request('method'))
                <a class="btn btn-ghost" href="{{ route('transaksi.index') }}">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Waktu Transaksi</th>
                    <th>Staff</th>
                    <th>Metode</th>
                    <th>Item</th>
                    <th>Total Nilai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $trx)
                    <tr>
                        <td class="font-bold text-slate-900 dark:text-slate-100">{{ $trx->kode }}</td>
                        <td>
                            <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $trx->tanggal ? $trx->tanggal->format('d M Y') : '-' }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $trx->tanggal ? $trx->tanggal->format('H:i') : '' }} WIB</div>
                        </td>
                        <td>{{ $trx->user->nama ?? '-' }}</td>
                        <td>
                            <span class="tag {{ $trx->metode_pembayaran === 'Tunai' ? 'green' : ($trx->metode_pembayaran === 'QRIS' ? 'orange' : 'blue') }}">
                                {{ $trx->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="text-slate-900 dark:text-slate-100">{{ $trx->detail_transaksi->sum('jumlah') }} item</td>
                        <td>
                            <div class="font-bold text-slate-900 dark:text-slate-100">
                                Rp {{ number_format((float) $trx->total_harga, 0, ',', '.') }}
                            </div>
                            @if($trx->biaya_admin > 0)
                                <div class="text-[10px] text-slate-400 dark:text-zinc-500">+ Admin Rp {{ number_format($trx->biaya_admin, 0, ',', '.') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($trx->status === 'Selesai')
                                <span class="badge badge-success">Selesai</span>
                            @else
                                <span class="badge badge-warning">Tertunda</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a class="link-btn more" href="{{ route('transaksi.show', $trx) }}">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                </div>
                                <p class="empty-state-title">Belum ada transaksi</p>
                                <p class="empty-state-desc">Belum ada transaksi pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $transaksi->links('pagination::tailwind') }}
    </div>
</section>
@endsection
