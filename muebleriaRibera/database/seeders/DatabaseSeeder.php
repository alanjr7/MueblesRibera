<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            CategoriaSeeder::class, // ← AGREGAR ESTA LÍNEA
            UserSeeder::class,
        ]);
    }
}