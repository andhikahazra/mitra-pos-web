@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-log-stok">
    <div class="section-head">
        <div>
            <h1>Log Pergerakan Stok</h1>
            <p>Riwayat kronologis arus masuk dan keluar barang di gudang/toko.</p>
        </div>
    </div>

    <article class="panel-card">
        <form class="toolbar !flex-row !items-center !gap-3 !flex-nowrap" id="logFilterForm" method="GET" action="{{ route('log-stok.index') }}">
            <div class="relative flex-1">
                <input class="field w-full" name="search" id="logSearchInput" type="text" placeholder="Cari produk atau keterangan..." value="{{ $search }}" autocomplete="off">
            </div>

            <select class="field w-auto min-w-[180px]" name="produk_id" id="logProductFilter">
                <option value="all">Semua Produk</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected($produkId == $p->id)>{{ $p->nama }}</option>
                @endforeach
            </select>

            <select class="field w-auto min-w-[130px]" name="tipe" id="logTypeFilter">
                <option value="all" @selected($tipe === 'all')>Semua Tipe</option>
                <option value="masuk" @selected($tipe === 'masuk')>Masuk</option>
                <option value="keluar" @selected($tipe === 'keluar')>Keluar</option>
            </select>

            @if($search || $tipe !== 'all' || $produkId !== 'all')
                <a href="{{ route('log-stok.index') }}" class="btn btn-ghost btn-sm whitespace-nowrap">Reset</a>
            @endif
        </form>

        <div id="logTableContainer">
            @include('log-stok._table')
        </div>
    </article>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('logFilterForm');
        const searchInput = document.getElementById('logSearchInput');
        const productFilter = document.getElementById('logProductFilter');
        const typeFilter = document.getElementById('logTypeFilter');
        const container = document.getElementById('logTableContainer');

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
            .catch(error => console.error('Error fetching log data:', error));
        };

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performFilter, 300);
        });

        productFilter.addEventListener('change', performFilter);
        typeFilter.addEventListener('change', performFilter);
    });
</script>
@endsection
