<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prods = App\Models\Produk::with('rop')->get();
foreach($prods as $p) {
    $s = $p->stok;
    $r = $p->rop->reorder_point ?? 0;
    $ss = $p->rop->safety_stock ?? 0;
    $st = 'aman';
    if ($s <= $r) $st = 'restock';
    elseif ($s <= $r + ($ss * 0.5)) $st = 'hampir';
    echo $p->nama . ' | S:' . $s . ' R:' . $r . ' SS:' . $ss . ' => ' . $st . PHP_EOL;
}
