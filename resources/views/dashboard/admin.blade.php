@extends('layouts.app')

@section('title', 'Dashboard - SuperAdmin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt"></i> Dashboard - SuperAdmin
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
                            Total Usuarios
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_usuarios'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Total Productos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_productos'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
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
                            Ventas Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ventas_hoy'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            Ingresos Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['ingresos_hoy'], 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ventas Recientes -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history"></i> Ventas Recientes
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Vendedor</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventas_recientes as $venta)
                            <tr>
                                <td>#{{ $venta->id }}</td>
                                <td>{{ $venta->usuario->nombre }}</td>
                                <td>${{ number_format($venta->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $venta->estado == 'completada' ? 'success' : ($venta->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($venta->estado) }}
                                    </span>
                                </td>
                                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay ventas recientes</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con Stock Bajo -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                </h6>
            </div>
            <div class="card-body">
                @forelse($productos_bajo_stock as $producto)
                <div class="alert alert-warning py-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ $producto->nombre }}</span>
                        <span class="badge bg-danger">{{ $producto->stock }}</span>
                    </div>
                </div>
                @empty
                <p class="text-muted">No hay productos con stock bajo</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection