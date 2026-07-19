@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-users">
    <div class="section-head">
        <div>
            <h1>Manajemen User</h1>
            
        </div>
        <a class="btn btn-primary" href="{{ route('users.create') }}">Tambah User</a>
    </div>

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
                            <td colspan="5" class="p-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a5.971 5.971 0 00-.94 3.197M15.659 10.21a3 3 0 00-4.243 0m0 0a3 3 0 012.122 5.177m-2.122-5.177a3 3 0 01-2.122-5.177m-5.375 4.294a4.486 4.486 0 002.89-1.95 4.497 4.497 0 000-4.897m5.64 0a4.486 4.486 0 00-2.89-1.95 4.497 4.497 0 000 4.897"/></svg>
                                    </div>
                                    <p class="empty-state-title">Belum ada user</p>
                                    <p class="empty-state-desc">Belum ada data pengguna yang terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</section>
@endsection
