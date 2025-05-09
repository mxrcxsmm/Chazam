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
                'titulo' => 'Suscripción Básica',
                'descripcion' => 'Acceso básico a las funciones premium durante un mes.',
                'precio' => 9.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 1,
            ],
            [
                'titulo' => 'Suscripción Premium',
                'descripcion' => 'Acceso completo a todas las funciones premium durante un mes.',
                'precio' => 19.99,
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
                'titulo' => 'Skips ilimitados',
                'descripcion' => 'Disfruta de skips ilimitados durante un mes.',
                'precio' => 9.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 2,
            ],

            // Packs (id_tipo_producto = 3) - Ahora son combos
            [
                'titulo' => 'Combo Básico',
                'descripcion' => 'Incluye 1 mes de suscripción básica y 250 puntos.',
                'precio' => 11.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 3,
            ],
            [
                'titulo' => 'Combo Premium',
                'descripcion' => 'Incluye 1 mes de suscripción premium y 500 puntos.',
                'precio' => 24.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 3,
            ],

            // Compras repetibles (id_tipo_producto = 4)
            [
                'titulo' => 'Pack de 750 puntos',
                'descripcion' => 'Compra de 750 puntos en Chazam.',
                'precio' => 9.99,
                'tipo_valor' => 'euros',
                'puntos' => 750,
                'id_tipo_producto' => 4,
            ],
            [
                'titulo' => 'Pack de 1000 puntos',
                'descripcion' => 'Compra de 1000 puntos en Chazam.',
                'precio' => 14.99,
                'tipo_valor' => 'euros',
                'puntos' => 1000,
                'id_tipo_producto' => 4,
            ],

            // Descuentos (id_tipo_producto = 5)
            [
                'titulo' => 'Descuento del 10%',
                'descripcion' => 'Obtén un descuento del 10% en tu próxima compra.',
                'precio' => 0.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 5,
            ],
            [
                'titulo' => 'Descuento del 20%',
                'descripcion' => 'Obtén un descuento del 20% en tu próxima compra.',
                'precio' => 1.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 5,
            ],

            // Donaciones (id_tipo_producto = 6)
            [
                'titulo' => 'Donación pequeña',
                'descripcion' => 'Apoya el desarrollo de Chazam con una pequeña donación.',
                'precio' => 2.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 6,
            ],
            [
                'titulo' => 'Donación grande',
                'descripcion' => 'Apoya el desarrollo de Chazam con una gran donación.',
                'precio' => 9.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 6,
            ],

            // Créditos (id_tipo_producto = 7)
            [
                'titulo' => '100 Créditos',
                'descripcion' => 'Compra de 100 créditos para usar en Chazam.',
                'precio' => 4.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 7,
            ],
            [
                'titulo' => '500 Créditos',
                'descripcion' => 'Compra de 500 créditos para usar en Chazam.',
                'precio' => 19.99,
                'tipo_valor' => 'euros',
                'puntos' => null,
                'id_tipo_producto' => 7,
            ],
        ]);
    }
}
