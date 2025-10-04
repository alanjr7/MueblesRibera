<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraAccion extends Model
{
    use HasFactory;

    protected $table = 'bitacora_acciones';

    protected $fillable = [
        'usuario_id',
        'accion',
        'modelo',
        'modelo_id',
        'descripcion',
        'ip_address',
        'user_agent',
        'datos_anteriores',
        'datos_nuevos',
        'url',
        'metodo'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime'
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scope por acción
    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    // Scope por modelo
    public function scopePorModelo($query, $modelo)
    {
        return $query->where('modelo', $modelo);
    }

    // Scope por usuario
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // Scope por rango de fechas
    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    // Accesor para acción formateada
    public function getAccionFormateadaAttribute()
    {
        $acciones = [
            'created' => 'Creación',
            'updated' => 'Actualización',
            'deleted' => 'Eliminación',
            'login' => 'Inicio de sesión',
            'logout' => 'Cierre de sesión',
            'stock_ajustado' => 'Ajuste de stock',
            'venta_realizada' => 'Venta realizada',
            'venta_cancelada' => 'Venta cancelada'
        ];

        return $acciones[$this->accion] ?? $this->accion;
    }
}