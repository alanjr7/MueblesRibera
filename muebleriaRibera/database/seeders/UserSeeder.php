<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'nombre' => 'Administrador Principal',
                'email' => 'admin@sistema.com',
                'password' => Hash::make('admin123'), // Contraseña: admin123
                'rol_id' => 1, // superadmin
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Juan Soldador',
                'email' => 'soldador@sistema.com',
                'password' => Hash::make('soldador123'), // Contraseña: soldador123
                'rol_id' => 2, // soldador
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Maria Vendedora',
                'email' => 'vendedor@sistema.com',
                'password' => Hash::make('vendedor123'), // Contraseña: vendedor123
                'rol_id' => 3, // vendedor
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Carlos Vendedor',
                'email' => 'carlos@sistema.com',
                'password' => Hash::make('carlos123'), // Contraseña: carlos123
                'rol_id' => 3, // vendedor
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('usuarios')->insert($users);
    }
}