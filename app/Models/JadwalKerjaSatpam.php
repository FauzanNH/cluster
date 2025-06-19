<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKerjaSatpam extends Model
{
    protected $table = 'jadwal_kerja_satpam';
    
    protected $fillable = [
        'users_id',
        'tanggal',
        'shift',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'lokasi_detail',
        'catatan',
        'is_active'
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
        'is_active' => 'boolean'
    ];
    
    /**
     * Get the user (satpam) that owns this schedule
     */
    public function satpam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }
    
    /**
     * Get the shift time range based on shift type
     */
    public static function getShiftTime(string $shift): array
    {
        switch($shift) {
            case 'pagi':
                return ['jam_mulai' => '06:00:00', 'jam_selesai' => '14:00:00'];
            case 'siang':
                return ['jam_mulai' => '14:00:00', 'jam_selesai' => '22:00:00'];
            case 'malam':
                return ['jam_mulai' => '22:00:00', 'jam_selesai' => '06:00:00'];
            case 'libur':
                return ['jam_mulai' => '00:00:00', 'jam_selesai' => '00:00:00'];
            default:
                return ['jam_mulai' => '00:00:00', 'jam_selesai' => '00:00:00'];
        }
    }
    
    /**
     * Get the shift label
     */
    public static function getShiftLabel(string $shift): string
    {
        switch($shift) {
            case 'pagi':
                return 'Pagi';
            case 'siang':
                return 'Siang';
            case 'malam':
                return 'Malam';
            case 'libur':
                return 'Libur';
            default:
                return '';
        }
    }
}
