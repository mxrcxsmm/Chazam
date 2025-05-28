<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('productos')->insert([
            // Suscripciones (id_tipo_producto = 1)
            [
                'titulo' => 'Usuario Premium',
                'descripcion' => 'Acceso completo a todas las funciones premium durante un mes.',
                'precio' => 9.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 1,
            ],

            // Compras únicas (id_tipo_producto = 2)
            [
                'titulo' => 'Mejorar skips',
                'descripcion' => 'Reduce el tiempo de espera para realizar skips.',
                'precio' => 5.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 2,
            ],
            [
                'titulo' => 'Combo 1',
                'descripcion' => 'Incluye 1 mes de suscripción premium y 750 puntos.',
                'precio' => 14.99,
                'tipo_valor' => 'euros',
                'puntos' => 750,
                'id_tipo_producto' => 2,
            ],
            [
                'titulo' => 'Combo 2',
                'descripcion' => 'Incluye 1 mes de suscripción premium y 1250 puntos.',
                'precio' => 19.99,
                'tipo_valor' => 'euros',
                'puntos' => 1250,
                'id_tipo_producto' => 2,
            ],
            [
                'titulo' => 'Combo 3',
                'descripcion' => 'Incluye 1 mes de suscripción premium y 2500 puntos.',
                'precio' => 29.99,
                'tipo_valor' => 'euros',
                'puntos' => 2500,
                'id_tipo_producto' => 2,
            ],
            // packs de puntos (id_tipo_producto = 4)
            [
                'titulo' => 'Pack de 1000 puntos',
                'descripcion' => 'Compra de 1000 puntos en Chazam.',
                'precio' => 1.99,
                'tipo_valor' => 'euros',
                'puntos' => 1000,
                'id_tipo_producto' => 3,
            ],
            [
                'titulo' => 'Pack de 2000 puntos',
                'descripcion' => 'Compra de 2000 puntos en Chazam.',
                'precio' => 4.99,
                'tipo_valor' => 'euros',
                'puntos' => 2000,
                'id_tipo_producto' => 3,
            ],
            [
                'titulo' => 'Pack de 3500 puntos',
                'descripcion' => 'Compra de 3500 puntos en Chazam.',
                'precio' => 7.99,
                'tipo_valor' => 'euros',
                'puntos' => 3500,
                'id_tipo_producto' => 3,
            ],
            [
                'titulo' => 'Pack de 5000 puntos',
                'descripcion' => 'Compra de 5000 puntos en Chazam.',
                'precio' => 9.99,
                'tipo_valor' => 'euros',
                'puntos' => 5000,
                'id_tipo_producto' => 3,
            ],
            [
                'titulo' => 'Pack de 10000 puntos',
                'descripcion' => 'Compra de 10000 puntos en Chazam.',
                'precio' => 19.99,
                'tipo_valor' => 'euros',
                'puntos' => 10000,
                'id_tipo_producto' => 3,
            ],
            [
                'titulo' => 'Usuario miembro con puntos',
                'descripcion' => 'Usuario miembro con 15000 puntos.',
                'precio' => 15000,
                'tipo_valor' => 'puntos',
                'puntos' => 15000,
                'id_tipo_producto' => 1,
            ],
            [
                'titulo' => 'Mejorar skips con puntos',
                'descripcion' => 'Mejorar skips con 60000 puntos.',
                'precio' => 60000,
                'tipo_valor' => 'puntos',
                'puntos' => 60000,
                'id_tipo_producto' => 2,
            ],
            [
                'titulo' => 'Comunidad',
                'descripcion' => 'Creación de una comunidad.',
                'precio' => 75000,
                'tipo_valor' => 'puntos',
                'puntos' => null,
                'id_tipo_producto' => 5,
            ],
            // 1) Cada uno de tus marcos disponibles
            [
                'titulo'           => 'Marco Clásico',
                'descripcion'      => 'default.svg',
                'precio'           => 100,
                'tipo_valor'       => 'puntos',
                'puntos'           => 100,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Azul Suave',
                'descripcion'      => 'azuul.svg',
                'precio'           => 120,
                'tipo_valor'       => 'puntos',
                'puntos'           => 120,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Azul Champions',
                'descripcion'      => 'azul-champions.svg',
                'precio'           => 120,
                'tipo_valor'       => 'puntos',
                'puntos'           => 120,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Rojinegro',
                'descripcion'      => 'circle-rojonegro.svg',
                'precio'           => 130,
                'tipo_valor'       => 'puntos',
                'puntos'           => 130,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Cromado',
                'descripcion'      => 'cromado.svg',
                'precio'           => 150,
                'tipo_valor'       => 'puntos',
                'puntos'           => 150,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Cromado Normal',
                'descripcion'      => 'cromado-normal.svg',
                'precio'           => 140,
                'tipo_valor'       => 'puntos',
                'puntos'           => 140,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Cromado Pequeño',
                'descripcion'      => 'cromado-peque.svg',
                'precio'           => 130,
                'tipo_valor'       => 'puntos',
                'puntos'           => 130,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Estrellas Cutres',
                'descripcion'      => 'cutre-estrellas.svg',
                'precio'           => 110,
                'tipo_valor'       => 'puntos',
                'puntos'           => 110,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Golden Champions',
                'descripcion'      => 'golden-champions.svg',
                'precio'           => 160,
                'tipo_valor'       => 'puntos',
                'puntos'           => 160,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Wave Haikei',
                'descripcion'      => 'wave-haikei.svg',
                'precio'           => 125,
                'tipo_valor'       => 'puntos',
                'puntos'           => 125,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo'           => 'Marco Estrellas Haikei',
                'descripcion'      => 'estrellas-haikei.svg',
                'precio'           => 125,
                'tipo_valor'       => 'puntos',
                'puntos'           => 125,
                'id_tipo_producto' => 6,
            ],

            // 2) Animación rotatoria
            [
                'titulo'           => 'Rotación de Marco',
                'descripcion'      => 'Activa/desactiva la rotación de tu marco.',
                'precio'           => 80,
                'tipo_valor'       => 'puntos',
                'puntos'           => 80,
                'id_tipo_producto' => 6,
            ],

            // 3) Brillo
            [
                'titulo'           => 'Brillo de Marco',
                'descripcion'      => 'Añade un resplandor de color a tu marco.',
                'precio'           => 90,
                'tipo_valor'       => 'puntos',
                'puntos'           => 90,
                'id_tipo_producto' => 6,
            ],

            // 4) Color de Sidebar
            [
                'titulo'           => 'Color de Sidebar',
                'descripcion'      => 'Personaliza el color de tu menú lateral.',
                'precio'           => 100,
                'tipo_valor'       => 'puntos',
                'puntos'           => 100,
                'id_tipo_producto' => 6,
            ],
        ]);
    }
}