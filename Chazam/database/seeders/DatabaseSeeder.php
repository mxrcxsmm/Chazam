<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            EstadosSeeder::class,
            NacionalidadSeeder::class,
            UsersSeeder::class,
            PersonalizarSeeder::class,
            RetoSeeder::class,
            TipoProductosSeeder::class,
            ProductosSeeder::class,
            ComunidadSeeder::class,
        ]);
    }
}
