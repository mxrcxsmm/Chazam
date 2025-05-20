<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar chats
        $chat1 = DB::table('chats')->insertGetId([
            'fecha_creacion' => Carbon::now(),
            'img' => 'chat1.jpg',
            'nombre' => 'Comunidad Gamers',
            'descripcion' => 'Chat para amantes de los videojuegos',
            'id_reto' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $chat2 = DB::table('chats')->insertGetId([
            'fecha_creacion' => Carbon::now(),
            'img' => 'chat2.jpg',
            'nombre' => 'Música y Arte',
            'descripcion' => 'Comparte tu pasión por la música y el arte',
            'id_reto' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $chat3 = DB::table('chats')->insertGetId([
            'fecha_creacion' => Carbon::now(),
            'img' => 'chat3.jpg',
            'nombre' => 'Programación',
            'descripcion' => 'Comunidad de desarrolladores',
            'id_reto' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Insertar relaciones chat_usuario
        DB::table('chat_usuario')->insert([
            [
                'id_chat' => $chat1,
                'id_usuario' => 1, // Admin en Comunidad Gamers
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_chat' => $chat1,
                'id_usuario' => 2, // Moderador en Comunidad Gamers
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_chat' => $chat1,
                'id_usuario' => 3, // Usuario1 en Comunidad Gamers
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_chat' => $chat2,
                'id_usuario' => 1, // Admin en Música y Arte
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_chat' => $chat2,
                'id_usuario' => 3, // Usuario1 en Música y Arte
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_chat' => $chat3,
                'id_usuario' => 1, // Admin en Programación
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id_chat' => $chat3,
                'id_usuario' => 2, // Moderador en Programación
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
} 