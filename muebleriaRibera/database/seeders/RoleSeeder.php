<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'nombre' => 'superadmin',
                'descripcion' => 'Administrador total del sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'soldador',
                'descripcion' => 'Usuario especializado en soldadura',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'vendedor',
                'descripcion' => 'Usuario con permisos de venta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        // Insertar permisos básicos
        $permisos = [
            ['nombre' => 'gestion_usuarios', 'descripcion' => 'Crear, editar y eliminar usuarios'],
            ['nombre' => 'gestion_productos', 'descripcion' => 'Gestionar catálogo de productos'],
            ['nombre' => 'realizar_ventas', 'descripcion' => 'Procesar ventas y transacciones'],
            ['nombre' => 'ver_reportes', 'descripcion' => 'Acceder a reportes del sistema'],
            ['nombre' => 'gestion_inventario', 'descripcion' => 'Controlar stock y movimientos'],
        ];

        foreach ($permisos as $permiso) {
            DB::table('permisos')->insert([
                'nombre' => $permiso['nombre'],
                'descripcion' => $permiso['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Asignar permisos a roles
        $superadminPermisos = DB::table('permisos')->pluck('id');
        foreach ($superadminPermisos as $permisoId) {
            DB::table('rol_permiso')->insert([
                'rol_id' => 1, // superadmin
                'permiso_id' => $permisoId,
                'created_at' => now(),
            ]);
        }

        // Permisos para vendedor
        $vendedorPermisos = [2, 3, 4]; // gestion_productos, realizar_ventas, ver_reportes
        foreach ($vendedorPermisos as $permisoId) {
            DB::table('rol_permiso')->insert([
                'rol_id' => 3, // vendedor
                'permiso_id' => $permisoId,
                'created_at' => now(),
            ]);
        }

        // Permisos para soldador
        $soldadorPermisos = [3, 4]; // realizar_ventas, ver_reportes
        foreach ($soldadorPermisos as $permisoId) {
            DB::table('rol_permiso')->insert([
                'rol_id' => 2, // soldador
                'permiso_id' => $permisoId,
                'created_at' => now(),
            ]);
        }
    }
}