@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-supplier-show">
    <div class="section-head">
        <div>
            <h1>Detail Supplier</h1>
            <p>Informasi lengkap dan riwayat transaksi pengadaan dari {{ $supplier->nama }}.</p>
        </div>
        <div class="header-actions">
            <a class="btn btn-ghost" href="{{ route('supplier.index') }}">Kembali</a>
            <a class="btn btn-primary" href="{{ route('supplier.edit', $supplier) }}">Edit Supplier</a>
        </div>
    </div>

    <div class="overview-grid">
        <article class="panel-card p-4">
            <h2 class="m-0 text-base font-semibold text-slate-800">Informasi Supplier</h2>
            <ul class="metric-list mt-3">
                <li><span>Nama</span><strong>{{ $supplier->nama }}</strong></li>
                <li><span>No. Telepon</span><strong>{{ $supplier->no_telp ?? '-' }}</strong></li>
                <li><span>Alamat</span><strong>{{ $supplier->alamat ?? '-' }}</strong></li>
            </ul>
        </article>

        <article class="panel-card p-4">
            <h2 class="m-0 text-base font-semibold text-slate-800">Statistik Pengadaan</h2>
            <ul class="metric-list mt-3">
                <li><span>Total Pengadaan</span><strong>{{ $supplier->barangMasuk->count() }} Dokumen</strong></li>
                <li><span>Terakhir Pengadaan</span><strong>{{ $supplier->barangMasuk->first() ? ($supplier->barangMasuk->first()->tanggal_terima ? $supplier->barangMasuk->first()->tanggal_terima->format('d M Y') : '-') : '-' }}</strong></li>
            </ul>
        </article>
    </div>

    <article class="panel-card mt-6">
        <div class="panel-head">
            <h2>Riwayat 10 Pengadaan Terakhir</h2>
            <span class="tag blue">Barang Masuk</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Dokumen</th>
                        <th>Tanggal Terima</th>
                        <th>Diterima Oleh</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplier->barangMasuk as $bm)
                        <tr>
                            <td class="font-medium">{{ $bm->kode }}</td>
                            <td>{{ $bm->tanggal_terima ? $bm->tanggal_terima->format('d M Y') : '-' }}</td>
                            <td>{{ $bm->user->nama ?? '-' }}</td>
                            <td>
                                @if($bm->status === 'disetujui')
                                    <span class="status-pill success">Disetujui</span>
                                @elseif($bm->status === 'menunggu')
                                    <span class="status-pill warning">Menunggu</span>
                                @else
                                    <span class="status-pill danger">{{ ucfirst($bm->status) }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a class="link-btn more" href="{{ route('barang-masuk.show', $bm) }}">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-500 py-6">Belum ada riwayat pengadaan dari supplier ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($supplier->barangMasuk->count() > 0)
            <div class="mt-4 p-4 bg-slate-50 rounded-lg text-sm text-slate-600">
                Data di atas hanya menampilkan 10 transaksi terakhir. Untuk riwayat lengkap, gunakan fitur Barang Masuk.
            </div>
        @endif
    </article>
</section>
@endsection
