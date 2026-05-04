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
        <article class="kpi-card !bg-white">
            <p class="kpi-label">Total Transaksi</p>
            <h3>{{ $summary['total_transaksi'] }}</h3>
            <p class="kpi-trend info">{{ $summary['total_item'] }} items terjual</p>
        </article>
        <article class="kpi-card !bg-white">
            <p class="kpi-label">Total Nilai (Barang)</p>
            <h3>Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</h3>
            <p class="kpi-trend positive">Belum termasuk biaya admin</p>
        </article>
        <article class="kpi-card !bg-white">
            <p class="kpi-label">Biaya Admin & QRIS</p>
            <div class="mt-2 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Admin:</span>
                    <span class="font-bold">Rp {{ number_format($summary['total_admin'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Omzet QRIS:</span>
                    <span class="font-bold">Rp {{ number_format($summary['pembayaran']['QRIS'], 0, ',', '.') }}</span>
                </div>
            </div>
        </article>
        <article class="kpi-card !bg-white">
            <p class="kpi-label">Metode Lainnya</p>
            <div class="mt-2 text-xs space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tunai:</span>
                    <span class="font-bold">Rp {{ number_format($summary['pembayaran']['Tunai'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Transfer/Piutang:</span>
                    <span class="font-bold">Rp {{ number_format($summary['pembayaran']['Transfer'] + $summary['pembayaran']['Piutang'], 0, ',', '.') }}</span>
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
                <button class="btn btn-primary" type="submit">Filter</button>
                @if(request('start_date') || request('end_date'))
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
                        <th>Biaya Admin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $trx)
                        <tr>
                            <td class="font-bold text-indigo-600">{{ $trx->kode }}</td>
                            <td>
                                <div class="text-sm font-bold">{{ $trx->tanggal ? $trx->tanggal->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $trx->tanggal ? $trx->tanggal->format('H:i') : '' }} WIB</div>
                            </td>
                            <td>{{ $trx->user->nama ?? '-' }}</td>
                            <td>
                                <span class="tag {{ $trx->metode_pembayaran === 'Tunai' ? 'green' : ($trx->metode_pembayaran === 'QRIS' ? 'orange' : 'blue') }}">
                                    {{ $trx->metode_pembayaran }}
                                </span>
                            </td>
                            <td>{{ $trx->detail_transaksi->sum('jumlah') }} item</td>
                            <td>
                                <div class="font-bold text-slate-700">Rp {{ number_format((float) $trx->total_harga, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                @if($trx->biaya_admin > 0)
                                    <div class="font-medium text-orange-600">Rp {{ number_format($trx->biaya_admin, 0, ',', '.') }}</div>
                                @else
                                    <span class="text-slate-300">-</span>
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
