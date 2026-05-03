<?php

namespace App\Models;

use App\Models\Produk;
use App\Models\StokBatch;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';

    public $timestamps = false;

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'batch_id',
        'jumlah',
        'harga',
        'harga_modal',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'harga_modal' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(StokBatch::class, 'batch_id');
    }
}
