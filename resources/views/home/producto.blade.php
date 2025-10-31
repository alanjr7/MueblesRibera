@extends('layouts.public')

@section('title', $producto->nombre . ' - Tienda Online')

@section('content')
<div class="container py-5">
    <!-- Migas de pan -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalogo') }}">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $producto->nombre }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Imagen del producto -->
        <div class="col-md-6 mb-4">
            @if($producto->imagen_url)
            <img src="{{ asset('storage/' . $producto->imagen_url) }}" 
                 alt="{{ $producto->nombre }}" 
                 class="img-fluid rounded shadow-sm">
            @else
            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                 style="height: 400px;">
                <i class="fas fa-box fa-5x text-muted"></i>
            </div>
            @endif
        </div>

        <!-- Información del producto -->
        <div class="col-md-6">
            <h1 class="h2 mb-3">{{ $producto->nombre }}</h1>
            
            @if($producto->categoria_nombre)
            <p class="text-muted mb-3">
                <i class="fas fa-tag"></i> Categoría: {{ $producto->categoria_nombre }}
            </p>
            @endif

            <div class="mb-4">
                <span class="h3 text-primary">${{ number_format($producto->precio, 2) }}</span>
            </div>

            <div class="mb-4">
                <span class="badge bg-{{ $producto->stock > 10 ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }} fs-6">
                    @if($producto->stock > 10)
                        <i class="fas fa-check"></i> En stock ({{ $producto->stock }} disponibles)
                    @elseif($producto->stock > 0)
                        <i class="fas fa-exclamation-triangle"></i> Últimas unidades ({{ $producto->stock }} disponibles)
                    @else
                        <i class="fas fa-times"></i> Sin stock
                    @endif
                </span>
            </div>

            @if($producto->descripcion)
            <div class="mb-4">
                <h5 class="mb-3">Descripción</h5>
                <p class="text-muted">{{ $producto->descripcion }}</p>
            </div>
            @endif

            <!-- Botones de acción -->
            <div class="d-grid gap-2 d-md-flex">
                @auth
                    @if($producto->stock > 0)
                    <form action="{{ route('carrito.store') }}" method="POST" class="me-md-2">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                        <input type="hidden" name="cantidad" value="1">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </form>
                    @else
                    <button class="btn btn-secondary btn-lg" disabled>
                        <i class="fas fa-cart-plus"></i> Sin Stock
                    </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión para Comprar
                    </a>
                @endauth
                
                <a href="{{ route('catalogo') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Seguir Comprando
                </a>
            </div>
        </div>
    </div>

    <!-- Productos relacionados -->
    @if($productosRelacionados && $productosRelacionados->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Productos Relacionados</h3>
            <div class="row">
                @foreach($productosRelacionados as $productoRel)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        @if($productoRel->imagen_url)
                        <img src="{{ asset('storage/' . $productoRel->imagen_url) }}" 
                             class="card-img-top" 
                             alt="{{ $productoRel->nombre }}" 
                             style="height: 200px; object-fit: cover;">
                        @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-box fa-3x text-muted"></i>
                        </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $productoRel->nombre }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($productoRel->descripcion, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">${{ number_format($productoRel->precio, 2) }}</span>
                                <span class="badge bg-{{ $productoRel->stock > 10 ? 'success' : ($productoRel->stock > 0 ? 'warning' : 'danger') }}">
                                    {{ $productoRel->stock }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-grid gap-2">
                                <a href="{{ route('producto.show', $productoRel->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection