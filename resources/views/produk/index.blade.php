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
            <div class="toolbar">
                <input class="field" id="productSearch" type="text" placeholder="Cari nama/SKU/kategori/tipe...">
                <span class="table-info" id="productMeta">Total {{ $produk->count() }} produk</span>
            </div>
            
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
        </article>
    </section>
@endsection
