<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'usuario_id',
        'fecha_venta',
        'total',
        'estado'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_venta' => 'datetime'
    ];

    // Relación con usuario (vendedor)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relación con detalles de venta
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    // Scope para ventas por estado
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Scope para ventas por fecha
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_venta', $fecha);
    }

    // Scope para ventas por rango de fechas
    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_venta', [$desde, $hasta]);
    }

    // Calcular total de la venta
    public function calcularTotal()
    {
        return $this->detalles->sum('subtotal');
    }

    // Marcar como completada
    public function marcarCompletada()
    {
        $this->update(['estado' => 'completada']);
    }

    // Marcar como cancelada
    public function marcarCancelada()
    {
        $this->update(['estado' => 'cancelada']);
    }
}