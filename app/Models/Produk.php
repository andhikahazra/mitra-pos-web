<?php

namespace App\Models;

use App\Models\DetailBarangMasuk;
use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Rop;
use App\Models\StokBatch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'sku',
        'kategori_id',
        'harga',
        'stok',
        'tipe_produk',
        'status',
        'panjang',
        'lebar',
        'tinggi',
        'volume',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'status' => 'boolean',
            'panjang' => 'decimal:2',
            'lebar' => 'decimal:2',
            'tinggi' => 'decimal:2',
            'volume' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }


    public function detailBarangMasuk(): HasMany
    {
        return $this->hasMany(DetailBarangMasuk::class, 'produk_id');
    }

    public function stokBatch(): HasMany
    {
        return $this->hasMany(StokBatch::class, 'produk_id');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'produk_id');
    }


    public function rop(): HasOne
    {
        return $this->hasOne(Rop::class, 'produk_id');
    }
}
