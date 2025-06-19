<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    protected $table = 'aktivitas';
    protected $primaryKey = 'aktivitas_id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'aktivitas_id',
        'users_id',
        'tamu_id',
        'judul',
        'sub_judul',
    ];
}
