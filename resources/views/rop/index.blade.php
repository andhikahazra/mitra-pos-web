@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-rop">
    <div class="section-head">
        <div>
            <h1>Report Reorder Point</h1>
        </div>
    </div>

    <article class="panel-card">
        <form class="toolbar flex items-center gap-3 !mb-6" method="GET" action="{{ route('rop.index') }}" id="ropFilterForm">
            <div class="relative flex-1">
                <input class="field w-full" name="search" id="ropSearchInput" type="text" placeholder="Cari nama produk..." 
                       value="{{ $search }}" autocomplete="off">
            </div>
            
            <select class="field w-auto min-w-[160px]" name="status" id="ropStatusFilter">
                <option value="all"          @selected($statusFilter === 'all')>Semua Status</option>
                <option value="aman"         @selected($statusFilter === 'aman')>Aman</option>
                <option value="hampir habis" @selected($statusFilter === 'hampir habis')>Hampir Habis</option>
                <option value="harus restock" @selected($statusFilter === 'harus restock')>Harus Restock</option>
            </select>
            
            <select class="field w-auto min-w-[160px]" name="sort" id="ropSortFilter">
                <option value="name"     @selected($sort === 'name')>Urut Nama</option>
                <option value="stockAsc" @selected($sort === 'stockAsc')>Stok Terendah</option>
                <option value="ropDesc"  @selected($sort === 'ropDesc')>ROP Tertinggi</option>
            </select>

            @if($search || $statusFilter !== 'all' || $sort !== 'name')
                <a class="btn btn-ghost !h-11" href="{{ route('rop.index') }}">Reset</a>
            @endif
        </form>

        <div class="table-wrap" id="ropTableContainer">
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Stok Saat Ini</th>
                        <th>Safety Stock</th>
                        <th>Lead Time</th>
                        <th>ROP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="ropTableBody">
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['stock'] }}</td>
                            <td>{{ $row['safetyStock'] }}</td>
                            <td>{{ $row['leadTime'] }} hari</td>
                            <td>{{ $row['rop'] }}</td>
                            <td>
                                @if($row['status'] === 'aman')
                                    <span class="status-pill success">Aman</span>
                                @elseif($row['status'] === 'hampir habis')
                                    <span class="status-pill warning">Hampir Habis</span>
                                @else
                                    <span class="status-pill danger">Harus Restock</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('rop.show', $row['id']) }}" class="link-btn more">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500 py-8">Tidak ada data yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ropFilterForm');
        const searchInput = document.getElementById('ropSearchInput');
        const statusFilter = document.getElementById('ropStatusFilter');
        const sortFilter = document.getElementById('ropSortFilter');
        const tableContainer = document.getElementById('ropTableContainer');

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
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('ropTableContainer').innerHTML;
                tableContainer.innerHTML = newTable;
            })
            .catch(error => console.error('Error fetching ROP data:', error));
        };

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performFilter, 300);
        });

        statusFilter.addEventListener('change', performFilter);
        sortFilter.addEventListener('change', performFilter);
    });
</script>
@endsection
