@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" id="section-laporan">
    {{-- Header Section --}}
    <div class="section-head">
        <div>
            <h1>Laporan Keuangan</h1>
            <p>Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> — <strong>{{ date('d M Y', strtotime($endDate)) }}</strong></p>
        </div>
        <div class="header-actions">
            <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="start_date" class="field !h-9 !w-auto" value="{{ $startDate }}" aria-label="Tanggal mulai">
                <span class="text-slate-400 text-sm">s/d</span>
                <input type="date" name="end_date" class="field !h-9 !w-auto" value="{{ $endDate }}" aria-label="Tanggal akhir">
                <button type="submit" class="btn btn-primary !h-9">Filter</button>
            </form>
        </div>
    </div>

    {{-- Financial Summary KPI --}}
    <div class="kpi-strip mb-6">
        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Total Omset</p>
                <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($totalOmset, 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Total Penjualan Kotor</p>
        </article>
        
        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Total Modal</p>
                <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($totalModal, 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Harga Pokok Penjualan</p>
        </article>

        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Laba Kotor</p>
                <svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-emerald-800 dark:text-emerald-300">Rp {{ number_format($labaKotor, 0, ',', '.') }}</h3>
            <p class="kpi-trend text-emerald-600/80 text-xs mt-1">Keuntungan Penjualan</p>
        </article>

        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Total Transaksi</p>
                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">{{ $totalTransaksi }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Volume Penjualan</p>
        </article>
    </div>

    <div class="flex flex-col gap-6">
        {{-- Top Selling Products --}}
        <article class="panel-card overflow-hidden !p-0 flex flex-col">
            <div class="panel-head px-5 py-4 m-0 border-b border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/50">
                <h2 class="text-base font-semibold text-slate-800 dark:text-zinc-100 m-0">Produk Terlaris (Top 5)</h2>
            </div>
            <div class="table-wrap !border-none !rounded-none flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-zinc-800">
                            <th class="py-3 px-5 text-left font-semibold text-slate-500 uppercase tracking-wider text-xs">Produk</th>
                            <th class="py-3 px-5 text-center font-semibold text-slate-500 uppercase tracking-wider text-xs">Terjual</th>
                            <th class="py-3 px-5 text-right font-semibold text-slate-500 uppercase tracking-wider text-xs">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        @forelse($topProducts as $index => $top)
                            <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-3 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-semibold text-slate-400 w-4">{{ $index + 1 }}.</span>
                                        <span class="font-semibold text-slate-800 dark:text-zinc-200">{{ $top->produk->nama ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-center font-mono text-slate-600 dark:text-zinc-400">{{ (int)$top->total_qty }}</td>
                                <td class="py-3 px-5 text-right font-mono font-semibold text-slate-800 dark:text-zinc-200">Rp {{ number_format($top->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-slate-400 text-sm">Belum ada produk yang terjual.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        {{-- Daily Breakdown --}}
        <article class="panel-card overflow-hidden !p-0 flex flex-col">
            <div class="panel-head px-5 py-4 m-0 border-b border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/50">
                <h2 class="text-base font-semibold text-slate-800 dark:text-zinc-100 m-0">Rincian Harian</h2>
            </div>
            <div class="table-wrap !border-none !rounded-none flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-zinc-800">
                            <th class="py-3 px-5 text-left font-semibold text-slate-500 uppercase tracking-wider text-xs">Tanggal</th>
                            <th class="py-3 px-5 text-center font-semibold text-slate-500 uppercase tracking-wider text-xs">Transaksi</th>
                            <th class="py-3 px-5 text-right font-semibold text-slate-500 uppercase tracking-wider text-xs">Omset (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                        @forelse($dailyStats as $stat)
                            <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="py-3 px-5 whitespace-nowrap">
                                    <span class="font-semibold text-slate-800 dark:text-zinc-200">{{ date('d M Y', strtotime($stat->date)) }}</span>
                                    <span class="ml-2 text-xs text-slate-400">{{ date('D', strtotime($stat->date)) }}</span>
                                </td>
                                <td class="py-3 px-5 text-center font-mono text-slate-600 dark:text-zinc-400">{{ $stat->count }}</td>
                                <td class="py-3 px-5 text-right font-mono font-semibold text-slate-800 dark:text-zinc-200">{{ number_format($stat->omset, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-slate-400 text-sm">Tidak ada data rincian harian untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
@endsection
