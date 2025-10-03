<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol_id',
        'activo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relación con rol
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    // Relación con carrito
    public function carritoItems()
    {
        return $this->hasMany(CarritoItem::class, 'usuario_id');
    }

    // Relación con ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'usuario_id');
    }

    // Relación con bitácora de login
    public function bitacoraLogins()
    {
        return $this->hasMany(BitacoraLogin::class, 'usuario_id');
    }

    // Relación con movimientos de inventario
    public function movimientosInventario()
    {
        return $this->hasMany(InventarioMov::class, 'usuario_id');
    }

    // Verificar si usuario tiene un rol específico
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->rol->nombre, $role);
        }
        return $this->rol->nombre === $role;
    }

    // Verificar permisos
    public function hasPermission($permission)
    {
        return $this->rol->permisos->contains('nombre', $permission);
    }

    // Scope para usuarios activos
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // Accesor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre;
    }
}