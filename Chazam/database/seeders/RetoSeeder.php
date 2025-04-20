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
        // Verificar si ya existen retos
        $existenRetos = Reto::count() > 0;
        
        if (!$existenRetos) {
            // Crear retos
            $retos = [
                [
                    'nom_reto' => 'Hoy toca hablar con emojis',
                    'desc_reto' => 'Durante tu próxima conversación, intenta comunicarte principalmente usando emojis. ¡Será divertido ver cómo te las arreglas para expresar tus ideas!',
                ],
                [
                    'nom_reto' => 'Charla temática: Películas',
                    'desc_reto' => 'Inicia una conversación con alguien desconocido y habla sobre tus películas favoritas. Descubre si tienen gustos similares.',
                ],
                [
                    'nom_reto' => 'Juego de palabras',
                    'desc_reto' => 'Inicia una conversación donde cada mensaje debe contener una palabra que comience con la última letra de la palabra anterior.',
                ],
                [
                    'nom_reto' => 'Charla en otro idioma',
                    'desc_reto' => 'Intenta mantener una conversación breve con alguien usando un idioma que no sea tu lengua materna. ¡Es un gran ejercicio!',
                ],
                [
                    'nom_reto' => 'Historia colaborativa',
                    'desc_reto' => 'Inicia una historia con alguien desconocido y turnaos para añadir una frase cada uno. ¡Veamos a dónde llega vuestra imaginación!',
                ],
                [
                    'nom_reto' => 'Charla sin preguntas',
                    'desc_reto' => 'Mantén una conversación interesante sin hacer preguntas directas. ¡Es un desafío más difícil de lo que parece!',
                ],
                [
                    'nom_reto' => 'Comparte un secreto',
                    'desc_reto' => 'Comparte un pequeño secreto o confesión con alguien desconocido. Algo que no sea demasiado personal pero que normalmente no contarías a un extraño.',
                ],
                [
                    'nom_reto' => 'Charla de 3 minutos',
                    'desc_reto' => 'Inicia una conversación y acuerda con tu interlocutor que durará exactamente 3 minutos. ¡Haz que cuente!',
                ],
                [
                    'nom_reto' => 'Charla sin palabras comunes',
                    'desc_reto' => 'Mantén una conversación evitando palabras comunes como "y", "el", "la", "es", "son". ¡Será un desafío lingüístico!',
                ],
                [
                    'nom_reto' => 'Charla de superhéroes',
                    'desc_reto' => 'Inicia una conversación sobre superhéroes y villanos. ¿Cuál sería tu superpoder ideal? ¿Qué villano te gustaría ser?',
                ],
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
