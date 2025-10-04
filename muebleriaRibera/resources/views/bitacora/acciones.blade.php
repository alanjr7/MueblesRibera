@extends('layouts.app')

@section('title', 'Todas las Acciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-cogs"></i> Todas las Acciones
    </h1>
    <a href="{{ route('bitacora.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Resumen
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('bitacora.acciones') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="accion" class="form-select">
                    <option value="">Todas las acciones</option>
                    <option value="created" {{ request('accion') == 'created' ? 'selected' : '' }}>Creaciones</option>
                    <option value="updated" {{ request('accion') == 'updated' ? 'selected' : '' }}>Actualizaciones</option>
                    <option value="deleted" {{ request('accion') == 'deleted' ? 'selected' : '' }}>Eliminaciones</option>
                    <option value="login" {{ request('accion') == 'login' ? 'selected' : '' }}>Logins</option>
                    <option value="logout" {{ request('accion') == 'logout' ? 'selected' : '' }}>Logouts</option>
                    <option value="stock_ajustado" {{ request('accion') == 'stock_ajustado' ? 'selected' : '' }}>Ajustes de Stock</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Modelo</th>
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
                        <td>
                            @if($accion->modelo)
                            <small class="text-muted">{{ class_basename($accion->modelo) }}</small>
                            @endif
                        </td>
                        <td>{{ $accion->descripcion }}</td>
                        <td><code>{{ $accion->ip_address }}</code></td>
                        <td>{{ $accion->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay acciones registradas</td>
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