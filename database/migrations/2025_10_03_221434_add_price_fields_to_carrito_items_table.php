<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('carrito_items', function (Blueprint $table) {
            $table->decimal('precio_unitario', 10, 2)->after('cantidad')->default(0);
            $table->decimal('subtotal', 10, 2)->after('precio_unitario')->default(0);
        });
    }

    public function down()
    {
        Schema::table('carrito_items', function (Blueprint $table) {
            $table->dropColumn(['precio_unitario', 'subtotal']);
        });
    }
};