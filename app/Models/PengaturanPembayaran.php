<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPembayaran extends Model
{
    protected $table = 'pengaturan_pembayaran';

    protected $fillable = [
        'tanggal_jatuh_tempo',
        'hari_pengingat',
        'aktif',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'integer',
        'hari_pengingat' => 'integer',
        'aktif' => 'boolean',
    ];
}