<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('inventario_mov', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
           $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('inventario_mov'); }
};