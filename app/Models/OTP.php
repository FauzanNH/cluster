<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'otp';
    protected $fillable = [
        'users_id', 'no_hp', 'otp_code', 'is_used', 'expired_at'
    ];
    protected $casts = [
        'is_used' => 'boolean',
        'expired_at' => 'datetime',
        'otp_code' => 'string',
    ];
}
