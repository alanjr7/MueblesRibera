@extends('layouts.app')

@section('title', 'Dashboard - Soldador')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt"></i> Dashboard - Soldador
    </h1>
</div>

<!-- Productos Disponibles -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-boxes"></i> Productos Disponibles
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($productos as $producto)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($producto->imagen_url)
                            <img src="{{ asset('storage/' . $producto->imagen_url) }}" class="card-img-top" alt="{{ $producto->nombre }}" style="height: 200px; object-fit: cover;">
                            @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-box fa-3x text-muted"></i>
                            </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $producto->nombre }}</h5>
                                <p class="card-text text-muted small">{{ Str::limit($producto->descripcion, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">${{ number_format($producto->precio, 2) }}</span>
                                    <span class="badge bg-{{ $producto->stock > 10 ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }}">
                                        Stock: {{ $producto->stock }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted text-center">No hay productos disponibles</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection