<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPengajuan extends Model
{   
    protected $table = 'suratpengajuan';
    protected $fillable = [
        'surat_id',
        'warga_id',
        'rumah_id',
        'jenis_surat',
        'status_penegerjaan',
        'foto_ktp',
        'kartu_keluarga',
        'dokumen_lainnya1',
        'dokumen_lainnya2',
        'keperluan_keramaian',
        'tempat_keramaian',
        'tanggal_keramaian',
        'jam_keramaian',
    ];
}
