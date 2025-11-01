@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-boxes"></i> Gestión de Productos
    </h1>
    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Buscar productos..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Productos -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr>
                        <td>
                       @if($producto->imagen_url)
                            <img src="{{ Storage::url($producto->imagen_url) }}" 
                                alt="{{ $producto->nombre }}" 
                                class="img-fluid rounded" 
                                style="max-height: 60px; width: auto; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" 
                                alt="Sin imagen" 
                                class="img-fluid rounded bg-light" 
                                style="max-height: 60px; width: 60px; object-fit: contain;">
                        @endif
                        </td>
                        <td>
                            <strong>{{ $producto->nombre }}</strong>
                            @if($producto->descripcion)
                            <br><small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td>${{ number_format($producto->precio, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $producto->stock > 10 ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }}">
                                {{ $producto->stock }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $producto->activo ? 'success' : 'secondary' }}">
                                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('productos.show', $producto) }}" class="btn btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" 
                                        data-bs-target="#stockModal{{ $producto->id }}" title="Ajustar Stock">
                                    <i class="fas fa-boxes"></i>
                                </button>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('¿Estás seguro de eliminar este producto?')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Modal para ajustar stock -->
                            <div class="modal fade" id="stockModal{{ $producto->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ajustar Stock - {{ $producto->nombre }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('productos.ajustar-stock', $producto) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Tipo de Movimiento</label>
                                                    <select name="tipo_movimiento" class="form-select" required>
                                                        <option value="entrada">Entrada (Aumentar stock)</option>
                                                        <option value="salida">Salida (Disminuir stock)</option>
                                                        <option value="ajuste">Ajuste (Corregir stock)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Cantidad</label>
                                                    <input type="number" name="cantidad" class="form-control" 
                                                           min="1" required value="1">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Observaciones (Opcional)</label>
                                                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Aplicar Ajuste</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No se encontraron productos</p>
                            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primer Producto
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $productos->links() }}
        </div>
    </div>
</div>
@endsection