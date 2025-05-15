<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pagos';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_pago';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_comprador',
        'fecha_pago',
        'id_producto',
        'cantidad',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_pago' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the buyer (comprador) who made the payment.
     */
    public function comprador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_comprador');
    }

    /**
     * Get the product that was purchased.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    /**
     * Get the formatted payment date.
     *
     * @return string
     */
    public function getFechaPago()
    {
        return $this->fecha_pago->format('d/m/Y H:i');
    }

    /**
     * Compras de 1 UN UNICO user.
     */
    public function scopeByComprador($query, $userId)
    {
        return $query->where('id_comprador', $userId);
    }

    /**
     * Compras para UN UNICO PRODUCTO.
     */
    public function scopeForProducto($query, $productId)
    {
        return $query->where('id_producto', $productId);
    }

    /**
     * Filtrar por entre fechas.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('fecha_pago', [$startDate, $endDate]);
    }
}