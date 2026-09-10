<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranTagihan extends Model
{
    protected $table = 'pembayaran_tagihan';

    protected $fillable = [
        'tagihan_id',
        'user_id',
        'nominal',
        'metode',
        'bukti_pembayaran',
        'status',
        'catatan',
        'tanggal_kirim',
        'tanggal_disetujui',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_kirim' => 'datetime',
        'tanggal_disetujui' => 'datetime',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(
            Tagihan::class,
            'tagihan_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}