<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nom_rol' => 'Administrador'],
            ['nom_rol' => 'Usuario'],
            ['nom_rol' => 'Premium'],
            ['nom_rol' => 'Miembro']
        ]);
    }
} 