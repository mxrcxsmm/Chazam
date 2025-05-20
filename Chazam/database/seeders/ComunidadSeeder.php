<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chat;
use Carbon\Carbon;

class ComunidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comunidades = [
            [
                'nombre' => 'Programadores PHP',
                'descripcion' => 'Comunidad para discutir sobre desarrollo en PHP, Laravel y frameworks relacionados.',
                'tipocomunidad' => 'publica',
                'creator' => 1,
                'fecha_creacion' => Carbon::now(),
                'img' => 'php.jpg',
            ],
            [
                'nombre' => 'Gamers Chazam',
                'descripcion' => 'Comunidad para gamers que quieren compartir sus experiencias y organizar partidas.',
                'tipocomunidad' => 'publica',
                'creator' => 1,
                'fecha_creacion' => Carbon::now(),
                'img' => 'gamers.jpg',
            ],
            [
                'nombre' => 'Música y Arte',
                'descripcion' => 'Espacio para compartir y discutir sobre música, arte y cultura en general.',
                'tipocomunidad' => 'publica',
                'creator' => 1,
                'fecha_creacion' => Carbon::now(),
                'img' => 'music_art.jpg',
            ],
            [
                'nombre' => 'Comunidad Cerrada',
                'descripcion' => 'Comunidad privada para miembros selectos.',
                'tipocomunidad' => 'privada',
                'codigo' => 'CHAZAM2024',
                'creator' => 1,
                'fecha_creacion' => Carbon::now(),
                'img' => 'secret.jpg',
            ],
        ];

        foreach ($comunidades as $comunidad) {
            Chat::create($comunidad);
        }
    }
}
