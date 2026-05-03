@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-user-edit">
    <div class="section-head">
        <div>
            <h1>Edit User</h1>
            <p>Perbarui data akun pengguna.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('users.index') }}">Kembali ke User</a>
    </div>

    <article class="panel-card">
        @include('users._form', ['user' => $user])
    </article>
</section>
@endsection
