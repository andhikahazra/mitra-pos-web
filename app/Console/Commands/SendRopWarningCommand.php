<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produk;
use App\Models\Rop;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Log;

class SendRopWarningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fonnte:send-rop-warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi peringatan ROP via WhatsApp menggunakan Fonnte';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnteService)
    {
        $this->info('Memeriksa stok kritis...');

        $criticalProducts = Produk::join('rop', 'produk.id', '=', 'rop.produk_id')
            ->whereColumn('produk.stok', '<=', 'rop.reorder_point')
            ->where('produk.status', true)
            ->where('produk.tipe_produk', 'stock')
            ->select('produk.nama', 'produk.stok', 'rop.reorder_point as rop')
            ->get();

        if ($criticalProducts->isEmpty()) {
            $this->info('Tidak ada stok kritis.');
            return 0;
        }

        $criticalItemsArray = $criticalProducts->toArray();
        
        $target = env('FONNTE_OWNER_TARGET');
        if (empty($target)) {
            $this->error('FONNTE_OWNER_TARGET belum diatur di .env');
            Log::error('Gagal mengirim ROP Warning: FONNTE_OWNER_TARGET kosong');
            return 1;
        }

        $this->info('Mengirim notifikasi ROP ke ' . $target);
        
        $success = $fonnteService->sendRopWarning($target, $criticalItemsArray);

        if ($success) {
            $this->info('Pesan berhasil dikirim.');
            return 0;
        } else {
            $this->error('Gagal mengirim pesan.');
            return 1;
        }
    }
}
