<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Consulta directa sin Eloquent para evitar problemas
        $productosDestacados = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*', 'categorias.nombre as categoria_nombre')
            ->where('productos.activo', true)
            ->where('productos.stock', '>', 0)
            ->orderBy(DB::raw('RANDOM()'))
            ->limit(8)
            ->get();

        // Consulta directa para categorías también
        $categorias = DB::table('categorias')
            ->select('categorias.*', 
                DB::raw('(SELECT COUNT(*) FROM productos WHERE productos.categoria_id = categorias.id AND productos.activo = TRUE) as productos_count')
            )
            ->where('categorias.activo', true)
            ->get();

        return view('home.index', compact('productosDestacados', 'categorias'));
    }

    public function catalogo(Request $request)
    {
        $query = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*', 'categorias.nombre as categoria_nombre')
            ->where('productos.activo', true)
            ->where('productos.stock', '>', 0);

        // Filtros
        if ($request->has('categoria_id')) {
            $query->where('productos.categoria_id', $request->categoria_id);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('productos.nombre', 'LIKE', "%{$request->search}%")
                  ->orWhere('productos.descripcion', 'LIKE', "%{$request->search}%")
                  ->orWhere('categorias.nombre', 'LIKE', "%{$request->search}%");
            });
        }

        $productos = $query->orderBy('productos.nombre')
                          ->paginate(12);

        $categorias = DB::table('categorias')
            ->where('activo', true)
            ->get();

        return view('home.catalogo', compact('productos', 'categorias'));
    }

    public function verProducto($id)
    {
        $producto = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*', 'categorias.nombre as categoria_nombre')
            ->where('productos.activo', true)
            ->where('productos.id', $id)
            ->first();

        if (!$producto) {
            abort(404);
        }

        // Productos relacionados
        $productosRelacionados = DB::table('productos')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select('productos.*', 'categorias.nombre as categoria_nombre')
            ->where('productos.activo', true)
            ->where('productos.categoria_id', $producto->categoria_id)
            ->where('productos.id', '!=', $producto->id)
            ->where('productos.stock', '>', 0)
            ->orderBy(DB::raw('RANDOM()'))

            ->limit(4)
            ->get();

        return view('home.producto', compact('producto', 'productosRelacionados'));
    }

    public function contacto()
    {
        return view('home.contacto');
    }
}