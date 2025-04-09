<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatUsuario extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chat_usuario';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_chat_usuario';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_chat',
        'id_usuario'
    ];

    /**
     * Get the chat associated with this record.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'id_chat');
    }

    /**
     * Get the user associated with this record.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Scope a query to only include records for a specific chat.
     */
    public function scopeForChat($query, $chatId)
    {
        return $query->where('id_chat', $chatId);
    }

    /**
     * Buscador
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('id_usuario', $userId);
    }
}