<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$t = App\Models\Transaksi::with(['detail_transaksi.produk'])->where('kode', 'TKMP-001-001')->first();
$f = app(App\Services\FonnteService::class);
$result = $f->sendReceipt('085647365722', $t);
echo $result ? "BERHASIL\n" : "GAGAL\n";
