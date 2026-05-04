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
            <h3 style="color: #1e1b4b;">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</h3>
            <p class="kpi-trend info">{{ $summary['total_transaksi'] }} transaksi | {{ $summary['total_item'] }} item</p>
        </article>
        <article class="kpi-card" style="border-left: 4px solid #16a34a;">
            <p class="kpi-label">Total Pendapatan (Lunas)</p>
            <h3 style="color: #064e3b;">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
            <p class="kpi-trend positive">Uang riil yang sudah diterima</p>
        </article>
        <article class="kpi-card" style="border-left: 4px solid #ea580c;">
            <p class="kpi-label">Piutang (Belum Terbayar)</p>
            <h3 style="color: #7c2d12;">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</h3>
            <p class="kpi-trend warning">Uang yang masih di pelanggan</p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Rincian Per Metode</p>
            <div class="mt-2 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tunai:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($summary['pembayaran']['Tunai'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">QRIS:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($summary['pembayaran']['QRIS'], 0, ',', '.') }}</span>
                </div>
                @if($summary['total_admin_qris'] > 0)
                    <div class="flex justify-between">
                        <span class="text-[10px] text-slate-400">Total Admin QRIS:</span>
                        <span class="text-[10px] font-medium text-slate-400">Rp {{ number_format($summary['total_admin_qris'], 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-slate-500">Transfer:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($summary['pembayaran']['Transfer'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </article>
    </div>

    <article class="panel-card">
        <form class="toolbar" method="GET" action="{{ route('transaksi.index') }}">
            <div class="flex items-center gap-2">
                <input class="field" name="start_date" type="date" value="{{ $startDate }}" placeholder="Mulai">
                <span class="text-slate-400">s/d</span>
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
                            <td class="font-bold text-slate-900">{{ $trx->kode }}</td>
                            <td>
                                <div class="text-sm font-bold text-slate-900">{{ $trx->tanggal ? $trx->tanggal->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $trx->tanggal ? $trx->tanggal->format('H:i') : '' }} WIB</div>
                            </td>
                            <td>{{ $trx->user->nama ?? '-' }}</td>
                            <td>
                                <span class="tag {{ $trx->metode_pembayaran === 'Tunai' ? 'green' : ($trx->metode_pembayaran === 'QRIS' ? 'orange' : 'blue') }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="text-slate-900">{{ $trx->detail_transaksi->sum('jumlah') }} item</td>
                            <td>
                                <div class="font-bold text-slate-900">
                                    Rp {{ number_format((float) $trx->total_harga, 0, ',', '.') }}
                                </div>
                                @if($trx->biaya_admin > 0)
                                    <div class="text-[10px] text-slate-400">+ Admin Rp {{ number_format($trx->biaya_admin, 0, ',', '.') }}</div>
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
                            <td colspan="9" class="text-center text-slate-500 py-10">Belum ada transaksi pada periode ini.</td>
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
