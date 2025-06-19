<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTamu extends Model
{
    use HasFactory;

    protected $table = 'detail_tamu';
    protected $fillable = [
        'tamu_id', 'nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'kewarganegaraan', 'alamat', 'rt', 'rw', 'kel_desa', 'kecamatan', 'kabupaten', 'agama',
    ];

    public function tamu()
    {
        return $this->belongsTo(Tamu::class, 'tamu_id', 'tamu_id');
    }
} 