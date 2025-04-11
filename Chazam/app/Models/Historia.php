<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historia extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'historias';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_historia';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'fecha_inicio',
        'fecha_fin',
        'img'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the historia.
     */
    public function historia_de(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Esto se tendrá que cambiar la ruta de la imagen
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/historias/' . $this->img);
    }

    /**
     * Check if the historia is currently active.
     *
     * @return bool
     */
    public function historia_activa()
    {
        $now = now();
        return $now->between($this->fecha_inicio, $this->fecha_fin);
    }
}