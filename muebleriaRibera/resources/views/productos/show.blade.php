@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fas fa-box"></i> {{ $producto->nombre }}
                </h4>
                <div class="btn-group">
                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($producto->imagen_url)
                        <img src="{{ asset('storage/' . $producto->imagen_url) }}" alt="{{ $producto->nombre }}" 
                             class="img-fluid rounded">
                        @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="height: 300px;">
                            <i class="fas fa-box fa-5x text-muted"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Nombre:</th>
                                <td>{{ $producto->nombre }}</td>
                            </tr>
                            <tr>
                                <th>Categoría:</th>
                                <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td>${{ number_format($producto->precio, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Stock:</th>
                                <td>
                                    <span class="badge bg-{{ $producto->stock > 10 ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }}">
                                        {{ $producto->stock }} unidades
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-{{ $producto->activo ? 'success' : 'secondary' }}">
                                        {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha Creación:</th>
                                <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Última Actualización:</th>
                                <td>{{ $producto->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>

                        @if($producto->descripcion)
                        <div class="mt-3">
                            <h5>Descripción:</h5>
                            <p class="text-muted">{{ $producto->descripcion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection