<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class TipoProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_producto')->insert([
            ['tipo_producto' => 'Membresía'],
            ['tipo_producto' => 'Compras únicas'],
            ['tipo_producto' => 'Packs de puntos'],
            ['tipo_producto' => 'Donaciones'],
            ['tipo_producto' => 'Comunidad'],
        ]);
    }
}
