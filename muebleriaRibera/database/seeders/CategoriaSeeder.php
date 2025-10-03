<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            [
                'nombre' => 'Camas',
                'descripcion' => 'Diferentes tipos de camas y bases',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Rejas',
                'descripcion' => 'Rejas de protección y decorativas',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sillas',
                'descripcion' => 'Sillas de diferentes estilos y materiales',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Mesas',
                'descripcion' => 'Mesas de centro, comedor y exterior',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Armarios',
                'descripcion' => 'Armarios y closets de diferentes tamaños',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Otros',
                'descripcion' => 'Otros productos de mueblería',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categorias')->insert($categorias);

        $this->command->info('¡Categorías creadas exitosamente!');
        $this->command->info('Categorías disponibles: Camas, Rejas, Sillas, Mesas, Armarios, Otros');
    }
}