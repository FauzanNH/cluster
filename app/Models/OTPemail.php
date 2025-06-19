<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPemail extends Model
{
    protected $table = 'otp_email';
    protected $fillable = [
        'users_id', 'email', 'otp_code', 'is_used', 'expired_at'
    ];
    protected $casts = [
        'is_used' => 'boolean',
        'expired_at' => 'datetime',
    ];
} 