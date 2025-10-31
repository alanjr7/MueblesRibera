<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoItem extends Model
{
    use HasFactory;

    protected $table = 'carrito_items';

    protected $fillable = [
        'usuario_id',
        'producto_id',
        'cantidad'
    ];

    protected $casts = [
        'cantidad' => 'integer'
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Calcular subtotal
    public function getSubtotalAttribute()
    {
        return $this->producto->precio * $this->cantidad;
    }

    // Verificar si el producto está disponible
    public function getDisponibleAttribute()
    {
        return $this->producto->tieneStock($this->cantidad);
    }

    // Incrementar cantidad
    public function incrementarCantidad($cantidad = 1)
    {
        $this->increment('cantidad', $cantidad);
    }

    // Decrementar cantidad
    public function decrementarCantidad($cantidad = 1)
    {
        $this->decrement('cantidad', $cantidad);
    }
}