<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nacionalidad';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_nacionalidad';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'bandera',
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


    // /**
                        // No se sabe si se va a usar banderas img o emojis
    //   Get the URL for the bandera image.
    //  
    //   @return string|null
    //  /
    // public function getBanderaUrlAttribute()
    // {
    //     return $this->bandera ? asset('storage/banderas/'.$this->bandera) : null;
    // }
}