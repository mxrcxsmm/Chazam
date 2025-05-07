<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmistadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cambiar el nombre y apellido de los usuarios
        DB::table('users')->where('id_usuario', 4)->update([
            'nombre' => 'Carlos',
            'apellido' => 'Cliente',
        ]);
        DB::table('users')->where('id_usuario', 5)->update([
            'nombre' => 'Ana',
            'apellido' => 'Cliente',
        ]);

        // Crear un chat
        $chatId = DB::table('chats')->insertGetId([
            'fecha_creacion' => Carbon::now(),
            'img' => null,
            'nombre' => 'Chat de Amistad',
            'descripcion' => 'Chat entre Carlos y Ana',
            'id_reto' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Relacionar ambos usuarios al chat
        $chatUsuario1 = DB::table('chat_usuario')->insertGetId([
            'id_chat' => $chatId,
            'id_usuario' => 4, // Carlos
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $chatUsuario2 = DB::table('chat_usuario')->insertGetId([
            'id_chat' => $chatId,
            'id_usuario' => 5, // Ana
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insertar un mensaje de ejemplo
        DB::table('mensajes')->insert([
            [
                'id_chat_usuario' => $chatUsuario1,
                'contenido' => '¡Hola Ana! ¿Cómo estás?',
                'fecha_envio' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_chat_usuario' => $chatUsuario2,
                'contenido' => '¡Hola Carlos! Muy bien, ¿y tú?',
                'fecha_envio' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
} 