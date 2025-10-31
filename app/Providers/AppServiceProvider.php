<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compartir la función de colores con todas las vistas
        View::share('getBadgeColor', function ($accion) {
            $colores = [
                'created' => 'success',
                'updated' => 'warning',
                'deleted' => 'danger',
                'login' => 'info',
                'logout' => 'secondary',
                'stock_ajustado' => 'primary',
                'login_attempt' => 'light',
                'login_failed_inactive' => 'dark',
                'login_success' => 'success'
            ];
            return $colores[$accion] ?? 'secondary';
        });
    }
}