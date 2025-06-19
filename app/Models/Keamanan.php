<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keamanan extends Model
{
    protected $table = 'keamanan';
    protected $primaryKey = 'users_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'users_id', 'pin', 'hint', 'pin_active', 'login_pin_active'
    ];
} 