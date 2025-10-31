<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();
        $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->timestamps();
            $table->unique(['usuario_id', 'producto_id']);
        });
    }
    public function down() { Schema::dropIfExists('carrito_items'); }
};