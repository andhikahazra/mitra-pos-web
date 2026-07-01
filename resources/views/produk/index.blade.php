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
                <div class="table-wrap" id="productTableWrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            @forelse ($produk as $item)
                                @php
                                    $searchText = strtolower(trim($item->nama . ' ' . $item->sku . ' ' . ($item->kategori->nama ?? '-') . ' ' . $item->tipe_produk));
                                @endphp
    
                                <tr data-search="{{ $searchText }}">
                                    <td><span class="product-mark">{{ strtoupper(substr($item->nama, 0, 1)) }}</span>{{ $item->nama }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->kategori->nama ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) $item->harga, 0, ',', '.') }}</td>
                                    <td>{{ (int) $item->stok }}</td>
                                    <td>{{ $item->tipe_produk }}</td>
                                    <td>
                                        @if ($item->status)
                                            <span class="status-pill success">Aktif</span>
                                        @else
                                            <span class="status-pill danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="row-actions">
                                            <a class="link-btn more" href="{{ route('produk.show', $item) }}">Detail</a>
                                            <a class="link-btn edit" href="{{ route('produk.edit', $item) }}">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-slate-500">Belum ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
    
                <div class="pagination">
                    {{ $produk->links() }}
                </div>
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

                fetch(targetUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Update table contents
                    const newTable = doc.getElementById('productTableContainer').innerHTML;
                    container.innerHTML = newTable;
                    
                    // Update total count meta
                    const newMeta = doc.getElementById('productMeta').textContent;
                    metaText.textContent = newMeta;
                })
                .catch(error => console.error('Error fetching product data:', error));
            };

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performFilter, 300);
            });

            container.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination a');
                if (link) {
                    e.preventDefault();
                    const targetUrl = link.getAttribute('href');

                    window.history.pushState(null, '', targetUrl);

                    fetch(targetUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        container.innerHTML = doc.getElementById('productTableContainer').innerHTML;
                    })
                    .catch(error => console.error('Error fetching paginated product data:', error));
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                performFilter();
            });
        });
    </script>
@endsection
