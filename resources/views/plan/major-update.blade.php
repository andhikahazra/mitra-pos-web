<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rencana Major Update MitraPOS</title>
    <style>
        @page { margin: 2cm 1.5cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.5; color: #1f2937; }
        h1 { font-size: 22px; color: #111827; border-bottom: 3px solid #1e40af; padding-bottom: 8px; margin-bottom: 4px; }
        h2 { font-size: 16px; color: #1e40af; border-left: 4px solid #1e40af; padding-left: 10px; margin: 20px 0 10px; }
        h3 { font-size: 13px; color: #374151; margin: 14px 0 6px; }
        h4 { font-size: 12px; color: #4b5563; margin: 10px 0 4px; }
        .meta { font-size: 10px; color: #6b7280; margin-bottom: 20px; }
        .meta span { display: inline-block; margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; vertical-align: top; }
        th { background: #1e40af; color: white; font-weight: 600; text-align: left; }
        tr:nth-child(even) td { background: #f9fafb; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: 600; text-transform: uppercase; }
        .badge-high { background: #fee2e2; color: #dc2626; }
        .badge-medium { background: #fef3c7; color: #d97706; }
        .badge-low { background: #dcfce7; color: #16a34a; }
        ul { margin: 6px 0 6px 18px; padding: 0; }
        li { margin: 3px 0; }
        .phase { margin: 14px 0; page-break-inside: avoid; }
        .phase-title { font-size: 13px; font-weight: 600; color: #1e40af; background: #eff6ff; padding: 6px 10px; border-radius: 4px; margin-bottom: 8px; }
        .risk { background: #fef2f2; border-left: 4px solid #ef4444; padding: 8px 12px; margin: 10px 0; font-size: 10px; }
        .toc { margin: 20px 0; }
        .toc li { margin: 4px 0; }
        .toc a { text-decoration: none; color: #1e40af; }
        .footer { position: fixed; bottom: -1.5cm; left: 1.5cm; right: 1.5cm; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    </style>
</head>
<body>
    <h1>Rencana Major Update MitraPOS</h1>
    <p class="meta">
        <span><strong>Versi:</strong> 1.0</span>
        <span><strong>Tanggal:</strong> {{ $generated_at }}</span>
        <span><strong>Disusun oleh:</strong> Tim Pengembangan</span>
    </p>

    <div class="toc">
        <h2>Daftar Isi</h2>
        <ul>
            <li><a href="#latar-belakang">1. Latar Belakang & Tujuan</a></li>
            <li><a href="#role-saat-ini">2. Analisis Role Saat Ini</a></li>
            <li><a href="#role-baru">3. Desain Role Baru</a></li>
            <li><a href="#modul">4. Perubahan per Modul</a></li>
            <li><a href="#tahapan">5. Tahapan Implementasi</a></li>
            <li><a href="#risiko">6. Risiko & Mitigasi</a></li>
        </ul>
    </div>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">

    <section id="latar-belakang">
        <h2>1. Latar Belakang & Tujuan</h2>
        <p>Saat ini MitraPOS memiliki dua role yang terpisah secara ketat:</p>
        <ul>
            <li><strong>Pemilik</strong> — hanya bisa akses dashboard web (manajemen, analitik, approval).</li>
            <li><strong>Karyawan</strong> — hanya bisa akses aplikasi mobile/API (POS, input barang masuk). Diblokir total dari dashboard web.</li>
        </ul>
        <p><strong>Masalah:</strong> Banyak toko butuh operator kasir di web (desktop/laptop di kasir) bukan di mobile. Role "Karyawan" terlalu umum dan tidak punya akses web sama sekali.</p>
        <p><strong>Tujuan:</strong> Tambah role <strong>Kasir</strong> yang bisa login dashboard web untuk menjalankan POS, serta bagi role Karyawan menjadi <strong>Kasir</strong> (transaksi) dan <strong>Gudang</strong> (stok/barang masuk) — masing-masing dengan akses terbatas & terpisah.</p>
    </section>

    <section id="role-saat-ini">
        <h2>2. Analisis Role Saat Ini</h2>
        <table>
            <thead><tr><th>Role</th><th>Web Dashboard</th><th>API/Mobile</th><th>Fitur Utama</th></tr></thead>
            <tbody>
                <tr>
                    <td>Pemilik</td><td>✅ Full Access</td><td>❌ Diblokir login</td>
                    <td>Dashboard, Produk CRUD, Supplier CRUD, Barang Masuk Approve, ROP, Laporan, Log Stok, Batch, Transaksi (read-only), User Management, Settings</td>
                </tr>
                <tr>
                    <td>Karyawan</td><td>❌ Diblokir (logout + error)</td><td>✅ Full Access</td>
                    <td>POS Transaksi (create), Katalog Produk, Kategori, Barang Masuk (create + approve), Supplier list, Customer history, Dashboard mobile, Settings (read untuk receipt)</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section id="role-baru">
        <h2>3. Desain Role Baru</h2>
        <table>
            <thead><tr><th>Role</th><th>Web Login</th><th>API Login</th><th>Akses Web</th><th>Akses API</th></tr></thead>
            <tbody>
                <tr>
                    <td>Pemilik (Owner)</td><td>✅</td><td>❌</td>
                    <td>Semua fitur web (Super Admin)</td>
                    <td>Tidak ada</td>
                </tr>
                <tr>
                    <td>Kasir</td><td>✅</td><td>❌</td>
                    <td>POS Web, Katalog Produk (read), Riwayat Transaksi Sendiri, Barang Masuk (create), Dashboard Kasir</td>
                    <td>Tidak ada</td>
                </tr>
                <tr>
                    <td>Gudang (Staff Stok)</td><td>✅ (opsional)</td><td>✅ (tetap)</td>
                    <td>Barang Masuk, Log Stok, Monitoring Batch, Katalog Produk (read)</td>
                    <td>Sama seperti Karyawan saat ini (POS, Barang Masuk, dll)</td>
                </tr>
            </tbody>
        </table>
        <p style="font-size: 10px; color: #6b7280; margin-top: 8px;">* Role "Gudang" bisa jadi alias dari Karyawan yang sudah ada — cukup tambah akses web terbatas tanpa ubah API.</p>
    </section>

    <section id="modul">
        <h2>4. Perubahan per Modul</h2>
        @foreach($modules as $module)
        <div style="page-break-inside: avoid; margin-bottom: 18px;">
            <h3>{{ $module['name'] }} <span class="badge {{ $module['scope'] === 'High' ? 'badge-high' : ($module['scope'] === 'Medium' ? 'badge-medium' : 'badge-low') }}">{{ $module['scope'] }}</span></h3>
            <ul>
                @foreach($module['changes'] as $change)
                <li>{{ $change }}</li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </section>

    <section id="tahapan">
        <h2>5. Tahapan Implementasi</h2>
        @foreach($phases as $phase)
        <div class="phase">
            <div class="phase-title">{{ $phase['phase'] }}</div>
            <ul>
                @foreach($phase['items'] as $item)
                <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </section>

    <section id="risiko">
        <h2>6. Risiko & Mitigasi</h2>
        @foreach($risks as $risk)
        <div class="risk">{{ $risk }}</div>
        @endforeach
    </section>

    <div class="footer">
        MitraPOS — Rencana Major Update Role & Akses — Generated {{ $generated_at }}
    </div>
</body>
</html>