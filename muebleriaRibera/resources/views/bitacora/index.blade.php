@extends('layouts.app')

@section('title', 'Bitácora del Sistema')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-clipboard-list"></i> Bitácora del Sistema
    </h1>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Acciones
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total_acciones'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-history fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Logins Exitosos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['logins_exitosos'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Logins Fallidos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['logins_fallidos'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Logins
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total_logins'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navegación -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('bitacora.index') }}">
            <i class="fas fa-home"></i> Resumen
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('bitacora.acciones') }}">
            <i class="fas fa-cogs"></i> Todas las Acciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('bitacora.logins') }}">
            <i class="fas fa-sign-in-alt"></i> Logins
        </a>
    </li>
</ul>

<!-- Últimas Acciones -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-clock"></i> Últimas Acciones
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>IP</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acciones as $accion)
                    <tr>
                        <td>
                            <strong>{{ $accion->usuario->nombre }}</strong>
                            <br><small class="text-muted">{{ $accion->usuario->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ getBadgeColor($accion->accion) }}">
                                {{ $accion->accion_formateada ?? $accion->accion }}
                            </span>
                        </td>
                        <td>{{ $accion->descripcion }}</td>
                        <td><code>{{ $accion->ip_address }}</code></td>
                        <td>{{ $accion->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay acciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $acciones->links() }}
        </div>
    </div>
</div>
@endsection

@php
function getBadgeColor($accion) {
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
}
@endphp