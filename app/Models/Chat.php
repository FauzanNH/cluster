<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'chat_id',
        'user1_id',
        'user2_id',
        'last_activity',
        'is_active',
    ];
    
    // Gunakan chat_id sebagai UUID
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
    ];

    /**
     * Mendapatkan user pertama dari chat
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id', 'users_id');
    }

    /**
     * Mendapatkan user kedua dari chat
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id', 'users_id');
    }

    /**
     * Mendapatkan semua pesan dalam chat
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Mendapatkan pesan terakhir dalam chat
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Mendapatkan jumlah pesan yang belum dibaca oleh user tertentu
     */
    public function unreadCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mendapatkan user lain dalam chat (bukan user yang sedang login)
     */
    public function getOtherUser($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        }
        return $this->user1;
    }

    /**
     * Mengecek apakah user adalah bagian dari chat ini
     */
    public function hasUser($userId): bool
    {
        return $this->user1_id == $userId || $this->user2_id == $userId;
    }
}
