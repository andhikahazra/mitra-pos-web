@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-log-stok">
    <div class="section-head">
        <div>
            <h1>Log Pergerakan Stok</h1>
        </div>
    </div>

    <form class="toolbar !flex-row !flex-wrap gap-3" id="logStokFilterForm" method="GET" action="{{ route('log-stok.index') }}">
        <input type="hidden" name="range" id="logStokRangeInput" value="{{ $range }}">
        
        <div class="relative flex-1 min-w-[300px]">
            <input class="field w-full" name="search" id="logStokSearch" type="text" placeholder="Cari produk atau keterangan..." value="{{ $search }}" autocomplete="off">
        </div>

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

        <div class="min-w-[150px]">
            <select class="field w-full" name="type" id="logStokType">
                <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                <option value="Masuk" {{ $typeFilter === 'Masuk' ? 'selected' : '' }}>Masuk</option>
                <option value="Keluar" {{ $typeFilter === 'Keluar' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>

        <div class="flex-shrink-0 flex items-center gap-0.5 p-0.5 h-10 rounded-lg bg-slate-100 dark:bg-zinc-900">
            @php
                $ranges = [
                    'all'    => 'Semua',
                    'today'  => 'Hari Ini',
                    '7d'     => '7 Hari',
                    '1m'     => '30 Hari',
                    'custom' => 'Kustom',
                ];
            @endphp
            @foreach($ranges as $key => $label)
                <button type="button"
                    class="range-btn h-full px-4 text-sm font-medium rounded-md transition-colors whitespace-nowrap cursor-pointer
                        {{ $range === $key
                            ? 'bg-white dark:bg-zinc-800 text-slate-900 dark:text-zinc-100 shadow-sm'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-300' }}"
                    data-range="{{ $key }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <a href="{{ route('log-stok.index') }}" class="btn btn-ghost" style="text-decoration: none; display: flex; align-items: center;">Reset Filter</a>

        {{-- Custom range wrapper --}}
        <div id="logStokCustomRange" class="w-full flex items-center gap-2 mt-2 {{ $range === 'custom' ? '' : 'hidden' }}">
            <div class="flex-1 min-w-0">
                <input type="date" name="start_date" id="logStokStartDate" class="field text-xs w-full" value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
            </div>
            <span class="text-slate-400 dark:text-zinc-500 text-xs flex-shrink-0 font-medium">s/d</span>
            <div class="flex-1 min-w-0">
                <input type="date" name="end_date" id="logStokEndDate" class="field text-xs w-full" value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
            </div>
            <button type="button" id="logStokApplyRange" class="btn btn-primary text-xs h-10 px-4 flex-shrink-0">Terapkan</button>
        </div>
    </form>

    <div id="logStokTableContainer">
        @include('log-stok._table')
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('logStokFilterForm');
        const searchInput = document.getElementById('logStokSearch');
        const productSelect = document.getElementById('logStokProduct');
        const typeSelect = document.getElementById('logStokType');
        const container = document.getElementById('logStokTableContainer');
        const rangeButtons = document.querySelectorAll('.range-btn');
        const rangeInput = document.getElementById('logStokRangeInput');
        const customRangeWrap = document.getElementById('logStokCustomRange');
        const applyRangeBtn = document.getElementById('logStokApplyRange');
        const startDateInput = document.getElementById('logStokStartDate');
        const endDateInput = document.getElementById('logStokEndDate');

        const activeClasses = ['bg-white', 'dark:bg-zinc-800', 'text-slate-900', 'dark:text-zinc-100', 'shadow-sm'];
        const inactiveClasses = ['text-slate-500', 'dark:text-zinc-400', 'hover:text-slate-700', 'dark:hover:text-zinc-300'];

        let debounceTimer;

        const setActiveRange = (activeBtn) => {
            rangeButtons.forEach(btn => {
                btn.classList.remove(...activeClasses);
                btn.classList.add(...inactiveClasses);
            });
            activeBtn.classList.remove(...inactiveClasses);
            activeBtn.classList.add(...activeClasses);
        };

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

        // Range buttons
        rangeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const range = this.dataset.range;

                // Update active state visually
                setActiveRange(this);

                // Update hidden input
                rangeInput.value = range;

                if (range === 'custom') {
                    customRangeWrap.classList.remove('hidden');
                } else {
                    customRangeWrap.classList.add('hidden');
                    performFilter();
                }
            });
        });

        // Apply custom range
        applyRangeBtn.addEventListener('click', function() {
            const start = startDateInput.value;
            const end = endDateInput.value;
            if (!start || !end) {
                alert('Lengkapi tanggal mulai dan akhir terlebih dulu');
                return;
            }
            performFilter();
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
