<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\InventarioMov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\BitacoraAccion;
class ProductoController extends Controller
{
    // Listar productos
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        // Búsqueda
        if ($request->has('search')) {
            $query->where('nombre', 'LIKE', "%{$request->search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$request->search}%");
        }

        // Filtro por categoría
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por estado
        if ($request->has('estado')) {
            if ($request->estado == 'activo') {
                $query->where('activo', true);
            } elseif ($request->estado == 'inactivo') {
                $query->where('activo', false);
            }
        }

        $productos = $query->latest()->paginate(10);
        $categorias = Categoria::where('activo', true)->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $categorias = Categoria::where('activo', true)->get();
        return view('productos.create', compact('categorias'));
    }

    // Almacenar nuevo producto
   public function store(Request $request)
        {
            $validated = $request->validate([
                'nombre' => 'required|string|max:200',
                'descripcion' => 'nullable|string',
                'precio_usd' => 'required|numeric|min:0',
                'tasa_cambio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'categoria_id' => 'required|exists:categorias,id',
                'imagen' => 'nullable|image|max:2048',
                'activo' => 'boolean'
            ]);

            DB::beginTransaction();

            try {
                // Calcular precio en bolivianos
                $validated['precio'] = $validated['precio_usd'] * $validated['tasa_cambio'];

                // Manejar imagen - CORREGIDO
                if ($request->hasFile('imagen')) {
                    // Obtener el archivo
                    $imagen = $request->file('imagen');
                    
                    // Generar nombre único
                    $nombreImagen = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
                    
                    // Guardar en storage público
                    $path = $imagen->storeAs('productos', $nombreImagen, 'public');
                    
                    // Guardar la ruta completa para acceso web
                    $validated['imagen_url'] = 'storage/' . $path;
                }

                $producto = Producto::create($validated);

                // Registrar movimiento de inventario
                if ($validated['stock'] > 0) {
                    InventarioMov::create([
                        'producto_id' => $producto->id,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $validated['stock'],
                        'usuario_id' => auth()->id(),
                        'observaciones' => 'Stock inicial'
                    ]);
                }

                DB::commit();

                return redirect()->route('productos.index')
                    ->with('success', 'Producto creado exitosamente.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error al crear producto: ' . $e->getMessage());
                return back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
            }
        }

    // Mostrar producto
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    // Mostrar formulario de edición
    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('activo', true)->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    // Actualizar producto
   public function update(Request $request, Producto $producto)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:200',
        'descripcion' => 'nullable|string',
        'precio_usd' => 'required|numeric|min:0',
        'tasa_cambio' => 'required|numeric|min:0',
        'categoria_id' => 'required|exists:categorias,id',
        'imagen' => 'nullable|image|max:2048',
        'activo' => 'boolean',
        'eliminar_imagen' => 'boolean'
    ]);

    DB::beginTransaction();

    try {
        $validated['precio'] = $validated['precio_usd'] * $validated['tasa_cambio'];

        // Manejar eliminación de imagen
        if ($request->has('eliminar_imagen') && $producto->imagen_url) {
            // Eliminar del storage
            $rutaArchivo = str_replace('storage/', '', $producto->imagen_url);
            Storage::disk('public')->delete($rutaArchivo);
            $validated['imagen_url'] = null;
        }

        // Manejar nueva imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen_url) {
                $rutaArchivo = str_replace('storage/', '', $producto->imagen_url);
                Storage::disk('public')->delete($rutaArchivo);
            }
            
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            $path = $imagen->storeAs('productos', $nombreImagen, 'public');
            $validated['imagen_url'] = 'storage/' . $path;
        }

        $producto->update($validated);

        DB::commit();

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error al actualizar producto: ' . $e->getMessage());
        return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
    }
}

    // Eliminar producto
    public function destroy(Producto $producto)
{
    DB::beginTransaction();

    try {
        // Primero eliminar registros relacionados
        \App\Models\InventarioMov::where('producto_id', $producto->id)->delete();
        \App\Models\VentaDetalle::where('producto_id', $producto->id)->delete();
        \App\Models\CarritoItem::where('producto_id', $producto->id)->delete();

        // Luego eliminar la imagen si existe
        if ($producto->imagen_url) {
            Storage::disk('public')->delete($producto->imagen_url);
        }

        // Finalmente eliminar el producto
        $producto->delete();

        DB::commit();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error al eliminar producto:', [
            'producto_id' => $producto->id,
            'error' => $e->getMessage()
        ]);
        return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
    }
}
    // Ajustar stock
    
public function ajustarStock(Request $request, Producto $producto)
{
    $request->validate([
        'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
        'cantidad' => 'required|integer|min:1',
        'observaciones' => 'nullable|string|max:500'
    ]);

    DB::beginTransaction();

    try {
        $cantidad = $request->cantidad;
        $tipo = $request->tipo_movimiento;
        $stockAnterior = $producto->stock;

        // Validar stock para salidas
        if ($tipo === 'salida' && $producto->stock < $cantidad) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        // Actualizar stock
        if ($tipo === 'entrada') {
            $producto->increment('stock', $cantidad);
        } elseif ($tipo === 'salida') {
            $producto->decrement('stock', $cantidad);
        } else { // ajuste
            $producto->update(['stock' => $cantidad]);
        }

        // Registrar movimiento de inventario
        InventarioMov::create([
            'producto_id' => $producto->id,
            'tipo_movimiento' => $tipo,
            'cantidad' => $cantidad,
            'usuario_id' => auth()->id(),
            'observaciones' => $request->observaciones
        ]);

        // Registrar en bitácora de acciones
        BitacoraAccion::create([
            'usuario_id' => auth()->id(),
            'accion' => 'stock_ajustado',
            'modelo' => Producto::class,
            'modelo_id' => $producto->id,
            'descripcion' => "Ajustó stock de {$producto->nombre} de {$stockAnterior} a {$producto->stock} ({$tipo})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'datos_anteriores' => ['stock' => $stockAnterior],
            'datos_nuevos' => ['stock' => $producto->stock],
            'url' => $request->fullUrl(),
            'metodo' => $request->method()
        ]);

        DB::commit();

        return back()->with('success', "Stock ajustado exitosamente. Nuevo stock: {$producto->stock}");

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al ajustar el stock: ' . $e->getMessage());
    }
}

    // Actualizar tasa de cambio global
    public function actualizarTasaGlobal(Request $request)
    {
        $request->validate([
            'nueva_tasa' => 'required|numeric|min:0'
        ]);

        try {
            $productosActualizados = Producto::actualizarTasaGlobal($request->nueva_tasa);

            return redirect()->route('productos.index')
                ->with('success', "Tasa de cambio actualizada a Bs {$request->nueva_tasa} para {$productosActualizados} productos.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar la tasa de cambio: ' . $e->getMessage());
        }
    }
}