@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-transaksi">
    <div class="section-head">
        <div>
            <h1>Riwayat Transaksi</h1>
            <p>Audit transaksi POS dengan filter tanggal dan detail invoice.</p>
        </div>
    </div>

    <article class="panel-card">
        <form class="toolbar" method="GET" action="{{ route('transaksi.index') }}">
            <input class="field" name="date" type="date" value="{{ $date }}">
            <button class="btn btn-ghost" type="submit">Filter</button>
            @if($date)
                <a class="btn btn-ghost" href="{{ route('transaksi.index') }}">Reset Filter</a>
            @endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $trx)
                        <tr>
                            <td>{{ $trx->kode }}</td>
                            <td>{{ $trx->tanggal ? $trx->tanggal->format('Y-m-d') : '-' }}</td>
                            <td>{{ $trx->user->nama ?? '-' }}</td>
                            <td>{{ $trx->detail_transaksi->sum('jumlah') }} item</td>
                            <td>Rp {{ number_format((float) $trx->total_harga, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->status === 'Selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-warning">Tertunda</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="link-btn more" href="{{ route('transaksi.show', $trx) }}">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-500">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $transaksi->links() }}
        </div>
    </article>
</section>
@endsection
