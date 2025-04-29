<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'username',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'genero',
        'email',
        'password',
        'puntos',
        'id_nacionalidad',
        'id_rol',
        'id_estado',
        'img',
        'descripcion',
        'inicio_ban',
        'fin_ban',
        'ultimo_login',
        'racha'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
        'puntos' => 'integer',
        'inicio_ban' => 'datetime',
        'fin_ban' => 'datetime',
        'ultimo_login' => 'datetime',
    ];

    /* RELACIONES */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function nacionalidad(): BelongsTo
    {
        return $this->belongsTo(Nacionalidad::class, 'id_nacionalidad');
    }

    // Relación con los chats del usuario
    public function chatUsuarios()
    {
        return $this->hasMany(\App\Models\ChatUsuario::class, 'id_usuario');
    }

    /* Extras */
    // nombre completo 
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // recuperar foto
    public function getImagenPerfilAttribute(): string
    {
        return $this->img ? asset('img/profile_img/'.$this->img) : asset('img/profile_img/avatar-default.png');
    }

    // edad en años
    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento->age;
    }

    /* MÉTODOS */
    public function estaBaneado(): bool
    {
        return $this->fin_ban && now()->lt($this->fin_ban);
    }

    // sirve para admin
    public function esAdministrador(): bool
    {
        return optional($this->rol)->nom_rol === 'Administrador';
    }

    // sumador de puntos
    public function agregarPuntos(int $cantidad): void
    {
        $this->increment('puntos', $cantidad);
    }

    // restador de puntos
    public function gastarPuntos(int $cantidad): void
    {
        $this->decrement('puntos', $cantidad);
    }

    // quien esta banned
    public function scopeBaneados($query)
    {
        return $query->whereNotNull('fin_ban')
                    ->where('fin_ban', '>', now());
    }
}