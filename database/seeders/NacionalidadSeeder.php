<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NacionalidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nacionalidad')->insert([
            ['nombre' => 'España', 'bandera' => 'es.png'],
            ['nombre' => 'Mexico', 'bandera' => 'mx.png'],
            ['nombre' => 'Colombia', 'bandera' => 'co.png'],
            ['nombre' => 'Argentina', 'bandera' => 'ar.png'],
            ['nombre' => 'Chile', 'bandera' => 'cl.png'],
            ['nombre' => 'Perú', 'bandera' => 'pe.png'],
            ['nombre' => 'Venezuela', 'bandera' => 've.png'],
            ['nombre' => 'Ecuador', 'bandera' => 'ec.png'],
            ['nombre' => 'Brasil', 'bandera' => 'br.png'],
            ['nombre' => 'Chile', 'bandera' => 'cl.png'],
            ['nombre' => 'China', 'bandera' => 'cn.png'],
            ['nombre' => 'Japón', 'bandera' => 'jp.png'],
            ['nombre' => 'Corea del Sur', 'bandera' => 'kr.png'],
            ['nombre' => 'India', 'bandera' => 'in.png'],
            ['nombre' => 'Australia', 'bandera' => 'au.png'],
            ['nombre' => 'Nueva Zelanda', 'bandera' => 'nz.png'],
            ['nombre' => 'Estados Unidos', 'bandera' => 'us.png'],
            ['nombre' => 'Canada', 'bandera' => 'ca.png'],
            ['nombre' => 'Francia', 'bandera' => 'fr.png'],
            ['nombre' => 'Alemania', 'bandera' => 'de.png'],
            ['nombre' => 'Italia', 'bandera' => 'it.png'],
            ['nombre' => 'Portugal', 'bandera' => 'pt.png'],
            ['nombre' => 'Grecia', 'bandera' => 'gr.png']
        ]);
    }
}