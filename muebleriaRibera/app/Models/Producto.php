<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'categoria_id',
        'imagen_url',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'activo' => 'boolean'
    ];

    // Relación con categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    // Relación con carrito items
    public function carritoItems()
    {
        return $this->hasMany(CarritoItem::class, 'producto_id');
    }

    // Relación con detalles de venta
    public function ventaDetalles()
    {
        return $this->hasMany(VentaDetalle::class, 'producto_id');
    }

    // Relación con movimientos de inventario
    public function movimientosInventario()
    {
        return $this->hasMany(InventarioMov::class, 'producto_id');
    }

    // Scope para productos activos
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // Scope para productos en stock
    public function scopeEnStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $search)
    {
        return $query->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%")
                    ->orWhereHas('categoria', function($q) use ($search) {
                        $q->where('nombre', 'LIKE', "%{$search}%");
                    });
    }

    // Verificar si hay stock disponible
    public function tieneStock($cantidad = 1)
    {
        return $this->stock >= $cantidad;
    }

    // Decrementar stock
    public function decrementarStock($cantidad = 1)
    {
        $this->decrement('stock', $cantidad);
    }

    // Incrementar stock
    public function incrementarStock($cantidad = 1)
    {
        $this->increment('stock', $cantidad);
    }
}