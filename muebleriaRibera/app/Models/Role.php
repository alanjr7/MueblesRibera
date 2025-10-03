<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    // Relación con usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    // Relación con permisos
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'rol_id', 'permiso_id');
    }

    // Asignar permisos al rol
    public function asignarPermiso($permiso)
    {
        if (is_string($permiso)) {
            $permiso = Permiso::where('nombre', $permiso)->firstOrFail();
        }
        $this->permisos()->syncWithoutDetaching([$permiso->id]);
    }

    // Remover permiso del rol
    public function removerPermiso($permiso)
    {
        if (is_string($permiso)) {
            $permiso = Permiso::where('nombre', $permiso)->firstOrFail();
        }
        $this->permisos()->detach($permiso->id);
    }
}