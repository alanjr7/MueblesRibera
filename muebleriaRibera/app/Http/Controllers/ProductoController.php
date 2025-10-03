<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\InventarioMov;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Listar productos
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        // Búsqueda
        if ($request->has('search')) {
            $query->buscar($request->search);
        }

        // Filtro por categoría
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por stock
        if ($request->has('stock')) {
            if ($request->stock == 'bajo') {
                $query->where('stock', '<', 10);
            } elseif ($request->stock == 'sin') {
                $query->where('stock', 0);
            }
        }

        $productos = $query->latest()->paginate(12);
        $categorias = Categoria::activo()->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $categorias = Categoria::activo()->get();
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
            'imagen' => 'nullable|image|max:2048'
        ]);

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen_url'] = $path;
        }

        $producto = Producto::create($validated);

        // Registrar movimiento de inventario inicial
        if ($validated['stock'] > 0) {
            InventarioMov::create([
                'producto_id' => $producto->id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => $validated['stock'],
                'usuario_id' => auth()->id()
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    // Mostrar producto
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    // Mostrar formulario de edición
    public function edit(Producto $producto)
    {
        $categorias = Categoria::activo()->get();
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
            'activo' => 'boolean'
        ]);

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen_url) {
                Storage::disk('public')->delete($producto->imagen_url);
            }
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen_url'] = $path;
        }

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    // Eliminar producto (soft delete)
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    // Ajustar stock
    public function ajustarStock(Request $request, Producto $producto)
    {
        $request->validate([
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string'
        ]);

        $cantidad = $request->tipo_movimiento === 'salida' 
            ? -$request->cantidad 
            : $request->cantidad;

        // Actualizar stock
        if ($request->tipo_movimiento === 'entrada') {
            $producto->incrementarStock($request->cantidad);
        } else {
            $producto->decrementarStock($request->cantidad);
        }

        // Registrar movimiento
        InventarioMov::create([
            'producto_id' => $producto->id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'cantidad' => $request->cantidad,
            'usuario_id' => auth()->id()
        ]);

        return back()->with('success', 'Stock ajustado exitosamente.');
    }
}