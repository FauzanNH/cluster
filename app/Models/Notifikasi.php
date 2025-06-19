<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = true;
    protected $fillable = [
        'users_id',
        'judul',
        'sub_judul',
        'halaman',
    ];
}
