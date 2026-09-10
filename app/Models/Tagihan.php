<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'kategori_tagihan_id',
        'nominal',
        'jatuh_tempo',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'jatuh_tempo' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'tahun_ajaran_id'
        );
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            KategoriTagihan::class,
            'kategori_tagihan_id'
        );
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(
            Pembayaran::class,
            'tagihan_id'
        );
    }
}