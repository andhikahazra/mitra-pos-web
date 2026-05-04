@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" id="section-laporan">
    <div class="pemilik-hero">
        <div class="hero-copy">
            <p class="hero-kicker">Financial Analysis & Performance</p>
            <h1>Laporan Keuangan</h1>
        </div>

        <div class="hero-filter-panel">
            <form action="{{ route('laporan.index') }}" method="GET" class="hero-custom-range !flex">
                <input type="date" name="start_date" class="field !h-10" value="{{ $startDate }}" aria-label="Tanggal mulai">
                <span class="hero-range-separator">s/d</span>
                <input type="date" name="end_date" class="field !h-10" value="{{ $endDate }}" aria-label="Tanggal akhir">
                <button type="submit" class="btn btn-primary !h-10 !px-3">Terapkan Filter</button>
            </form>

            <div class="hero-actions">
                <p class="hero-filter-label">
                    Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> - <strong>{{ date('d M Y', strtotime($endDate)) }}</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- Financial Summary KPI --}}
    <div class="kpi-strip">
        <article class="kpi-card">
            <p class="kpi-label">Total Omset</p>
            <h3 class="text-slate-900">Rp {{ number_format($totalOmset, 0, ',', '.') }}</h3>
            <p class="kpi-trend positive">Total Penjualan Kotor</p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Total Modal</p>
            <h3 class="text-slate-900">Rp {{ number_format($totalModal, 0, ',', '.') }}</h3>
            <p class="kpi-trend neutral">HPP (Harga Pokok Penjualan)</p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Laba Kotor</p>
            <h3 class="text-slate-900">Rp {{ number_format($labaKotor, 0, ',', '.') }}</h3>
            <p class="kpi-trend success">Keuntungan Penjualan</p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Total Transaksi</p>
            <h3 class="text-slate-900">{{ $totalTransaksi }}</h3>
            <p class="kpi-trend info">Volume Penjualan</p>
        </article>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        {{-- Daily Breakdown --}}
        <article class="panel-card">
            <div class="panel-head">
                <h2>Rincian Harian</h2>
                <span class="tag blue">Statistik Harian</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jml Transaksi</th>
                            <th>Omset (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-900">
                        @forelse($dailyStats as $stat)
                            <tr>
                                <td class="font-bold">{{ date('d M Y', strtotime($stat->date)) }}</td>
                                <td class="font-mono">{{ $stat->count }}</td>
                                <td class="font-mono">{{ number_format($stat->omset, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-6 text-slate-400">Tidak ada data untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        {{-- Top Selling Products --}}
        <article class="panel-card">
            <div class="panel-head">
                <h2>Produk Terlaris</h2>
                <span class="tag orange">Top 5 Berdasarkan Qty</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-900">
                        @forelse($topProducts as $top)
                            <tr>
                                <td class="font-medium">{{ $top->produk->nama ?? '-' }}</td>
                                <td class="text-center font-mono">{{ (int)$top->total_qty }}</td>
                                <td class="text-right font-bold font-mono">Rp {{ number_format($top->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-6 text-slate-400">Belum ada produk terjual.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
