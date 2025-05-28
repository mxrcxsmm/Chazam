<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalizarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos todos los IDs de usuario
        $userIds = DB::table('users')->pluck('id_usuario');

        // Para cada usuario, insertamos la personalización por defecto
        foreach ($userIds as $userId) {
            DB::table('personalizacion')->insert([
                'id_usuario'  => $userId,
                'marco'       => 'default.svg',  // marco por defecto
                'rotacion'    => false,          // sin rotación
                'brillo'      => null,           // brillo null
                'sidebar'     => '#4B0082',      // color de sidebar por defecto
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}