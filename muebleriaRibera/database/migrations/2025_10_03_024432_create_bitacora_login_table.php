<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('bitacora_login', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usuario_id'); // igual tipo que usuarios.id (signed)
            $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();

            $table->timestamp('fecha_login')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('exito')->default(false);
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('bitacora_login'); }
};