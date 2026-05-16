<?php

namespace App\Models;

use App\Models\DetailTransaksi;
use App\Models\LogStok;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'tanggal',
        'user_id',
        'nama_pelanggan',
        'no_hp_pelanggan',
        'catatan',
        'total_harga',
        'biaya_admin',
        'metode_pembayaran',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
            'total_harga' => 'decimal:2',
            'biaya_admin' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detail_transaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function logStok(): HasMany
    {
        return $this->hasMany(LogStok::class, 'transaksi_id');
    }
}
