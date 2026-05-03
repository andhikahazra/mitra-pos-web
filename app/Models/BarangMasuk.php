<?php

namespace App\Models;

use App\Models\DetailBarangMasuk;
use App\Models\LogStok;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'tanggal_pesan',
        'tanggal_terima',
        'supplier_id',
        'user_id',
        'status',
        'disetujui_oleh',
        'foto_struk',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesan' => 'datetime',
            'tanggal_terima' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailBarangMasuk::class, 'barang_masuk_id');
    }

    public function logStok(): HasMany
    {
        return $this->hasMany(LogStok::class, 'barang_masuk_id');
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'Disetujui';
    }
}
