@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </h1>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-user fa-4x text-primary mb-3"></i>
                <h3>Bienvenido, {{ $user->nombre }}!</h3>
                <p class="text-muted">Rol: {{ $user->rol->nombre }}</p>
                <p>Has ingresado al sistema de gestión de la tienda.</p>
                
                <div class="mt-4">
                    @if($user->hasRole('superadmin'))
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-cogs"></i> Ir al Panel de Administración
                        </a>
                    @elseif($user->hasRole('vendedor'))
                        <a href="{{ route('vendedor.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-chart-line"></i> Ir al Panel de Ventas
                        </a>
                    @elseif($user->hasRole('soldador'))
                        <a href="{{ route('soldador.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tools"></i> Ir al Panel de Producción
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection