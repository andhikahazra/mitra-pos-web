<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Jadwalkan kalkulasi ROP setiap tengah malam jam 00:00
Schedule::command('rop:calculate')->dailyAt('00:00');

// Jadwalkan pengiriman peringatan ROP via WA jam 00:30
Schedule::command('fonnte:send-rop-warning')->dailyAt('00:30');

// Jadwalkan pengiriman laporan harian via WA jam 01:00
Schedule::command('fonnte:send-daily-report')->dailyAt('01:00');
