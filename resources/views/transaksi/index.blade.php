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
        <article class="kpi-card" style="border-left: 4px solid #4f46e5;">
            <p class="kpi-label">Total Omzet (Kotor)</p>
            <h3 class="text-indigo-900 dark:text-indigo-300">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</h3>
            <p class="kpi-trend info">{{ $summary['total_transaksi'] }} transaksi | {{ $summary['total_item'] }} item</p>
        </article>
        <article class="kpi-card" style="border-left: 4px solid #16a34a;">
            <p class="kpi-label">Total Pendapatan (Lunas)</p>
            <h3 class="text-emerald-900 dark:text-emerald-300">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
        </article>
        <article class="kpi-card" style="border-left: 4px solid #ea580c;">
            <p class="kpi-label">Piutang (Belum Terbayar)</p>
            <h3 class="text-orange-900 dark:text-orange-300">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</h3>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Rincian Per Metode</p>
            <div class="mt-2 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-zinc-400">Tunai:</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($summary['pembayaran']['Tunai'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-zinc-400">QRIS:</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($summary['pembayaran']['QRIS'], 0, ',', '.') }}</span>
                </div>
                @if($summary['total_admin_qris'] > 0)
                    <div class="flex justify-between">
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">Total Admin QRIS:</span>
                        <span class="text-[10px] font-medium text-slate-400 dark:text-zinc-500">Rp {{ number_format($summary['total_admin_qris'], 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-slate-500 dark:text-zinc-400">Transfer:</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($summary['pembayaran']['Transfer'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </article>
    </div>

    <article class="panel-card">
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
                            <td colspan="9" class="p-0">
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
            {{ $transaksi->links() }}
        </div>
    </article>
</section>
@endsection
