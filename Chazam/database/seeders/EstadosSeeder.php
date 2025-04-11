<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estados')->insert([
            ['nom_estado' => 'Activo'],
            ['nom_estado' => 'Inactivo'],
            ['nom_estado' => 'Ban'],
            ['nom_estado' => 'PermaBan'],
            ['nom_estado' => 'Disponible']
        ]);
    }
} 