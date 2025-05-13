<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reto;
use Illuminate\Support\Facades\DB;

class RetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncar la tabla de retos
        DB::table('retos')->truncate();
        
        // Reactivar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Verificar si ya existen retos
        $existenRetos = Reto::count() > 0;
        
        if (!$existenRetos) {
            // Crear retos
            $retos = [
                [
                    'nom_reto' => 'Hoy toca hablar con emojis',
                    'desc_reto' => 'Usa SOLO emojis para comunicarte. ¿Podrán entenderte? ¡Sé creativo con tus combinaciones!',
                ],
                [
                    'nom_reto' => 'Mensaje encriptado',
                    'desc_reto' => '¡Algunos carácteres están cifrados! ¿Podrás conseguir comunicarte con tu pareja?',
                ],
                [
                    'nom_reto' => 'Desorden absoluto',
                    'desc_reto' => 'Vuestras frases se enviarán desordenadas, intentad descifrar el mensaje original',
                ],
                [
                    'nom_reto' => 'Boca abajo',
                    'desc_reto' => 'Vuestro texto estará boca abajo ¡no forzéis mucho el cuello!',
                ]
            ];
            
            // Insertar los retos en la base de datos
            foreach ($retos as $reto) {
                Reto::create($reto);
            }
            
            $this->command->info('Retos creados exitosamente.');
        } else {
            $this->command->info('Ya existen retos en la base de datos. No se han creado nuevos retos.');
        }
    }
}
