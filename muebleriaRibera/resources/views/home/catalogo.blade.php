@extends('layouts.public')

@section('title', 'Catálogo de Productos - Tienda Online')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">Catálogo de Productos</h1>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('catalogo') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Buscar productos..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="categoria_id" class="form-select">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="row">
        @forelse($productos as $producto)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card product-card h-100">
                @if($producto->imagen_url)
                <img src="{{ asset('storage/' . $producto->imagen_url) }}" 
                     class="card-img-top" 
                     alt="{{ $producto->nombre }}" 
                     style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                     style="height: 200px;">
                    <i class="fas fa-box fa-3x text-muted"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $producto->nombre }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($producto->descripcion, 80) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 text-primary mb-0">${{ number_format($producto->precio, 2) }}</span>
                        <span class="badge bg-{{ $producto->stock > 10 ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }}">
                            {{ $producto->stock }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-grid gap-2">
                        <a href="{{ route('producto.show', $producto->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        @auth
                        <form action="{{ route('carrito.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" class="btn btn-primary btn-sm w-100" 
                                    {{ $producto->stock == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                            </button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión para Comprar
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>No se encontraron productos</h4>
                <p class="text-muted">Intenta con otros términos de búsqueda o categorías.</p>
                <a href="{{ route('catalogo') }}" class="btn btn-primary">
                    <i class="fas fa-refresh"></i> Ver Todos los Productos
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($productos->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $productos->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection