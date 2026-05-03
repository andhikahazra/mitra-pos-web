@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-produk-create">
        <div class="section-head">
            <div>
                <h1>Tambah Produk</h1>
                <p>Buat produk baru dengan data utama, dimensi, dan foto.</p>
            </div>
            <a class="btn btn-ghost" href="{{ route('produk.index') }}">Kembali ke Produk</a>
        </div>

        <article class="panel-card">
            @include('produk._form', [
                'action' => route('produk.store'),
                'method' => 'POST',
            ])
        </article>
    </section>
@endsection
