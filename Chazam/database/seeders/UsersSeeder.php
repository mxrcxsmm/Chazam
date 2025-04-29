<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'fecha_nacimiento' => '1990-01-01',
                'genero' => 'hombre',
                'email' => 'admin@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 1000,
                'id_nacionalidad' => 1,
                'id_rol' => 1,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Administrador del sistema'
            ],
            [
                'username' => 'moderador',
                'nombre' => 'Moderador',
                'apellido' => 'Sistema',
                'fecha_nacimiento' => '1990-01-01',
                'genero' => 'hombre',
                'email' => 'moderador@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 800,
                'id_nacionalidad' => 1,
                'id_rol' => 2,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Moderador del sistema'
            ],
            [
                'username' => 'usuario1',
                'nombre' => 'Usuario',
                'apellido' => 'Ejemplo',
                'fecha_nacimiento' => '1995-05-15',
                'genero' => 'hombre',
                'email' => 'usuario1@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 500,
                'id_nacionalidad' => 2,
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ],
            [
                'username' => 'usuario2',
                'nombre' => 'Carlos',
                'apellido' => 'Gómez',
                'fecha_nacimiento' => '1992-07-20',
                'genero' => 'hombre',
                'email' => 'carlos@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 450,
                'id_nacionalidad' => 3, // Colombiana
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ],
            [
                'username' => 'usuario3',
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'fecha_nacimiento' => '1993-08-15',
                'genero' => 'mujer',
                'email' => 'ana@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 470,
                'id_nacionalidad' => 4, // Argentina
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ],
            [
                'username' => 'usuario4',
                'nombre' => 'Luis',
                'apellido' => 'Fernández',
                'fecha_nacimiento' => '1994-09-10',
                'genero' => 'hombre',
                'email' => 'luis@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 480,
                'id_nacionalidad' => 5, // Chilena
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ],
            [
                'username' => 'usuario5',
                'nombre' => 'María',
                'apellido' => 'López',
                'fecha_nacimiento' => '1995-10-05',
                'genero' => 'mujer',
                'email' => 'maria@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 490,
                'id_nacionalidad' => 6, // Peruana
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ],
            [
                'username' => 'usuario6',
                'nombre' => 'Jorge',
                'apellido' => 'Pérez',
                'fecha_nacimiento' => '1996-11-25',
                'genero' => 'hombre',
                'email' => 'jorge@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 500,
                'id_nacionalidad' => 7, // Venezolana
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ]
        ]);
    }
} 