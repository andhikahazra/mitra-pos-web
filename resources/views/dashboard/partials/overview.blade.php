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
    {{-- Hero Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 class="text-xl font-semibold text-slate-900 dark:text-zinc-100">Dashboard</h1>

        <div class="flex items-center gap-2">
            <div class="flex items-center gap-0.5 p-0.5 rounded-lg bg-slate-100 dark:bg-zinc-900">
                <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeRange === 'today' ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}" data-range="today">Hari ini</button>
                <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeRange === '7d' ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}" data-range="7d">7 hari</button>
                <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeRange === '1m' ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}" data-range="1m">30 hari</button>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Omzet</p>
                <span class="text-xs text-slate-400 dark:text-zinc-500">
                    @if($activeRange === 'today') Hari ini
                    @elseif($activeRange === '7d') 7 hari
                    @elseif($activeRange === '1m') 30 hari
                    @else Custom
                    @endif
                </span>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-zinc-100">Rp {{ number_format($metrics['omzetToday'] ?? 0, 0, ',', '.') }}</h3>
        </article>

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Transaksi</p>
                <span class="text-xs text-slate-400 dark:text-zinc-500">
                    @if($activeRange === 'today') Hari ini
                    @elseif($activeRange === '7d') 7 hari
                    @elseif($activeRange === '1m') 30 hari
                    @else Custom
                    @endif
                </span>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-zinc-100">{{ $metrics['trxToday'] ?? 0 }}</h3>
        </article>

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Stok Kritis</p>
                @if(($metrics['criticalRop'] ?? 0) > 0)
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                @endif
            </div>
            <h3 class="text-xl font-semibold {{ ($metrics['criticalRop'] ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-zinc-100' }}">{{ $metrics['criticalRop'] ?? 0 }}</h3>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Perlu restok</p>
        </article>

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Pending ACC</p>
                @if(($metrics['pendingIncoming'] ?? 0) > 0)
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                @endif
            </div>
            <h3 class="text-xl font-semibold {{ ($metrics['pendingIncoming'] ?? 0) > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-zinc-100' }}">{{ $metrics['pendingIncoming'] ?? 0 }}</h3>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Barang masuk</p>
        </article>
    </div>

    {{-- Charts --}}
    <div class="grid gap-4 lg:grid-cols-5 mb-6">
        <article class="panel-card lg:col-span-3">
            <div class="panel-head">
                <h2 class="text-sm font-medium text-slate-700 dark:text-zinc-200">Penjualan</h2>
            </div>
            <div class="h-[200px] mt-2"><canvas id="salesLineChart"></canvas></div>
        </article>

        <article class="panel-card lg:col-span-2">
            <div class="panel-head">
                <h2 class="text-sm font-medium text-slate-700 dark:text-zinc-200">Stok Kategori</h2>
            </div>
            <div class="h-[200px] mt-2"><canvas id="stockBarChart"></canvas></div>
        </article>
    </div>

    {{-- Latest Transactions + Alerts --}}
    <div class="grid gap-4 lg:grid-cols-5">
        <article class="panel-card lg:col-span-3">
            <div class="panel-head">
                <h2 class="text-sm font-medium text-slate-700 dark:text-zinc-200">Transaksi Terbaru</h2>
                <a href="{{ route('transaksi.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
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
                                <td class="font-medium text-slate-900 dark:text-zinc-100">{{ $trx['invoice'] }}</td>
                                <td>{{ $trx['date'] }}</td>
                                <td>{{ $trx['cashier'] }}</td>
                                <td>{{ $trx['items'] }}</td>
                                <td class="font-medium">Rp {{ number_format($trx['total'], 0, ',', '.') }}</td>
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
                                <td colspan="6" class="text-center text-sm text-slate-500 dark:text-zinc-400 py-8">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel-card lg:col-span-2">
            <div class="panel-head">
                <h2 class="text-sm font-medium text-slate-700 dark:text-zinc-200">Alert</h2>
            </div>
            <div class="grid gap-2">
                @forelse($alerts as $alert)
                    <article class="alert-row {{ $alert['level'] ?? 'neutral' }}">
                        <p class="alert-label">{{ $alert['label'] }}</p>
                        <p class="alert-text">{{ $alert['text'] }}</p>
                    </article>
                @empty
                    <div class="text-center py-8 text-sm text-slate-500 dark:text-zinc-400">
                        Tidak ada alert
                    </div>
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
