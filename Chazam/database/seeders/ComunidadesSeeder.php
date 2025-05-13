<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ComunidadesSeeder extends Seeder
{
    public function run()
    {
        // Crear la tabla de comunidades si no existe
        Schema::create('comunidades', function (Blueprint $table) {
            $table->id('id_comunidad');
            $table->string('nombre');
            $table->text('descripcion');
            $table->timestamps();
        });

        // Insertar datos de ejemplo
        DB::table('comunidades')->insert([
            [
                'nombre' => 'Comunidad de Desarrolladores',
                'descripcion' => 'Un lugar para compartir conocimientos y experiencias sobre desarrollo de software.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Amantes de la Tecnología',
                'descripcion' => 'Discute las últimas tendencias y avances en tecnología.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Fotografía y Arte',
                'descripcion' => 'Comparte tus mejores fotos y técnicas de arte.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 