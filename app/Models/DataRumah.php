<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRumah extends Model
{
    protected $table = 'datarumah';
    
    protected $fillable = [
        'rumah_id',
        'no_kk',
        'users_id',
        'warga_id1',
        'warga_id2',
        'warga_id3',
        'warga_id4',
        'warga_id5',
        'blok_rt',
        'status_kepemilikan',
        'alamat_cluster',
    ];

    protected $primaryKey = 'rumah_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function kepalaKeluarga()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }

    public function anggota1()
    {
        return $this->belongsTo(DataPenduduk::class, 'warga_id1', 'warga_id');
    }

    public function anggota2()
    {
        return $this->belongsTo(DataPenduduk::class, 'warga_id2', 'warga_id');
    }

    public function anggota3()
    {
        return $this->belongsTo(DataPenduduk::class, 'warga_id3', 'warga_id');
    }

    public function anggota4()
    {
        return $this->belongsTo(DataPenduduk::class, 'warga_id4', 'warga_id');
    }

    public function anggota5()
    {
        return $this->belongsTo(DataPenduduk::class, 'warga_id5', 'warga_id');
    }
}
