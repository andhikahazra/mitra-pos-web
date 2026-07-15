<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'nama_toko',
        'alamat_toko',
        'no_hp',
        'no_hp_rop_notif',
        'deskripsi',
        'biaya_admin_qris',
        'rekening_bank',
        'footer_nota',
    ];

    protected $casts = [
        'alamat_toko' => 'array',
        'deskripsi' => 'array',
        'rekening_bank' => 'array',
        'biaya_admin_qris' => 'decimal:2',
    ];
}
