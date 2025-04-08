<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NacionalidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nacionalidad')->insert([
            ['nombre' => 'Española', 'bandera' => 'es.png'],
            ['nombre' => 'Mexicana', 'bandera' => 'mx.png'],
            ['nombre' => 'Colombiana', 'bandera' => 'co.png'],
            ['nombre' => 'Argentina', 'bandera' => 'ar.png'],
            ['nombre' => 'Chilena', 'bandera' => 'cl.png'],
            ['nombre' => 'Peruana', 'bandera' => 'pe.png'],
            ['nombre' => 'Venezolana', 'bandera' => 've.png'],
            ['nombre' => 'Ecuatoriana', 'bandera' => 'ec.png']
        ]);
    }
} 