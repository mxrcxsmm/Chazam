<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reporte extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reportes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_reporte';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'id_reportador',
        'id_reportado'
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
     * Get the user who made the report.
     */
    public function reportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reportador');
    }

    /**
     * Get the user who was reported.
     */
    public function reportado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reportado');
    }

    /**
     * Ver si hay un usuario que abusa de reportar.
     */
    public function scopeFromReporter($query, $userId)
    {
        return $query->where('id_reportador', $userId);
    }

    /**
     * Revisar reportes sobre cierto individuo.
     */
    public function scopeAboutReported($query, $userId)
    {
        return $query->where('id_reportado', $userId);
    }
}