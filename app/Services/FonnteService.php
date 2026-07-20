<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;
use Carbon\Carbon;

class FonnteService
{
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->apiUrl = 'https://api.fonnte.com/send';
    }

    /**
     * Send a standard text message via Fonnte.
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (empty($this->token)) {
            Log::error('Fonnte Token is not set in .env');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['status']) && $responseData['status'] == true) {
                return true;
            }

            Log::error('Fonnte API Error', ['response' => $responseData]);
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte HTTP Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send ROP early warning.
     */
    public function sendRopWarning(string $target, array $criticalItems): bool
    {
        if (empty($criticalItems)) {
            return true;
        }

        $date = Carbon::now()->locale('id')->isoFormat('D MMMM YYYY');
        $message = "*PERINGATAN STOK KRITIS*\n\n";
        $message .= "Tanggal: $date\n\n";
        $message .= "Berikut adalah daftar barang yang sudah mencapai atau di bawah batas minimum (Reorder Point):\n\n";

        foreach ($criticalItems as $index => $item) {
            $message .= ($index + 1) . ". {$item['nama']} (Sisa: {$item['stok']}, ROP: {$item['rop']})\n";
        }

        $message .= "\nHarap segera melakukan pengadaan ulang untuk mencegah kehabisan stok.";

        return $this->sendMessage($target, $message);
    }

    /**
     * Send Daily Report.
     */
    public function sendDailyReport(string $target, array $reportData): bool
    {
        $date = Carbon::now()->locale('id')->isoFormat('D MMMM YYYY');
        
        $message = "*LAPORAN HARIAN TOKO*\n\n";
        $message .= "Tanggal: {$date}\n\n";
        $message .= "Total Omzet: Rp " . number_format($reportData['omzet'], 0, ',', '.') . "\n";
        $message .= "Total Transaksi: {$reportData['total_transaksi']}\n";
        $message .= "Barang Terjual: {$reportData['total_barang']} item\n\n";
        
        if (!empty($reportData['transaksi_terbesar'])) {
            $message .= "Transaksi Terbesar: Rp " . number_format($reportData['transaksi_terbesar'], 0, ',', '.') . "\n\n";
        }

        $message .= "Terima kasih, semoga sukses selalu!";

        return $this->sendMessage($target, $message);
    }

    /**
     * Send Digital Receipt.
     */
    public function sendReceipt(string $target, Transaksi $transaksi): bool
    {
        $setting = \App\Models\Setting::first();

        $grandTotal = (float) $transaksi->total_harga + (float) $transaksi->biaya_admin;
        $date = Carbon::parse($transaksi->tanggal)->locale('id')->isoFormat('D MMMM YYYY');

        $message = "*NOTA DIGITAL - {$transaksi->kode}*\n\n";
        $message .= "Tanggal: {$date}\n";
        $message .= "Total: Rp " . number_format($grandTotal, 0, ',', '.') . "\n\n";
        $message .= url('/nota/' . $transaksi->kode);

        return $this->sendMessage($target, $message);
    }
}