<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim laporan rekapitulasi harian via WhatsApp menggunakan Fonnte';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnteService)
    {
        $this->info('Menyiapkan laporan harian...');

        $today = Carbon::today();
        
        $transaksis = Transaksi::with('detail_transaksi')
            ->whereDate('tanggal', $today)
            ->where('status', 'Selesai')
            ->get();

        if ($transaksis->isEmpty()) {
            $this->info('Tidak ada transaksi hari ini.');
            $reportData = [
                'omzet' => 0,
                'total_transaksi' => 0,
                'total_barang' => 0,
                'transaksi_terbesar' => 0,
            ];
        } else {
            $totalOmzet = $transaksis->sum('total_harga') + $transaksis->sum('biaya_admin');
            $totalTransaksi = $transaksis->count();
            
            $totalBarang = 0;
            $transaksiTerbesar = 0;

            foreach ($transaksis as $trx) {
                $totalBarang += $trx->detail_transaksi->sum('jumlah');
                $totalTrx = $trx->total_harga + $trx->biaya_admin;
                if ($totalTrx > $transaksiTerbesar) {
                    $transaksiTerbesar = $totalTrx;
                }
            }

            $reportData = [
                'omzet' => $totalOmzet,
                'total_transaksi' => $totalTransaksi,
                'total_barang' => $totalBarang,
                'transaksi_terbesar' => $transaksiTerbesar,
            ];
        }

        $target = env('FONNTE_OWNER_TARGET');
        if (empty($target)) {
            $this->error('FONNTE_OWNER_TARGET belum diatur di .env');
            Log::error('Gagal mengirim Daily Report: FONNTE_OWNER_TARGET kosong');
            return 1;
        }

        $this->info('Mengirim laporan harian ke ' . $target);
        
        $success = $fonnteService->sendDailyReport($target, $reportData);

        if ($success) {
            $this->info('Laporan berhasil dikirim.');
            return 0;
        } else {
            $this->error('Gagal mengirim laporan.');
            return 1;
        }
    }
}
