@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-produk-index">
        <div class="section-head">
            <div>
                <h1>Manajemen Produk</h1>
            </div>
            <a class="btn btn-primary" href="{{ route('produk.create') }}">Tambah Produk</a>
        </div>

        <article class="panel-card">
            <form action="{{ route('produk.index') }}" method="GET" class="toolbar" id="productFilterForm" style="width: 100%;">
                <div style="display: flex; gap: 8px; flex: 1; align-items: center;">
                    <input class="field" name="search" id="productSearchInput" type="text" placeholder="Cari nama/SKU/kategori... (Tekan Enter)" value="{{ request('search') }}" style="flex: 1;">
                    @if(request('search'))
                        <a href="{{ route('produk.index') }}" class="btn btn-ghost" style="height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; border: 1px solid #cbd5e1; padding: 0 16px; margin: 0; background: white; white-space: nowrap;">Reset</a>
                    @endif
                </div>
                <span class="table-info" id="productMeta">Total {{ $produk->total() }} produk</span>
            </form>
            
            <div id="productTableContainer">
                @include('produk._table')
            </div>

            {{-- Skeleton loader (hidden by default) --}}
            <div id="productTableSkeleton" class="hidden">
                <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
                <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
                <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
                <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
                <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
            </div>
        </article>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('productFilterForm');
            const searchInput = document.getElementById('productSearchInput');
            const container = document.getElementById('productTableContainer');
            const metaText = document.getElementById('productMeta');

            let debounceTimer;

            const performFilter = () => {
                const formData = new URLSearchParams(new FormData(form)).toString();
                const targetUrl = `${window.location.pathname}?${formData}`;

                window.history.pushState(null, '', targetUrl);

                const fetchUrl = targetUrl + (targetUrl.includes('?') ? '&' : '?') + 'ajax=1';

                fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    
                    // Update total count meta
                    const tableWrap = container.querySelector('#productTableWrap');
                    if (tableWrap && tableWrap.dataset.total) {
                        metaText.textContent = `Total ${tableWrap.dataset.total} produk`;
                    }
                })
                .catch(error => console.error('Error fetching product data:', error));
            };

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performFilter, 300);
            });



            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                performFilter();
            });
        });
    </script>
@endsection
