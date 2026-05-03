<?php

namespace App\Models;

use App\Models\BarangMasuk;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogStok extends Model
{
    use HasFactory;

    protected $table = 'log_stok';

    public $timestamps = false;

    protected $fillable = [
        'produk_id',
        'tipe',
        'jumlah',
        'keterangan',
        'transaksi_id',
        'barang_masuk_id',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    public function getTanggalAttribute()
    {
        if ($this->transaksi) {
            return $this->transaksi->tanggal;
        }

        if ($this->barangMasuk) {
            return $this->barangMasuk->tanggal_terima;
        }

        return null;
    }
}
