<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RopService;

class CalculateRopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rop:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kalkulasi ulang nilai Reorder Point (ROP) dan Safety Stock untuk semua produk';

    /**
     * Execute the console command.
     */
    public function handle(RopService $ropService)
    {
        $this->info('Memulai kalkulasi ROP dan Safety Stock...');
        
        $ropService->calculateAll();
        $this->info('Kalkulasi ROP berhasil diselesaikan dan disimpan ke database.');
        return 0;
    }
}
