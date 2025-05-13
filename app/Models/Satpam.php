<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satpam extends Model
{
    protected $table = 'datasatpam';
    protected $primaryKey = 'users_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'users_id', 'nik', 'tanggal_lahir', 'no_kep', 'seksi_unit_gerbang'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }
}
