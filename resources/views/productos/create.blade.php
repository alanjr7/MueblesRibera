@extends('layouts.app')

@section('title', 'Crear Nuevo Producto')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-plus"></i> Crear Nuevo Producto
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Producto *</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" required>
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
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                                  id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SECCIÓN DE PRECIOS CON CONVERSIÓN -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-dollar-sign"></i> Información de Precios
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="precio_usd" class="form-label">Precio en USD *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control @error('precio_usd') is-invalid @enderror" 
                                                   id="precio_usd" name="precio_usd" value="{{ old('precio_usd') }}" min="0" required>
                                        </div>
                                        @error('precio_usd')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tasa_cambio" class="form-label">Tasa de Cambio (Bs por $1) *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Bs</span>
                                            <input type="number" step="0.0001" class="form-control @error('tasa_cambio') is-invalid @enderror" 
                                                   id="tasa_cambio" name="tasa_cambio" value="{{ old('tasa_cambio', 6.96) }}" min="0" required>
                                        </div>
                                        <div class="form-text">Tasa actual del dólar en bolivianos</div>
                                        @error('tasa_cambio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Mostrar precio calculado en tiempo real -->
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Precio en Dólares:</strong>
                                        <div id="precio_usd_calculado">$0.00 USD</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Precio en Bolivianos:</strong>
                                        <div id="precio_bs_calculado">Bs 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock Inicial *</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                       id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Producto</label>
                        <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                               id="imagen" name="imagen" accept="image/*">
                        <div class="form-text">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</div>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
<!-- cambiando a local -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                               {{ old('activo', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Producto activo</label>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const precioUsdInput = document.getElementById('precio_usd');
    const tasaCambioInput = document.getElementById('tasa_cambio');
    const precioUsdCalculado = document.getElementById('precio_usd_calculado');
    const precioBsCalculado = document.getElementById('precio_bs_calculado');

    function calcularPrecios() {
        const usd = parseFloat(precioUsdInput.value) || 0;
        const tasa = parseFloat(tasaCambioInput.value) || 6.96;
        const precioBs = usd * tasa;
        
        precioUsdCalculado.textContent = '$' + usd.toFixed(2) + ' USD';
        precioBsCalculado.textContent = 'Bs ' + precioBs.toFixed(2);
    }

    precioUsdInput.addEventListener('input', calcularPrecios);
    tasaCambioInput.addEventListener('input', calcularPrecios);
    
    // Calcular inicialmente
    calcularPrecios();
});
</script>
@endpush
@endsection