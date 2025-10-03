<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\InventarioMov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:superadmin,vendedor')->except(['show']);
    // }

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
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // Manejar imagen
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('productos', 'public');
                $validated['imagen_url'] = $path;
            }

            $producto = Producto::create($validated);

            // Registrar movimiento de inventario inicial si hay stock
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
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean',
            'eliminar_imagen' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // Manejar eliminación de imagen
            if ($request->has('eliminar_imagen') && $producto->imagen_url) {
                Storage::disk('public')->delete($producto->imagen_url);
                $validated['imagen_url'] = null;
            }

            // Manejar nueva imagen
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($producto->imagen_url) {
                    Storage::disk('public')->delete($producto->imagen_url);
                }
                $path = $request->file('imagen')->store('productos', 'public');
                $validated['imagen_url'] = $path;
            }

            $producto->update($validated);

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    // Eliminar producto
    public function destroy(Producto $producto)
    {
        DB::beginTransaction();

        try {
            // Eliminar imagen si existe
            if ($producto->imagen_url) {
                Storage::disk('public')->delete($producto->imagen_url);
            }

            $producto->delete();

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
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

            // Registrar movimiento
            InventarioMov::create([
                'producto_id' => $producto->id,
                'tipo_movimiento' => $tipo,
                'cantidad' => $cantidad,
                'usuario_id' => auth()->id(),
                'observaciones' => $request->observaciones
            ]);

            DB::commit();

            return back()->with('success', "Stock ajustado exitosamente. Nuevo stock: {$producto->stock}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al ajustar el stock: ' . $e->getMessage());
        }
    }
}