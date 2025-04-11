<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'solicitudes';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_solicitud';

    /**
     * The possible estado values.
     */
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aceptada' => 'Aceptada',
        'rechazada' => 'Rechazada',
        'solicitando' => 'Solicitando',
        'blockeada' => 'Bloqueada'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'estado',
        'id_emisor',
        'id_receptor'
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
     * Get the emisor (sender) of the solicitud.
     */
    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_emisor');
    }

    /**
     * Get the receptor (receiver) of the solicitud.
     */
    public function receptor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_receptor');
    }

    /**
     * Check if the solicitud is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Check if the solicitud is accepted.
     *
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->estado === 'aceptada';
    }

    /**
     * Check if the solicitud is rejected.
     *
     * @return bool
     */
    public function rechazados($query)
    {
        return $query->where('estado', 'rechazada');
    }

    /**
     * Scope a query to only include pending solicitudes.
     */
    public function verPendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope a query to only include accepted solicitudes.
     */
    public function verAmigos($query)
    {
        return $query->where('estado', 'aceptada');
    }

        /**
     * ESTE HAY QUE CECKEARLO
     */
    public function verBlockeds($query)
    {
        return $query->where('estado', 'bloqueada');
    }

    /**
     * Scope a query to only include solicitudes between two users.
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where('id_emisor', $userId1)
              ->where('id_receptor', $userId2);
        })->orWhere(function($q) use ($userId1, $userId2) {
            $q->where('id_emisor', $userId2)
              ->where('id_receptor', $userId1);
        });
    }
}