<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_usd', 10, 2)->after('precio')->default(0);
            $table->decimal('tasa_cambio', 10, 4)->after('precio_usd')->default(6.96);
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['precio_usd', 'tasa_cambio']);
        });
    }
};