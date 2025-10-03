<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
          $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamp('fecha_venta')->useCurrent();
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('ventas'); }
};