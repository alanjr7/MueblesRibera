<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Verificar que las tablas existan antes de crear la vista
        $tablasExisten = DB::select("SHOW TABLES LIKE 'productos'") && DB::select("SHOW TABLES LIKE 'categorias'");
        
        if ($tablasExisten) {
            DB::statement("
                CREATE OR REPLACE VIEW vista_inventario_actual AS 
                SELECT 
                    p.id,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.precio_usd,
                    p.tasa_cambio,
                    p.stock,
                    c.nombre as categoria,
                    p.activo
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
            ");
        }
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS vista_inventario_actual');
    }
};