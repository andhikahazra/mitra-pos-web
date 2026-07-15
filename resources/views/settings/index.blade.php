@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-settings">
    @if(session('success'))
        <div class="flash-message success" data-auto-dismiss="5000" role="status" aria-live="polite">
            <strong>Berhasil</strong>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="settings-container">
        <div class="settings-tabs" role="tablist" aria-label="Settings sections">
            <button type="button" class="settings-tab active" data-tab="toko" role="tab" aria-selected="true" aria-controls="panel-toko">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Toko
            </button>
            <button type="button" class="settings-tab" data-tab="struk" role="tab">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/></svg>
                Struk
            </button>
            <button type="button" class="settings-tab" data-tab="bank" role="tab">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                Rekening Bank
            </button>
            <button type="button" class="settings-tab" data-tab="notif" role="tab">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Notifikasi
            </button>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" id="settings-form">
            @csrf
            @method('PUT')

            {{-- Tab: Toko --}}
            <div class="settings-tab-content active" data-tab-content="toko" role="tabpanel">
                <div class="settings-field-row">
                    <div class="settings-field-label">Nama Toko</div>
                    <div class="settings-field-input">
                        <input type="text" name="nama_toko" class="field setting-input" value="{{ old('nama_toko', $setting->nama_toko) }}" required disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">No. HP Toko</div>
                    <div class="settings-field-input">
                        <input type="tel" name="no_hp" id="shopPhone" class="field setting-input" value="{{ old('no_hp', $setting->no_hp) }}" disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">Alamat</div>
                    <div class="settings-field-input">
                        <input type="text" name="alamat_jalan" class="field setting-input" value="{{ old('alamat_jalan', $setting->alamat_toko['jalan'] ?? '') }}" placeholder="Nama jalan / detail" disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">Kota</div>
                    <div class="settings-field-input">
                        <input type="text" name="alamat_kota" class="field setting-input" value="{{ old('alamat_kota', $setting->alamat_toko['kota'] ?? '') }}" disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">Provinsi</div>
                    <div class="settings-field-input">
                        <input type="text" name="alamat_provinsi" class="field setting-input" value="{{ old('alamat_provinsi', $setting->alamat_toko['provinsi'] ?? '') }}" disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">Slogan</div>
                    <div class="settings-field-input">
                        <input type="text" name="deskripsi_slogan" class="field setting-input" value="{{ old('deskripsi_slogan', $setting->deskripsi['slogan'] ?? '') }}" placeholder="Belanja Hemat Tiap Hari" disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">Keterangan</div>
                    <div class="settings-field-input">
                        <input type="text" name="deskripsi_keterangan" class="field setting-input" value="{{ old('deskripsi_keterangan', $setting->deskripsi['keterangan'] ?? '') }}" placeholder="Melayani Grosir & Eceran" disabled>
                    </div>
                </div>
                <div class="settings-field-row">
                    <div class="settings-field-label">Biaya Admin QRIS</div>
                    <div class="settings-field-input">
                        <input type="text" name="biaya_admin_qris" id="shopAdminFee" class="field setting-input" value="{{ old('biaya_admin_qris', $setting->biaya_admin_qris ? number_format($setting->biaya_admin_qris, 0, ',', '.') : '') }}" required disabled>
                        <div class="settings-field-hint">Biaya ditambahkan pada transaksi QRIS</div>
                    </div>
                </div>
            </div>

            {{-- Tab: Struk --}}
            <div class="settings-tab-content" data-tab-content="struk" role="tabpanel">
                <div class="settings-field-row" style="border-bottom: none;">
                    <div class="settings-field-label">Footer Nota</div>
                    <div class="settings-field-input">
                        <textarea name="footer_nota" class="field setting-input" placeholder="Terima kasih telah berbelanja..." disabled>{{ old('footer_nota', $setting->footer_nota) }}</textarea>
                        <div class="settings-field-hint">Pesan ini muncul di bagian bawah struk</div>
                    </div>
                </div>
            </div>

            {{-- Tab: Bank --}}
            <div class="settings-tab-content" data-tab-content="bank" role="tabpanel">
                <div class="bank-grid">
                    <div class="bank-card">
                        <div class="bank-card-header">
                            <div class="bank-card-badge primary">1</div>
                            <span class="bank-card-title">Rekening Utama</span>
                        </div>
                        <div class="settings-field-row" style="border-bottom: none;">
                            <div class="settings-field-label">Bank</div>
                            <div class="settings-field-input">
                                <input type="text" name="bank_name_1" class="field setting-input" placeholder="BCA" value="{{ old('bank_name_1', $setting->rekening_bank[0]['bank'] ?? '') }}" disabled>
                            </div>
                        </div>
                        <div class="settings-field-row" style="border-bottom: none;">
                            <div class="settings-field-label">No. Rekening</div>
                            <div class="settings-field-input">
                                <input type="text" name="bank_no_1" id="bankNo1" class="field setting-input" placeholder="1234567890" value="{{ old('bank_no_1', $setting->rekening_bank[0]['no'] ?? '') }}" disabled>
                            </div>
                        </div>
                        <div class="settings-field-row" style="border-bottom: none;">
                            <div class="settings-field-label">Nama Pemilik</div>
                            <div class="settings-field-input">
                                <input type="text" name="bank_pemilik_1" class="field setting-input" placeholder="Nama sesuai KTP" value="{{ old('bank_pemilik_1', $setting->rekening_bank[0]['nama'] ?? '') }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="bank-card">
                        <div class="bank-card-header">
                            <div class="bank-card-badge secondary">2</div>
                            <span class="bank-card-title">Rekening Cadangan</span>
                        </div>
                        <div class="settings-field-row" style="border-bottom: none;">
                            <div class="settings-field-label">Bank</div>
                            <div class="settings-field-input">
                                <input type="text" name="bank_name_2" class="field setting-input" placeholder="BRI" value="{{ old('bank_name_2', $setting->rekening_bank[1]['bank'] ?? '') }}" disabled>
                            </div>
                        </div>
                        <div class="settings-field-row" style="border-bottom: none;">
                            <div class="settings-field-label">No. Rekening</div>
                            <div class="settings-field-input">
                                <input type="text" name="bank_no_2" id="bankNo2" class="field setting-input" placeholder="0987654321" value="{{ old('bank_no_2', $setting->rekening_bank[1]['no'] ?? '') }}" disabled>
                            </div>
                        </div>
                        <div class="settings-field-row" style="border-bottom: none;">
                            <div class="settings-field-label">Nama Pemilik</div>
                            <div class="settings-field-input">
                                <input type="text" name="bank_pemilik_2" class="field setting-input" placeholder="Nama sesuai KTP" value="{{ old('bank_pemilik_2', $setting->rekening_bank[1]['nama'] ?? '') }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab: Notifikasi --}}
            <div class="settings-tab-content" data-tab-content="notif" role="tabpanel">
                <div class="settings-field-row" style="border-bottom: none;">
                    <div class="settings-field-label">No. HP WhatsApp</div>
                    <div class="settings-field-input">
                        <input type="tel" name="no_hp_rop_notif" id="ropNotifPhone" class="field setting-input" value="{{ old('no_hp_rop_notif', $setting->no_hp_rop_notif ?? '') }}" placeholder="81234567890" disabled>
                        <div class="settings-field-hint">Nomor untuk menerima alert stok kritis via Fonnte</div>
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <div class="settings-actions-hint" id="settings-hint">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    Klik "Edit" untuk mengubah pengaturan
                </div>
                <div id="action-buttons" class="flex items-center gap-3" style="display: none;">
                    <button type="button" id="btn-cancel" class="btn btn-ghost h-9 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary h-9 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1-2 2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan
                    </button>
                </div>
                <button type="button" id="btn-edit" class="btn btn-ghost h-9 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                    Edit
                </button>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.settings-tab');
    const tabContents = document.querySelectorAll('.settings-tab-content');
    const btnEdit = document.getElementById('btn-edit');
    const btnCancel = document.getElementById('btn-cancel');
    const actionButtons = document.getElementById('action-buttons');
    const settingsHint = document.getElementById('settings-hint');
    const inputs = document.querySelectorAll('.setting-input');
    const adminFeeInput = document.getElementById('shopAdminFee');
    const shopPhoneInput = document.getElementById('shopPhone');
    const form = document.getElementById('settings-form');

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.querySelector(`[data-tab-content="${target}"]`).classList.add('active');
        });
    });

    // Rupiah formatter
    function formatRupiah(value) {
        if (!value) return '';
        const clean = value.toString().replace(/\D/g, '');
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(clean);
    }

    if (adminFeeInput) {
        adminFeeInput.addEventListener('input', function() {
            this.value = formatRupiah(this.value.replace(/\D/g, ''));
        });
    }

    if (shopPhoneInput) {
        shopPhoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    const ropNotifPhoneInput = document.getElementById('ropNotifPhone');
    if (ropNotifPhoneInput) {
        ropNotifPhoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    const bankNo1Input = document.getElementById('bankNo1');
    const bankNo2Input = document.getElementById('bankNo2');
    if (bankNo1Input) {
        bankNo1Input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }
    if (bankNo2Input) {
        bankNo2Input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    if (form) {
        form.addEventListener('submit', function() {
            if (adminFeeInput) {
                adminFeeInput.value = adminFeeInput.value.replace(/\D/g, '');
            }
        });
    }

    // Edit mode
    btnEdit.addEventListener('click', function() {
        inputs.forEach(input => input.disabled = false);
        actionButtons.style.display = 'flex';
        settingsHint.style.display = 'none';
        btnEdit.style.display = 'none';
        document.querySelector('input[name="nama_toko"]').focus();
    });

    btnCancel.addEventListener('click', function() {
        inputs.forEach(input => input.disabled = true);
        actionButtons.style.display = 'none';
        settingsHint.style.display = 'flex';
        btnEdit.style.display = 'flex';
    });
});
</script>
@endsection
