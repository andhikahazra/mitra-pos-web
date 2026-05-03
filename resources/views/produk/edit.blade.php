@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-produk-edit">
        <div class="section-head">
            <div>
                <h1>Edit Produk</h1>
                <p>Perbarui data produk tanpa keluar dari modul produk terpisah.</p>
            </div>
            <a class="btn btn-ghost" href="{{ route('produk.index') }}">Kembali ke Produk</a>
        </div>

        <article class="panel-card">
            @include('produk._form', [
                'action' => route('produk.update', $produk),
                'method' => 'PUT',
                'produk' => $produk,
            ])
        </article>
    </section>
@endsection
