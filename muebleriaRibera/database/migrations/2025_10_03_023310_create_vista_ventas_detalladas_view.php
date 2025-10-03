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
        DB::statement("CREATE VIEW `vista_ventas_detalladas` AS select `v`.`id` AS `id`,`v`.`fecha_venta` AS `fecha_venta`,`u`.`nombre` AS `vendedor`,`v`.`total` AS `total`,`v`.`estado` AS `estado`,count(`vd`.`id`) AS `items_vendidos` from ((`muebleriaribera`.`ventas` `v` join `muebleriaribera`.`usuarios` `u` on(`v`.`usuario_id` = `u`.`id`)) left join `muebleriaribera`.`venta_detalles` `vd` on(`v`.`id` = `vd`.`venta_id`)) group by `v`.`id`,`v`.`fecha_venta`,`u`.`nombre`,`v`.`total`,`v`.`estado`");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `vista_ventas_detalladas`");
    }
};
