<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Mensaje extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mensajes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_mensaje';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_chat_usuario',
        'contenido',
        'fecha_envio'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_envio' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the chat user relationship.
     */
    public function chatUsuario(): BelongsTo
    {
        return $this->belongsTo(ChatUsuario::class, 'id_chat_usuario');
    }

    /**
     * Get the user who sent the message through chatUsuario.
     */
    public function mensajero(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            ChatUsuario::class,
            'id_chat_usuario', // Foreign key on chat_usuario table
            'id_usuario',      // Foreign key on users table
            'id_chat_usuario', // Local key on mensajes table
            'id_usuario'       // Local key on chat_usuario table
        );
    }

    /**
     * Get the chat through chatUsuario.
     */
    public function chat(): HasOneThrough
    {
        return $this->hasOneThrough(
            Chat::class,
            ChatUsuario::class,
            'id_chat_usuario', // Foreign key on chat_usuario table
            'id_chat',         // Foreign key on chats table
            'id_chat_usuario', // Local key on mensajes table
            'id_chat'          // Local key on chat_usuario table
        );
    }

    /**
     * Get the formatted send time.
     *
     * @return string
     */
    public function getDataEnvio()
    {
        return $this->fecha_envio->format('d/m/Y H:i');
    }

    /**
     * Check if the message was sent today.
     *
     * @return bool
     */
    public function EsHoy()
    {
        return $this->fecha_envio->isToday();
    }

    /**
     * Buscar chat specifico
     */
    public function scopeFromChat($query, $chatId)
    {
        return $query->whereHas('chatUsuario', function($q) use ($chatId) {
            $q->where('id_chat', $chatId);
        });
    }

    /**
     * Buscar user specifico
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->whereHas('chatUsuario', function($q) use ($userId) {
            $q->where('id_usuario', $userId);
        });
    }
}