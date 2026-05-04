@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-stok-monitoring">
    <div class="section-head">
        <div>
            <h1>Monitoring Stok & Inventori</h1>
        </div>
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
