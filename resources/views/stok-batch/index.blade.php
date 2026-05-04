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
        <article class="kpi-card" style="border-left: 4px solid #6366f1;">
            <p class="kpi-label">Total Modal Aset (Stok)</p>
            <h3 style="color: #1e1b4b;">Rp {{ number_format($summary['total_modal'], 0, ',', '.') }}</h3>
            <p class="kpi-trend info">Uang yang mengendap di stok barang</p>
        </article>
        <article class="kpi-card" style="border-left: 4px solid #10b981;">
            <p class="kpi-label">Total Nilai Jual Aset</p>
            <h3 style="color: #064e3b;">Rp {{ number_format($summary['total_aset'], 0, ',', '.') }}</h3>
            <p class="kpi-trend positive">Potensi uang jika semua terjual</p>
        </article>
        <article class="kpi-card" style="border-left: 4px solid #f59e0b;">
            <p class="kpi-label">Potensi Margin (Laba Kotor)</p>
            <h3 style="color: #78350f;">Rp {{ number_format($summary['total_aset'] - $summary['total_modal'], 0, ',', '.') }}</h3>
            <p class="kpi-trend warning">Selisih nilai jual vs modal</p>
        </article>
    </div>

    <article class="panel-card">
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
    </article>
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
    });
</script>
@endsection
