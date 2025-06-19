<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tamu extends Model
{
    use HasFactory;

    protected $table = 'tamu';
    protected $primaryKey = 'tamu_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'tamu_id', 'no_hp', 'email',
    ];

    public function detailTamu()
    {
        return $this->hasOne(DetailTamu::class, 'tamu_id', 'tamu_id');
    }
}
