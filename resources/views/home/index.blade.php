@extends('layouts.public')

@section('title', 'Inicio - Tienda Online')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Bienvenido a Nuestra Tienda</h1>
        <p class="lead mb-4">Descubre los mejores productos con la mejor calidad y precio.</p>
        <a href="{{ route('catalogo') }}" class="btn btn-light btn-lg">
            <i class="fas fa-shopping-bag"></i> Ver Catálogo
        </a>
    </div>
</section>

<!-- Productos Destacados -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Productos Destacados</h2>
        <div class="row">
            @foreach($productosDestacados as $producto)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card product-card h-100">
                    @if($producto->imagen_url)
                    <img src="{{ asset('storage/' . $producto->imagen_url) }}" class="card-img-top" alt="{{ $producto->nombre }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-box fa-3x text-muted"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $producto->nombre }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($producto->descripcion, 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">${{ number_format($producto->precio, 2) }}</span>
                            <span class="badge bg-{{ $producto->stock > 10 ? 'success' : 'warning' }}">
                                {{ $producto->stock }} en stock
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
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('catalogo') }}" class="btn btn-outline-primary">
                Ver Todos los Productos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Categorías -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Nuestras Categorías</h2>
        <div class="row">
            @foreach($categorias as $categoria)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $categoria->nombre }}</h5>
                        <p class="card-text">{{ $categoria->descripcion }}</p>
                        <span class="badge bg-primary">{{ $categoria->productos_count }} productos</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection