<?php

namespace App\Models;

use App\Models\BarangMasuk;
use App\Models\Produk;
use App\Models\StokBatch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailBarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'detail_barang_masuk';

    public $timestamps = false;

    protected $fillable = [
        'barang_masuk_id',
        'produk_id',
        'jumlah',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function stokBatch(): HasMany
    {
        return $this->hasMany(StokBatch::class, 'detail_barang_masuk_id');
    }
}
