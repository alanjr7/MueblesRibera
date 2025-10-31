<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bitacora_acciones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('usuario_id'); // 👈 igual tipo que usuarios.id (signed)
            $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();
            $table->string('accion', 50); // created, updated, deleted, login, logout, etc.
            $table->string('modelo', 100)->nullable(); // Producto, Categoria, User, etc.
            $table->unsignedBigInteger('modelo_id')->nullable(); // ID del modelo afectado
            $table->text('descripcion');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('url')->nullable();
            $table->string('metodo', 10)->nullable();
            $table->timestamps();

            // Índices para mejor performance
            $table->index(['usuario_id', 'created_at']);
            $table->index(['modelo', 'modelo_id']);
            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bitacora_acciones');
    }
};