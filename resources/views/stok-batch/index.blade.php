@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-stok-monitoring">
    <div class="section-head">
        <div>
            <h1>Monitoring Stok & Inventori</h1>
        </div>
    </div>

    {{-- Summary Cards Assets --}}
    <div class="kpi-strip mb-4">
        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Total Modal Aset</p>
                <svg class="w-4 h-4 text-indigo-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Uang yang mengendap di stok</p>
        </article>
        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Total Nilai Jual</p>
                <svg class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($summary['total_aset'], 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Potensi jika semua terjual</p>
        </article>
        <article class="kpi-card">
            <div class="flex justify-between items-start">
                <p class="kpi-label uppercase tracking-wider text-[11px]">Potensi Margin</p>
                <svg class="w-4 h-4 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-zinc-100">Rp {{ number_format($summary['total_aset'] - $summary['total_modal'], 0, ',', '.') }}</h3>
            <p class="kpi-trend text-slate-500 text-xs mt-1">Selisih nilai jual vs modal</p>
        </article>
    </div>

    <form class="toolbar !flex-row !flex-wrap gap-3" id="stokMonitoringForm" method="GET" action="{{ route('stok-batch.index') }}">
        <div class="relative flex-1 min-w-[300px]">
            <input class="field w-full" name="search" id="stokMonitoringSearch" type="text" placeholder="Cari nama produk atau SKU..." value="{{ $search }}" autocomplete="off">
        </div>

        @if($search)
            <a href="{{ route('stok-batch.index') }}" class="btn btn-ghost">Reset Pencarian</a>
        @endif
    </form>

    <div id="stokBatchTableContainer">
        @include('stok-batch._table')
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('stokMonitoringForm');
        const searchInput = document.getElementById('stokMonitoringSearch');
        const container = document.getElementById('stokBatchTableContainer');

        let debounceTimer;

        const performFilter = () => {
            const formData = new URLSearchParams(new FormData(form)).toString();
            const targetUrl = `${window.location.pathname}?${formData}`;

            window.history.pushState(null, '', targetUrl);

            fetch(targetUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => console.error('Error fetching inventory data:', error));
        };

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performFilter, 300);
        });

        // Handle pagination clicks via AJAX
        container.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.pagination a');
            if (pageLink) {
                e.preventDefault();
                const url = pageLink.getAttribute('href');
                if (url) {
                    const targetUrl = new URL(url);
                    const formData = new URLSearchParams(new FormData(form));
                    for (const [key, value] of formData.entries()) {
                        if (value && !targetUrl.searchParams.has(key)) {
                            targetUrl.searchParams.set(key, value);
                        }
                    }
                    
                    const pathWithSearch = targetUrl.pathname + targetUrl.search;
                    window.history.pushState(null, '', pathWithSearch);
                    
                    fetch(pathWithSearch, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                    })
                    .catch(error => console.error('Error fetching paginated data:', error));
                }
            }
        });
    });
</script>
@endsection
