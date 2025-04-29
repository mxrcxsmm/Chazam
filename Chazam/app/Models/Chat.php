<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chats';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_chat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fecha_creacion',
        'img',
        'nombre',
        'descripcion',
        'id_reto',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the reto associated with this chat.
     */
    public function reto(): BelongsTo
    {
        return $this->belongsTo(Reto::class, 'id_reto', 'id_reto');
    }

    /**
     * Get the chat usuarios associated with this chat.
     */
    public function chatUsuarios()
    {
        return $this->hasMany(ChatUsuario::class, 'id_chat', 'id_chat');
    }
}