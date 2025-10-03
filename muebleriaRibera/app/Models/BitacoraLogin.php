<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraLogin extends Model
{
    use HasFactory;

    protected $table = 'bitacora_login';

    protected $fillable = [
        'usuario_id',
        'fecha_login',
        'ip_address',
        'exito'
    ];

    protected $casts = [
        'fecha_login' => 'datetime',
        'exito' => 'boolean'
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scope para logins exitosos
    public function scopeExitosos($query)
    {
        return $query->where('exito', true);
    }

    // Scope para logins fallidos
    public function scopeFallidos($query)
    {
        return $query->where('exito', false);
    }

    // Scope por rango de fechas
    public function scopePorRangoFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_login', [$desde, $hasta]);
    }
}