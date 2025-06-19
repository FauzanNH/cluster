<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPenduduk extends Model
{
    protected $table = 'datawarga';
    protected $primaryKey = 'warga_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'warga_id',
        'nama',
        'nik',
        'no_kk',
        'domisili_ktp',
        'tanggal_lahir',
        'gender',
        'agama',
        'status_pernikahan',
        'pekerjaan',
        'pendidikan_terakhir',
        'foto_ktp',
        'foto_kk',
        'blok_rt',
    ];
}
