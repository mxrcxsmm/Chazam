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
                'email' => 'usuario1@example.com',
                'password' => Hash::make('qweQWE123'),
                'puntos' => 500,
                'id_nacionalidad' => 2,
                'id_rol' => 3,
                'id_estado' => 1,
                'img' => null,
                'descripcion' => 'Usuario de ejemplo'
            ]
        ]);
    }
} 