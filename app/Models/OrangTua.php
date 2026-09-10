<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    protected $table = 'orang_tua';

    protected $fillable = [
        'user_id',
        'nama',
        'no_hp',
        'alamat',
    ];


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    public function siswa()
    {
        return $this->hasMany(
            Siswa::class,
            'orang_tua_id'
        );
    }
}