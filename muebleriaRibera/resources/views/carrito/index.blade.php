@extends('layouts.app')

@section('title', 'Mi Carrito de Compras')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">
        <i class="fas fa-shopping-cart"></i> Mi Carrito de Compras
    </h1>

    @if($carritoItems->count() > 0)
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @foreach($carritoItems as $item)
                    <div class="row align-items-center mb-4 pb-4 border-bottom">
                        <div class="col-md-2">
                            @if($item->producto->imagen_url)
                            <img src="{{ asset('storage/' . $item->producto->imagen_url) }}" 
                                 alt="{{ $item->producto->nombre }}" 
                                 class="img-fluid rounded">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="height: 80px;">
                                <i class="fas fa-box text-muted"></i>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <h5 class="mb-1">{{ $item->producto->nombre }}</h5>
                            <p class="text-muted small mb-0">{{ $item->precio_unitario_formateado }} c/u</p>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <form action="{{ route('carrito.update', $item) }}" method="POST" class="d-flex">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="cantidad" value="{{ $item->cantidad }}" 
                                           min="1" max="{{ $item->producto->stock }}" 
                                           class="form-control" style="width: 80px;">
                                    <button type="submit" class="btn btn-outline-primary ms-2">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <strong>{{ $item->subtotal_formateado }}</strong>
                        </div>
                        
                        <div class="col-md-1">
                            <form action="{{ route('carrito.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resumen del Pedido</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>Bs {{ number_format($total, 2) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total:</span>
                        <strong class="h5 text-primary">Bs {{ number_format($total, 2) }}</strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <!-- Botón para iniciar chat con vendedor -->
                        <form action="{{ route('chats.crear-desde-carrito') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-comments"></i> Iniciar Chat con Vendedor
                            </button>
                        </form>
                        
                        <form action="{{ route('carrito.vaciar') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> Vaciar Carrito
                            </button>
                        </form>
                        
                        <a href="{{ route('catalogo') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
        <h3>Tu carrito está vacío</h3>
        <p class="text-muted mb-4">Agrega algunos productos para comenzar a comprar.</p>
        <a href="{{ route('catalogo') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-store"></i> Ir al Catálogo
        </a>
    </div>
    @endif
</div>
@endsection