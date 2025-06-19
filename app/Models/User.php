<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'users_id',
        'nama',
        'email',
        'no_hp',
        'password',
        'gender',
        'role',
        'alamat',
        'rt_blok',
        'is_online',
        'last_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'last_active' => 'datetime',
        ];
    }

    protected $primaryKey = 'users_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Update status user menjadi online
     */
    public function setOnline()
    {
        $this->is_online = true;
        $this->last_active = now();
        $this->save();
    }

    /**
     * Update status user menjadi offline
     */
    public function setOffline()
    {
        $this->is_online = false;
        $this->last_active = now();
        $this->save();
    }

    /**
     * Cek apakah user sedang online (baik secara langsung atau berdasarkan last active)
     * 
     * @param int $thresholdMinutes Batas waktu dalam menit untuk menganggap user masih online
     * @return bool
     */
    public function isOnline($thresholdMinutes = 2)
    {
        if (!$this->last_active) {
            return false;
        }
        
        // Jika status online, cek waktu last active
        // Jika last active > threshold menit yang lalu, anggap offline
        $threshold = now()->subMinutes($thresholdMinutes);
        return $this->is_online && $this->last_active >= $threshold;
    }

    /**
     * Mendapatkan status user yang ramah untuk tampilan
     */
    public function getOnlineStatusText()
    {
        if ($this->isOnline()) {
            return 'Online';
        }
        
        if ($this->last_active) {
            $timeDiff = now()->diffInMinutes($this->last_active);
            
            if ($timeDiff < 1) {
                return "Baru saja online";
            } else if ($timeDiff < 60) {
                return "Terakhir online {$timeDiff} menit yang lalu";
            } else if ($timeDiff < 1440) { // 24 jam
                $hours = floor($timeDiff / 60);
                $minutes = $timeDiff % 60;
                if ($minutes > 0) {
                    return "Terakhir online {$hours} jam {$minutes} menit yang lalu";
                }
                return "Terakhir online {$hours} jam yang lalu";
            } else if ($timeDiff < 10080) { // 7 hari
                $days = floor($timeDiff / 1440);
                return "Terakhir online {$days} hari yang lalu";
            } else {
                // Format tanggal jika lebih dari 7 hari
                return "Terakhir online " . $this->last_active->format('d M Y') . " pukul " . $this->last_active->format('H:i');
            }
        }
        
        return 'Offline';
    }

    /**
     * Mendapatkan waktu terakhir aktif dalam format yang lebih detail
     */
    public function getLastActiveTime()
    {
        if (!$this->last_active) {
            return 'Belum pernah aktif';
        }
        
        return $this->last_active->format('d M Y H:i:s');
    }
}
