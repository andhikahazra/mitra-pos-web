@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-supplier">
    <div class="section-head">
        <div>
            <h1>Manajemen Supplier</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('supplier.create') }}">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Supplier
        </a>
    </div>

    <article class="panel-card">
        <form class="toolbar !flex-row !flex-wrap gap-3" method="GET" action="{{ route('supplier.index') }}" id="supplierFilterForm">
            <div class="relative flex-1 min-w-[300px]">
                <input class="field w-full" name="search" id="supplierSearchInput" type="text" placeholder="Cari nama atau nomor telepon..." 
                       value="{{ $search }}" autocomplete="off">
            </div>
            
            @if($search)
                <a class="btn btn-ghost" href="{{ route('supplier.index') }}">Reset</a>
            @endif
        </form>

        <div id="supplierTableContainer">
            @include('supplier._table')
        </div>

        {{-- Skeleton loader --}}
        <div id="supplierTableSkeleton" class="hidden">
            <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
            <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
            <div class="skeleton-row"><div class="skeleton skeleton-avatar"></div><div class="skeleton-content"><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text-sm"></div></div></div>
        </div>
    </article>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('supplierFilterForm');
        const searchInput = document.getElementById('supplierSearchInput');
        const container = document.getElementById('supplierTableContainer');

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
            .catch(error => console.error('Error fetching supplier data:', error));
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
