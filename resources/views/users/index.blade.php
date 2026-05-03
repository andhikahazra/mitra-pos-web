@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-users">
    <div class="section-head">
        <div>
            <h1>Manajemen User</h1>
            <p>Kelola akun pengguna, role, dan status akses.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('users.create') }}">Tambah User</a>
    </div>

    <article class="panel-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @if($user->status)
                                    <span class="status-pill success">Aktif</span>
                                @else
                                    <span class="status-pill danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="link-btn edit" href="{{ route('users.edit', $user) }}">Edit</a>
                                    <form method="POST" action="{{ route('users.toggle', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="link-btn more" type="submit">
                                            {{ $user->status ? 'Deaktivasi' : 'Aktivasi' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate-500">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</section>
@endsection
