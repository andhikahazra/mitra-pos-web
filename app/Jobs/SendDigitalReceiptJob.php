<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaksi;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;

class SendDigitalReceiptJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $transaksi;
    protected $target;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaksi $transaksi, string $target)
    {
        $this->transaksi = $transaksi;
        $this->target = $target;
    }

    /**
     * Execute the job.
     */
    public function handle(FonnteService $fonnteService): void
    {
        Log::info("Memulai pengiriman nota digital ke {$this->target} untuk transaksi {$this->transaksi->kode}");
        
        $success = $fonnteService->sendReceipt($this->target, $this->transaksi);
        
        if (!$success) {
            Log::error("Gagal mengirim nota digital ke {$this->target} untuk transaksi {$this->transaksi->kode}");
        } else {
            Log::info("Nota digital berhasil dikirim ke {$this->target} untuk transaksi {$this->transaksi->kode}");
        }
    }
}
