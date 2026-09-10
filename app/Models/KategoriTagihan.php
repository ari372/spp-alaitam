<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriTagihan extends Model
{
    protected $table = 'kategori_tagihan';

    protected $fillable = [
        'nama',
        'nominal',
        'keterangan',
    ];

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'kategori_tagihan_id');
    }
}