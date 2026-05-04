@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-settings">
    <div class="section-head">
        <div>
            <h1>Pengaturan Sistem</h1>
        </div>
        <button type="button" id="btn-edit" class="btn secondary" style="padding: 8px 20px;">
            <i class="fas fa-edit"></i> Edit Pengaturan
        </button>
    </div>

    @if(session('success'))
        <div class="status-pill success" style="margin-bottom: 20px; width: 100%; justify-content: center; padding: 12px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" id="settings-form">
        @csrf
        @method('PUT')
        
        <div class="settings-grid">
            <article class="panel-card" style="grid-column: span 2;">
                <div class="panel-head">
                    <h2>Informasi Toko</h2>
                </div>
                <div style="display: grid; gap: 15px; padding: 15px;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                        <div class="field-group">
                            <label>Nama Toko</label>
                            <input type="text" name="nama_toko" class="field setting-input" value="{{ old('nama_toko', $setting->nama_toko) }}" required disabled>
                        </div>
                        <div class="field-group">
                            <label>No. HP Toko</label>
                            <input type="text" name="no_hp" class="field setting-input" value="{{ old('no_hp', $setting->no_hp) }}" disabled>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Alamat Toko (Nama Jalan / Detail)</label>
                        <input type="text" name="alamat_jalan" class="field setting-input" value="{{ old('alamat_jalan', $setting->alamat_toko['jalan'] ?? '') }}" disabled>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div class="field-group">
                            <label>Kota/Kabupaten</label>
                            <input type="text" name="alamat_kota" class="field setting-input" value="{{ old('alamat_kota', $setting->alamat_toko['kota'] ?? '') }}" disabled>
                        </div>
                        <div class="field-group">
                            <label>Provinsi</label>
                            <input type="text" name="alamat_provinsi" class="field setting-input" value="{{ old('alamat_provinsi', $setting->alamat_toko['provinsi'] ?? '') }}" disabled>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="field-group">
                            <label>Slogan Toko (Tagline)</label>
                            <input type="text" name="deskripsi_slogan" class="field setting-input" value="{{ old('deskripsi_slogan', $setting->deskripsi['slogan'] ?? '') }}" placeholder="Contoh: Belanja Hemat Tiap Hari" disabled>
                        </div>
                        <div class="field-group">
                            <label>Keterangan Tambahan</label>
                            <input type="text" name="deskripsi_keterangan" class="field setting-input" value="{{ old('deskripsi_keterangan', $setting->deskripsi['keterangan'] ?? '') }}" placeholder="Contoh: Melayani Grosir & Eceran" disabled>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Biaya Admin QRIS (Rp)</label>
                        <input type="number" name="biaya_admin_qris" class="field setting-input" value="{{ old('biaya_admin_qris', round($setting->biaya_admin_qris)) }}" required disabled>
                    </div>
                </div>
            </article>

            <article class="panel-card">
                <div class="panel-head">
                    <h2>Konfigurasi Struk</h2>
                </div>
                <div style="padding: 15px;">
                    <div class="field-group">
                        <label>Pesan Footer Nota (Bawah)</label>
                        <textarea name="footer_nota" class="field setting-input" style="height: 100px;" disabled>{{ old('footer_nota', $setting->footer_nota) }}</textarea>
                    </div>
                </div>
            </article>

            <article class="panel-card" style="grid-column: span 2;">
                <div class="panel-head">
                    <h2>Rekening Bank Pembayaran</h2>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 15px;">
                    <!-- Slot 1 -->
                    <div style="border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px;">
                        <h3 style="font-size: 14px; margin-bottom: 10px; color: #1e293b;">Rekening Utama (Slot 1)</h3>
                        <div class="field-group" style="margin-bottom: 10px;">
                            <label>Nama Bank (Contoh: BCA)</label>
                            <input type="text" name="bank_name_1" class="field setting-input" value="{{ old('bank_name_1', $setting->rekening_bank[0]['bank'] ?? '') }}" disabled>
                        </div>
                        <div class="field-group" style="margin-bottom: 10px;">
                            <label>No. Rekening</label>
                            <input type="text" name="bank_no_1" class="field setting-input" value="{{ old('bank_no_1', $setting->rekening_bank[0]['no'] ?? '') }}" disabled>
                        </div>
                        <div class="field-group">
                            <label>Nama Pemilik Rekening</label>
                            <input type="text" name="bank_pemilik_1" class="field setting-input" value="{{ old('bank_pemilik_1', $setting->rekening_bank[0]['nama'] ?? '') }}" disabled>
                        </div>
                    </div>

                    <!-- Slot 2 -->
                    <div style="border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px;">
                        <h3 style="font-size: 14px; margin-bottom: 10px; color: #1e293b;">Rekening Cadangan (Slot 2)</h3>
                        <div class="field-group" style="margin-bottom: 10px;">
                            <label>Nama Bank (Contoh: BRI)</label>
                            <input type="text" name="bank_name_2" class="field setting-input" value="{{ old('bank_name_2', $setting->rekening_bank[1]['bank'] ?? '') }}" disabled>
                        </div>
                        <div class="field-group" style="margin-bottom: 10px;">
                            <label>No. Rekening</label>
                            <input type="text" name="bank_no_2" class="field setting-input" value="{{ old('bank_no_2', $setting->rekening_bank[1]['no'] ?? '') }}" disabled>
                        </div>
                        <div class="field-group">
                            <label>Nama Pemilik Rekening</label>
                            <input type="text" name="bank_pemilik_2" class="field setting-input" value="{{ old('bank_pemilik_2', $setting->rekening_bank[1]['nama'] ?? '') }}" disabled>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div id="action-buttons" style="margin-top: 20px; display: none; justify-content: flex-end; gap: 10px;">
            <button type="button" id="btn-cancel" class="btn secondary" style="padding: 10px 25px;">Batal</button>
            <button type="submit" class="btn primary" style="padding: 10px 25px;">Simpan Perubahan</button>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnEdit = document.getElementById('btn-edit');
    const btnCancel = document.getElementById('btn-cancel');
    const actionButtons = document.getElementById('action-buttons');
    const inputs = document.querySelectorAll('.setting-input');

    btnEdit.addEventListener('click', function() {
        // Enable all inputs
        inputs.forEach(input => {
            input.disabled = false;
        });
        
        // Show Save/Cancel buttons, hide Edit button
        actionButtons.style.display = 'flex';
        btnEdit.style.display = 'none';
        
        // Focus on first input
        document.querySelector('input[name="nama_toko"]').focus();
    });

    btnCancel.addEventListener('click', function() {
        // Disable all inputs
        inputs.forEach(input => {
            input.disabled = true;
        });
        
        // Hide Save/Cancel buttons, show Edit button
        actionButtons.style.display = 'none';
        btnEdit.style.display = 'block';
        
        // Optional: Reset form values if user cancelled
        // location.reload(); 
    });
});
</script>
@endsection
