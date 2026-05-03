@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-supplier-create">
    <div class="section-head">
        <div>
            <h1>Tambah Supplier</h1>
            <p>Daftarkan mitra pemasok baru ke dalam sistem.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('supplier.index') }}">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7 7l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <article class="panel-card">
        @include('supplier._form')
    </article>
</section>
@endsection
