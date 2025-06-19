<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kunjungan extends Model
{
    use HasFactory;

    protected $table = 'kunjungan';
    protected $fillable = [
        'kunjungan_id', 'tamu_id', 'rumah_id', 'tujuan_kunjungan', 'status_kunjungan', 'waktu_masuk', 'waktu_keluar'
    ];

    public function tamu()
    {
        return $this->belongsTo(Tamu::class, 'tamu_id', 'tamu_id');
    }

    public function rumah()
    {
        return $this->belongsTo(DataRumah::class, 'rumah_id', 'rumah_id');
    }
} 