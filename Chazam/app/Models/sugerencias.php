<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sugerencia extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sugerencia';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_sugerencia';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'id_sugerente'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who made the suggestion.
     */
    public function sugerente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_sugerente');
    }

    /**
     * Get a shortened version of the description. (VEREMOS SI SE USA)
     *
     * @return string
     */
    public function getDescripcionCortaAttribute()
    {
        return str()->limit($this->descripcion, 100);
    }

    /**
     * Scope a query to only include suggestions from a specific user.
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('id_sugerente', $userId);
    }

    /**
     * FILTRADO.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('titulo', 'like', "%{$searchTerm}%")
                    ->orWhere('descripcion', 'like', "%{$searchTerm}%");
    }
}