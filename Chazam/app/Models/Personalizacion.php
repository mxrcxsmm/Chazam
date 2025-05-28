<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personalizacion extends Model
{
    use HasFactory;

    protected $table = 'personalizacion';
    protected $primaryKey = 'id_personalizacion';

    protected $fillable = [
        'id_usuario',
        'marco',
        'rotacion',
        'brillo',
        'sidebar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    // Accesor para 'marco'
    public function getMarcoAttribute($value)
    {
        return $value ?? 'default.svg';
    }

    // Accesor para 'brillo' (deja null si no hay valor)
    public function getBrilloAttribute($value)
    {
        return $value;
    }
}