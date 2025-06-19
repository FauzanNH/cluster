<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';
    protected $primaryKey = 'pengaduan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'pengaduan_id',
        'users_id',
        'jenis_pengaduan',
        'detail_pengaduan',
        'lokasi',
        'status_pengaduan',
        'dokumen1',
        'dokumen2',
        'remark',
        'blok_rt',
    ];
}
