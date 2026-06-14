@php
    $payload = $dashboardPayload ?? [];
    $metrics = $payload['metrics'] ?? [];
    $latestTransactions = $payload['latestTransactions'] ?? [];
    $alerts = $payload['alerts'] ?? [];
    $charts = $payload['charts'] ?? [];

    $activeRange = request()->query('range', 'today');
    $startDateVal = request()->query('start_date', '');
    $endDateVal = request()->query('end_date', '');
@endphp

<section class="feature-section active" id="section-dashboard">
    <div class="pemilik-hero">
        <div class="hero-copy">
            <p class="hero-kicker">Daily Operation Snapshot</p>
            <h1>Dashboard pemilik MitraPOS</h1>
            <p>Pantau omzet, stok kritis, dan kualitas operasional toko dalam satu command center yang lebih fokus.</p>
        </div>

        <div class="hero-filter-panel">
            <div class="hero-range-tabs" role="tablist" aria-label="Filter waktu dashboard">
                <button type="button" class="hero-range-btn {{ $activeRange === 'today' ? 'active' : '' }}" data-range="today">Hari Ini</button>
                <button type="button" class="hero-range-btn {{ $activeRange === '7d' ? 'active' : '' }}" data-range="7d">7 Hari</button>
                <button type="button" class="hero-range-btn {{ $activeRange === '1m' ? 'active' : '' }}" data-range="1m">1 Bulan</button>
                <button type="button" class="hero-range-btn {{ $activeRange === 'custom' ? 'active' : '' }}" data-range="custom">Custom</button>
            </div>

            <div class="hero-custom-range {{ $activeRange === 'custom' ? '' : 'hidden' }}" id="heroCustomRange">
                <input type="date" class="field !h-10" id="heroStartDate" aria-label="Tanggal mulai" value="{{ $startDateVal }}">
                <span class="hero-range-separator">s/d</span>
                <input type="date" class="field !h-10" id="heroEndDate" aria-label="Tanggal akhir" value="{{ $endDateVal }}">
                <button type="button" class="btn btn-ghost !h-10 !px-3" id="heroApplyRange">Terapkan</button>
            </div>

            <div class="hero-actions">
                @if($activeRange === 'custom' && $startDateVal && $endDateVal)
                    <p class="hero-filter-label" id="heroFilterLabel">Menampilkan data custom {{ $startDateVal }} s/d {{ $endDateVal }}</p>
                @else
                    <p class="hero-filter-label" id="heroFilterLabel">
                        {{ $activeRange === '7d' ? 'Menampilkan data 7 hari terakhir' : ($activeRange === '1m' ? 'Menampilkan data 1 bulan terakhir' : 'Menampilkan data hari ini') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-strip">
        <article class="kpi-card">
            <p class="kpi-label">
                @if($activeRange === 'today')
                    Omzet Hari Ini
                @elseif($activeRange === '7d')
                    Omzet 7 Hari Terakhir
                @elseif($activeRange === '1m')
                    Omzet 30 Hari Terakhir
                @else
                    Omzet Kustom
                @endif
            </p>
            <h3>Rp {{ number_format($metrics['omzetToday'] ?? 0, 0, ',', '.') }}</h3>
            <p class="kpi-trend positive" id="kpiOmzetTrend">
                @if($activeRange === 'today')
                    Data hari ini
                @elseif($activeRange === '7d')
                    Data 7 hari terakhir
                @elseif($activeRange === '1m')
                    Data 30 hari terakhir
                @else
                    Periode: {{ $startDateVal }} s/d {{ $endDateVal }}
                @endif
            </p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">
                @if($activeRange === 'today')
                    Transaksi Hari Ini
                @elseif($activeRange === '7d')
                    Transaksi 7 Hari Terakhir
                @elseif($activeRange === '1m')
                    Transaksi 30 Hari Terakhir
                @else
                    Transaksi Kustom
                @endif
            </p>
            <h3>{{ $metrics['trxToday'] ?? 0 }}</h3>
            <p class="kpi-trend info">
                @if($activeRange === 'today')
                    Total transaksi hari ini
                @elseif($activeRange === '7d')
                    Total transaksi 7 hari terakhir
                @elseif($activeRange === '1m')
                    Total transaksi 30 hari terakhir
                @else
                    Total transaksi periode kustom
                @endif
            </p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Produk ROP Kritis</p>
            <h3>{{ $metrics['criticalRop'] ?? 0 }}</h3>
            <p class="kpi-trend warning">Produk perlu perhatian restock</p>
        </article>
        <article class="kpi-card">
            <p class="kpi-label">Approval Barang Masuk</p>
            <h3>{{ $metrics['pendingIncoming'] ?? 0 }}</h3>
            <p class="kpi-trend danger">Menunggu ACC pemilik</p>
        </article>
    </div>

    {{-- Charts --}}
    <div class="grid gap-3 xl:grid-cols-5">
        <article class="panel-card xl:col-span-3">
            <div class="panel-head">
                <h2>
                    @if($activeRange === '1m')
                        Tren Penjualan 30 Hari Terakhir
                    @elseif($activeRange === 'custom')
                        Tren Penjualan (Kustom)
                    @else
                        Tren Penjualan 7 Hari Terakhir
                    @endif
                </h2>
                <span class="tag blue">Omzet Harian</span>
            </div>
            <div class="h-[250px]"><canvas id="salesLineChart"></canvas></div>
        </article>

        <article class="panel-card xl:col-span-2">
            <div class="panel-head">
                <h2>Distribusi Stok per Kategori</h2>
                <span class="tag orange">Unit</span>
            </div>
            <div class="h-[250px]"><canvas id="stockBarChart"></canvas></div>
        </article>
    </div>

    {{-- Latest Transactions + Alerts --}}
    <div class="grid gap-3 xl:grid-cols-5">
        <article class="panel-card xl:col-span-3">
            <div class="panel-head">
                <h2>Transaksi Terbaru</h2>
                <a href="{{ route('transaksi.index') }}" class="btn btn-ghost">Lihat Semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Staff</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTransactions as $trx)
                            <tr>
                                <td>{{ $trx['invoice'] }}</td>
                                <td>{{ $trx['date'] }}</td>
                                <td>{{ $trx['cashier'] }}</td>
                                <td>{{ $trx['items'] }}</td>
                                <td>Rp {{ number_format($trx['total'], 0, ',', '.') }}</td>
                                <td>
                                    @if($trx['status'] === 'Selesai')
                                        <span class="status-pill success">Selesai</span>
                                    @else
                                        <span class="status-pill warning">Tertunda</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-sm text-slate-500 py-6">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel-card xl:col-span-2">
            <div class="panel-head">
                <h2>Alert Operasional</h2>
                <span class="tag neutral">Prioritas</span>
            </div>
            <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                @forelse($alerts as $alert)
                    <article class="alert-row {{ $alert['level'] ?? 'neutral' }}">
                        <p class="alert-label">{{ $alert['label'] }}</p>
                        <p class="alert-text">{{ $alert['text'] }}</p>
                    </article>
                @empty
                    <article class="alert-row neutral">
                        <p class="alert-label">Info</p>
                        <p class="alert-text">Belum ada alert operasional.</p>
                    </article>
                @endforelse
            </div>
        </article>
    </div>
</section>

{{-- Inject chart data untuk JS --}}
<script>
    window.__DASHBOARD_DATA__ = {
        range: @json($activeRange),
        charts: @json($charts),
        metrics: @json($metrics),
        latestTransactions: @json($latestTransactions),
        alerts: @json($alerts),
    };
</script>
