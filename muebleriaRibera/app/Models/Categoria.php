<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Scope para categorías activas
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $search)
    {
        return $query->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('descripcion', 'LIKE', "%{$search}%");
    }
}