<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\RegistraBitacora;
class Producto extends Model
{
    use HasFactory, RegistraBitacora;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'precio_usd',
        'tasa_cambio',
        'stock',
        'categoria_id',
        'imagen_url',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_usd' => 'decimal:2',
        'tasa_cambio' => 'decimal:4',
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

    // Calcular precio en bolivianos automáticamente
    public function getPrecioBsAttribute()
    {
        return $this->precio_usd * $this->tasa_cambio;
    }

    // Formatear precio en bolivianos
    public function getPrecioBsFormateadoAttribute()
    {
        return 'Bs ' . number_format($this->precio_bs, 2);
    }

    // Formatear precio en dólares
    public function getPrecioUsdFormateadoAttribute()
    {
        return '$' . number_format($this->precio_usd, 2) . ' USD';
    }

    // Formatear tasa de cambio
    public function getTasaFormateadaAttribute()
    {
        return 'Bs ' . number_format($this->tasa_cambio, 4);
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

    // Actualizar tasa de cambio para todos los productos
    public static function actualizarTasaGlobal($nuevaTasa)
    {
        return self::where('activo', true)->update(['tasa_cambio' => $nuevaTasa]);
    }
}