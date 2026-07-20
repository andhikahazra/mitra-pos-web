<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    public function majorUpdate(): Response
    {
        $data = $this->getPlanData();

        $pdf = Pdf::loadView('plan.major-update', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download('Rencana-Major-Update-MitraPOS.pdf');
    }

    private function getPlanData(): array
    {
        return [
            'generated_at' => now()->format('d F Y H:i'),
            'current_roles' => [
                'Pemilik' => 'Akses penuh ke dashboard web (manajemen, analitik, approval). Satu-satunya role yang bisa login web.',
                'Karyawan' => 'Hanya akses API/mobile (POS, input barang masuk). Diblokir masuk dashboard web.',
            ],
            'proposed_roles' => [
                'Pemilik' => 'Super admin. Akses penuh ke seluruh fitur web + manajemen user + settings.',
                'Kasir' => 'Operator kasir di web. Bisa transaksi (POS), lihat katalog produk, input barang masuk, lihat riwayat transaksi sendiri.',
                'Karyawan/Gudang' => 'Fokus stok & barang masuk. Input barang masuk, monitoring batch, log stok. Tanpa akses transaksi POS.',
            ],
            'modules' => [
                [
                    'name' => 'Sistem Role & Permission',
                    'scope' => 'High',
                    'changes' => [
                        'Tambah ROLE_KASIR = "Kasir" di app/Models/User.php',
                        'Tambah method isKasir() di User model',
                        'Pisah middleware pemilik menjadi role-based: EnsureRole (menerima array role)',
                        'Update validation UserController untuk menerima role Kasir',
                        'Update dropdown role di users/_form.blade.php',
                        'Update DatabaseSeeder & PosSeeder untuk akun contoh Kasir',
                    ],
                ],
                [
                    'name' => 'Autentikasi Web (AuthController)',
                    'scope' => 'Medium',
                    'changes' => [
                        'Izinkan role Pemilik DAN Kasir login ke web dashboard',
                        'Pembatasan akses fitur sensitif tetap di level route/middleware per-role',
                        'Topbar menampilkan nama + role yang sedang login',
                    ],
                ],
                [
                    'name' => 'POS / Transaksi Kasir (Web)',
                    'scope' => 'High',
                    'changes' => [
                        'Buat modul POS web untuk Kasir (saat ini POS hanya ada di API mobile)',
                        'Reuse logic batch deduction (FIFO/termurah) dari Api\\TransactionController',
                        'Form transaksi: pilih produk, jumlah, metode bayar (Tunai/QRIS/Transfer/Piutang)',
                        'Cetak nota + kirim receipt WhatsApp (Fonnte) seperti di API',
                        'Riwayat transaksi Kasir difilter hanya miliknya sendiri',
                    ],
                ],
                [
                    'name' => 'Sidebar & Navigasi',
                    'scope' => 'Medium',
                    'changes' => [
                        'Sidebar menampilkan menu berdasarkan role (Kasir tidak lihat Settings/User/ROP/Laporan)',
                        'Gunakan @if auth()->user()->isKasir() untuk conditional menu',
                        'Aktifkan menu POS untuk Kasir',
                    ],
                ],
                [
                    'name' => 'Manajemen Produk & Supplier',
                    'scope' => 'Low',
                    'changes' => [
                        'Kasir: read-only (lihat katalog + harga) — sama seperti API saat ini',
                        'Pemilik: full CRUD tetap',
                    ],
                ],
                [
                    'name' => 'Barang Masuk',
                    'scope' => 'Medium',
                    'changes' => [
                        'Kasir/Karyawan: bisa input barang masuk (status Menunggu)',
                        'Approval tetap hanya Pemilik (updateStatus)',
                    ],
                ],
                [
                    'name' => 'Laporan, ROP & Settings',
                    'scope' => 'Low',
                    'changes' => [
                        'Pemilik only: ROP, Laporan Keuangan, Settings, Manajemen User',
                        'Kasir diarahkan ke dashboard kasir setelah login',
                    ],
                ],
            ],
            'phases' => [
                [
                    'phase' => 'Fase 1 — Fondasi Role',
                    'items' => [
                        'Tambah ROLE_KASIR + isKasir() di User model',
                        'Refactor middleware EnsureIsPemilik → EnsureRole (multi-role)',
                        'Update AuthController web login untuk terima Kasir',
                        'Update UserController validation + form + seeder',
                    ],
                ],
                [
                    'phase' => 'Fase 2 — Akses Web Kasir',
                    'items' => [
                        'Sidebar conditional per role',
                        'Route group khusus kasir (POS + riwayat)',
                        'Dashboard kasir (ringkas)',
                    ],
                ],
                [
                    'phase' => 'Fase 3 — Modul POS Web',
                    'items' => [
                        'Reuse logic transaksi API ke web',
                        'UI transaksi, pembayaran, nota, receipt WhatsApp',
                        'Filter riwayat per kasir',
                    ],
                ],
                [
                    'phase' => 'Fase 4 — Polishing & Testing',
                    'items' => [
                        'Uji akses tiap role (Pemilik/Kasir/Karyawan)',
                        'Pastikan Kasir tidak bisa buka Settings/User/ROP',
                        'Dokumentasi user manual',
                    ],
                ],
            ],
            'risks' => [
                'Logika FIFO batch harus konsisten antara API & web POS — reuse service yang sama',
                'Pastikan middleware baru tidak membuka celah akses sensitif bagi Kasir',
                'Seeder & user existing (Karyawan) tidak broken setelah refactor role',
            ],
        ];
    }
}
