@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-produk-create">
        <div class="section-head">
            <div>
                <h1>Tambah Produk</h1>
                <p>Buat produk baru dengan data utama, dimensi, dan foto.</p>
            </div>
            <div class="header-actions">
                <a class="btn btn-ghost" href="{{ route('produk.index') }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Produk
                </a>
            </div>
        </div>

        <article class="panel-card">
            @include('produk._form', [
                'action' => route('produk.store'),
                'method' => 'POST',
            ])
        </article>
    </section>
@endsection
