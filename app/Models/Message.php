<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'message_id',
        'chat_id',
        'sender_id',
        'message',
        'image_path',
        'document_path',
        'document_name',
        'document_type',
        'document_size',
        'reply_to',
        'is_read',
    ];
    
    // Gunakan id sebagai primary key
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Mendapatkan chat yang berisi pesan ini
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Mendapatkan pengirim pesan
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'users_id');
    }

    /**
     * Mendapatkan pesan yang dibalas
     */
    public function replyMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    /**
     * Cek apakah pesan memiliki gambar
     */
    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }

    /**
     * Cek apakah pesan memiliki dokumen
     */
    public function hasDocument(): bool
    {
        return !empty($this->document_path);
    }

    /**
     * Cek apakah pesan adalah balasan
     */
    public function isReply(): bool
    {
        return !empty($this->reply_to);
    }

    /**
     * Mendapatkan URL gambar
     */
    public function getImageUrl()
    {
        if ($this->hasImage()) {
            return asset($this->image_path);
        }
        return null;
    }

    /**
     * Mendapatkan URL dokumen
     */
    public function getDocumentUrl()
    {
        if ($this->hasDocument()) {
            return asset($this->document_path);
        }
        return null;
    }
}
