<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productos';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_producto';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'precio',
        'tipo_valor',
        'id_tipo_producto'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'precio' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tipo_producto that owns the producto.
     */
    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(TipoProducto::class, 'id_tipo_producto');
    }

    /**
     * Get the formatted currency value.
     *
     * //  
     */
    //  SE MIRARÁ DE USAR
    //  @return string
     
    // public function getValorFormateadoAttribute()
    // {
    //     return '$' . number_format($this->valor, 0, ',', '.');
    // }
}