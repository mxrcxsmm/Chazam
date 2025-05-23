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
        'tipocomunidad',
        'codigo',
        'descripcion',
        'id_reto',
        'creator',
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
     * Relación con los usuarios del chat
     */
    public function chatUsuarios()
    {
        return $this->hasMany(ChatUsuario::class, 'id_chat', 'id_chat');
    }

    /**
     * Relación con los mensajes del chat
     */
    public function mensajes()
    {
        return $this->hasManyThrough(Mensaje::class, ChatUsuario::class, 'id_chat', 'id_chat_usuario', 'id_chat', 'id_chat_usuario');
    }

    /**
     * Get the creator of the chat.
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creator', 'id_usuario');
    }

    /**
     * Genera un código único para comunidades privadas
     * @param int $length Longitud inicial del código
     * @return string Código único generado
     */
    public static function generarCodigoUnico($length = 10)
    {
        do {
            $codigo = '';
            $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            
            for ($i = 0; $i < $length; $i++) {
                $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
            
            // Verificar si el código ya existe
            $existe = self::where('codigo', $codigo)->exists();
            
            // Si no existe, retornar el código
            if (!$existe) {
                return $codigo;
            }
            
            // Si existe y hemos intentado todas las combinaciones posibles
            // (36^length intentos), aumentar la longitud
            if (self::where('codigo', 'like', str_repeat('_', $length))->count() >= pow(36, $length)) {
                $length++;
            }
            
        } while (true);
    }
}