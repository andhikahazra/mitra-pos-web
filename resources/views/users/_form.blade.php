@php $isEdit = isset($user); @endphp

<form class="form-grid" method="POST"
      action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <label>Nama
        <input class="field" name="nama" required
               value="{{ old('nama', $isEdit ? $user->nama : '') }}">
    </label>

    <label>Email
        <input class="field" name="email" type="email" required
               value="{{ old('email', $isEdit ? $user->email : '') }}">
    </label>

    <label>Role
        <select class="field" name="role" required>
            <option value="Pemilik"  @selected(old('role', $isEdit ? $user->role : '') === 'Pemilik')>Pemilik</option>
            <option value="Karyawan" @selected(old('role', $isEdit ? $user->role : '') === 'Karyawan')>Karyawan</option>
        </select>
    </label>

    <label>Status
        <select class="field" name="status" required>
            <option value="1" @selected((string) old('status', $isEdit ? (int) $user->status : '1') === '1')>Aktif</option>
            <option value="0" @selected((string) old('status', $isEdit ? (int) $user->status : '1') === '0')>Nonaktif</option>
        </select>
    </label>

    <label>{{ $isEdit ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}
        <input class="field" name="password" type="password"
               {{ $isEdit ? '' : 'required' }}
               placeholder="{{ $isEdit ? 'Isi untuk mengganti password...' : 'Minimal 8 karakter' }}">
    </label>

    <label>Konfirmasi Password
        <input class="field" name="password_confirmation" type="password"
               {{ $isEdit ? '' : 'required' }}
               placeholder="Ulangi password">
    </label>

    <div class="form-actions full">
        <a class="btn btn-ghost px-5" href="{{ route('users.index') }}">Batal</a>
        <button class="btn btn-primary px-6" type="submit">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan User
        </button>
    </div>
</form>
