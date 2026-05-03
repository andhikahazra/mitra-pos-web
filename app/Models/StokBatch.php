<?php

namespace App\Models;

use App\Models\DetailBarangMasuk;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokBatch extends Model
{
    use HasFactory;

    protected $table = 'stok_batch';

    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'detail_barang_masuk_id',
        'qty_sisa',
        'harga_beli',
        'tanggal_masuk',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'tanggal_masuk' => 'date',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function detailBarangMasuk(): BelongsTo
    {
        return $this->belongsTo(DetailBarangMasuk::class, 'detail_barang_masuk_id');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'batch_id');
    }
}
