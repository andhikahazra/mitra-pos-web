@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-user-create">
    <div class="section-head">
        <div>
            <h1>Tambah User</h1>
            <p>Buat akun pengguna baru dengan role dan status.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('users.index') }}">Kembali ke User</a>
    </div>

    <article class="panel-card">
        @include('users._form')
    </article>
</section>
@endsection
