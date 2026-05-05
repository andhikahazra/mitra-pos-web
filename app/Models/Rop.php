<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rop extends Model
{
    use HasFactory;

    protected $table = 'rop';

    public $timestamps = true;
    const CREATED_AT = null;
    const UPDATED_AT = 'waktu_penghitungan';

    protected $fillable = [
        'produk_id',
        'rata_penjualan',
        'standar_deviasi',
        'lead_time',
        'safety_stock',
        'reorder_point',
        'periode',
        'waktu_penghitungan',
    ];

    protected function casts(): array
    {
        return [
            'rata_penjualan' => 'decimal:2',
            'standar_deviasi' => 'decimal:2',
            'lead_time' => 'decimal:2',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
