<?php

namespace App\Traits;

use App\Models\BitacoraAccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait RegistraBitacora
{
    protected static function bootRegistraBitacora()
    {
        static::created(function ($model) {
            self::registrarAccion($model, 'created');
        });

        static::updated(function ($model) {
            self::registrarAccion($model, 'updated');
        });

        static::deleted(function ($model) {
            self::registrarAccion($model, 'deleted');
        });
    }

    protected static function registrarAccion($model, $accion)
    {
        if (Auth::check()) {
            $datosAnteriores = $accion === 'updated' ? $model->getOriginal() : null;
            $datosNuevos = $accion === 'deleted' ? null : $model->getAttributes();

            BitacoraAccion::create([
                'usuario_id' => Auth::id(),
                'accion' => $accion,
                'modelo' => get_class($model),
                'modelo_id' => $model->id,
                'descripcion' => self::generarDescripcion($model, $accion),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $datosNuevos,
                'url' => Request::fullUrl(),
                'metodo' => Request::method()
            ]);
        }
    }

    protected static function generarDescripcion($model, $accion)
    {
        $nombreModelo = class_basename($model);
        $acciones = [
            'created' => 'creó',
            'updated' => 'actualizó',
            'deleted' => 'eliminó'
        ];

    return $acciones[$accion] . ' ' . $nombreModelo . ': ' . ($model->nombre ?? $model->id);

    }
}