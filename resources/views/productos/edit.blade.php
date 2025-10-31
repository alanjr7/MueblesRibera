@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-edit"></i> Editar Producto: {{ $producto->nombre }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Producto *</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoría *</label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                        id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ (old('categoria_id', $producto->categoria_id) == $categoria->id) ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('precio') is-invalid @enderror" 
                                           id="precio" name="precio" value="{{ old('precio', $producto->precio) }}" min="0" required>
                                </div>
                                @error('precio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Stock Actual</label>
                                <input type="text" class="form-control" value="{{ $producto->stock }}" readonly>
                                <small class="form-text text-muted">
                                    Para ajustar stock, use el botón "Ajustar Stock" en la lista de productos
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Imagen actual -->
                    @if($producto->imagen_url)
                    <div class="mb-3">
                        <label class="form-label">Imagen Actual</label>
                        <div>
                            <img src="{{ asset('storage/' . $producto->imagen_url) }}" alt="{{ $producto->nombre }}" 
                                 class="img-thumbnail" style="max-height: 200px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminar_imagen" value="1">
                                <label class="form-check-label" for="eliminar_imagen">
                                    Eliminar imagen actual
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="imagen" class="form-label">
                            {{ $producto->imagen_url ? 'Nueva Imagen' : 'Imagen del Producto' }}
                        </label>
                        <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                               id="imagen" name="imagen" accept="image/*">
                        <div class="form-text">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</div>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                               {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Producto activo</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection