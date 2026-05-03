<?php

namespace App\Models;

use App\Models\BarangMasuk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'no_telp',
        'alamat',
    ];

    public function barangMasuk(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'supplier_id');
    }
}
