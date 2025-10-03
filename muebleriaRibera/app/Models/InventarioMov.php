<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioMov extends Model
{
    use HasFactory;

    protected $table = 'inventario_mov';

    protected $fillable = [
        'producto_id',
        'tipo_movimiento',
        'cantidad',
        'usuario_id',
        'fecha_movimiento'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha_movimiento' => 'datetime'
    ];

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scope por tipo de movimiento
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    // Scope por producto
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    // Scope por rango de fechas
    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_movimiento', [$desde, $hasta]);
    }
}