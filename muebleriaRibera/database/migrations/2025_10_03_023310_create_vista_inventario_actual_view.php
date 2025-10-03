<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW `vista_inventario_actual` AS select `p`.`id` AS `id`,`p`.`nombre` AS `nombre`,`p`.`descripcion` AS `descripcion`,`p`.`precio` AS `precio`,`p`.`stock` AS `stock`,`c`.`nombre` AS `categoria`,`p`.`activo` AS `activo` from (`muebleriaribera`.`productos` `p` left join `muebleriaribera`.`categorias` `c` on(`p`.`categoria_id` = `c`.`id`))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `vista_inventario_actual`");
    }
};
