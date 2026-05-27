@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-log-stok">
    <div class="section-head">
        <div>
            <h1>Log Pergerakan Stok</h1>
        </div>
    </div>

    <article class="panel-card">
        <form class="toolbar !flex-row !flex-wrap gap-3" id="logStokFilterForm" method="GET" action="{{ route('log-stok.index') }}">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[300px]">
                <input class="field w-full" name="search" id="logStokSearch" type="text" placeholder="Cari produk atau keterangan..." value="{{ $search }}" autocomplete="off">
            </div>

            <!-- Product Dropdown Filter -->
            <div class="min-w-[200px]">
                <select class="field w-full" name="produk_id" id="logStokProduct">
                    <option value="all" {{ $productFilter === 'all' ? 'selected' : '' }}>Semua Produk</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ (string)$productFilter === (string)$prod->id ? 'selected' : '' }}>
                            {{ $prod->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Type Dropdown Filter -->
            <div class="min-w-[150px]">
                <select class="field w-full" name="type" id="logStokType">
                    <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="Masuk" {{ $typeFilter === 'Masuk' ? 'selected' : '' }}>Masuk</option>
                    <option value="Keluar" {{ $typeFilter === 'Keluar' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>

            @if($search || $typeFilter !== 'all' || $productFilter !== 'all')
                <a href="{{ route('log-stok.index') }}" class="btn btn-ghost" style="text-decoration: none; display: flex; align-items: center;">Reset Filter</a>
            @endif
        </form>

        <div id="logStokTableContainer">
            @include('log-stok._table')
        </div>
    </article>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('logStokFilterForm');
        const searchInput = document.getElementById('logStokSearch');
        const productSelect = document.getElementById('logStokProduct');
        const typeSelect = document.getElementById('logStokType');
        const container = document.getElementById('logStokTableContainer');

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
            .catch(error => console.error('Error fetching stock log data:', error));
        };

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performFilter, 300);
        });

        productSelect.addEventListener('change', performFilter);
        typeSelect.addEventListener('change', performFilter);
    });
</script>
@endsection
