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
    <div class="section-head">
        <div>
            <h1>Dashboard</h1>
        </div>
        <div class="flex items-center gap-0.5 p-0.5 rounded-lg bg-slate-100 dark:bg-zinc-900">
            <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeRange === 'today' ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}" data-range="today">Hari ini</button>
            <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeRange === '7d' ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}" data-range="7d">7 hari</button>
            <button type="button" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeRange === '1m' ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm' : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}" data-range="1m">30 hari</button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @php
            $periodLabel = match($activeRange) {
                'today' => 'Hari ini',
                '7d' => '7 hari terakhir',
                '1m' => '30 hari terakhir',
                default => 'Custom',
            };
            $criticalRop = $metrics['criticalRop'] ?? 0;
            $pendingIncoming = $metrics['pendingIncoming'] ?? 0;
        @endphp

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Omzet</p>
                <span class="text-xs text-slate-400 dark:text-zinc-500">{{ $periodLabel }}</span>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-zinc-100">Rp {{ number_format($metrics['omzetToday'] ?? 0, 0, ',', '.') }}</h3>
        </article>

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Transaksi</p>
                <span class="text-xs text-slate-400 dark:text-zinc-500">{{ $periodLabel }}</span>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 dark:text-zinc-100">{{ $metrics['trxToday'] ?? 0 }}</h3>
        </article>

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Stok Kritis</p>
                <span class="inline-flex items-center gap-1.5 text-xs {{ $criticalRop > 0 ? 'text-amber-600 dark:text-amber-400 font-medium' : 'text-slate-400 dark:text-zinc-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $criticalRop > 0 ? 'bg-amber-500' : 'bg-slate-300 dark:bg-zinc-600' }}"></span>
                    {{ $criticalRop > 0 ? 'Perlu restok' : 'Normal' }}
                </span>
            </div>
            <h3 class="text-xl font-semibold {{ $criticalRop > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-900 dark:text-zinc-100' }}">{{ $criticalRop }}</h3>
        </article>

        <article class="kpi-card">
            <div class="flex items-center justify-between mb-2">
                <p class="kpi-label">Pending ACC</p>
                <span class="inline-flex items-center gap-1.5 text-xs {{ $pendingIncoming > 0 ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-slate-400 dark:text-zinc-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $pendingIncoming > 0 ? 'bg-blue-500' : 'bg-slate-300 dark:bg-zinc-600' }}"></span>
                    {{ $pendingIncoming > 0 ? 'Perlu ACC' : 'Selesai' }}
                </span>
            </div>
            <h3 class="text-xl font-semibold {{ $pendingIncoming > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-900 dark:text-zinc-100' }}">{{ $pendingIncoming }}</h3>
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
                @if($alerts)
                    <span class="text-xs text-slate-400 dark:text-zinc-500">{{ count($alerts) }} notifikasi</span>
                @endif
            </div>
            <div class="p-4 space-y-2">
                @forelse($alerts as $alert)
                    @php
                        $level = $alert['level'] ?? 'neutral';
                        $iconMap = [
                            'danger' => '<svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
                            'warning' => '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                            'info' => '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                            'neutral' => '<svg class="w-5 h-5 text-slate-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
                        ];
                        $icon = $iconMap[$level] ?? $iconMap['neutral'];
                        $labelClass = match($level) {
                            'danger' => 'text-rose-700 dark:text-rose-400',
                            'warning' => 'text-amber-700 dark:text-amber-400',
                            'info' => 'text-blue-700 dark:text-blue-400',
                            default => 'text-slate-600 dark:text-zinc-300',
                        };
                    @endphp
                    <div class="alert-row {{ $level }} flex items-start gap-3 transition-all duration-150 hover:shadow-sm">
                        <div class="flex-shrink-0 mt-0.5">
                            {!! $icon !!}
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="m-0 text-xs font-semibold {{ $labelClass }}">{{ $alert['label'] }}</p>
                            @if($alert['text'])
                                <p class="m-0 text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">{{ $alert['text'] }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="m-0 text-sm font-medium text-slate-600 dark:text-zinc-400">Tidak ada alert</p>
                        <p class="m-0 text-xs text-slate-400 dark:text-zinc-500 mt-1">Semua berjalan lancar</p>
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
