<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdukDimensi extends Model
{
    use HasFactory;

    protected $table = 'produk_dimensi';

    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'panjang',
        'lebar',
        'tinggi',
        'volume',
    ];

    protected function casts(): array
    {
        return [
            'panjang' => 'decimal:2',
            'lebar' => 'decimal:2',
            'tinggi' => 'decimal:2',
            'volume' => 'decimal:2',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
