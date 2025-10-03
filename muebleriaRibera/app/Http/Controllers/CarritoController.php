<?php

namespace App\Http\Controllers;

use App\Models\CarritoItem;
use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Ver carrito
    public function index()
    {
        $carritoItems = CarritoItem::with('producto')
            ->where('usuario_id', auth()->id())
            ->get();

        $total = $carritoItems->sum(function ($item) {
            return $item->subtotal;
        });

        return view('carrito.index', compact('carritoItems', 'total'));
    }

    // Agregar producto al carrito
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        // Verificar stock
        if (!$producto->tieneStock($request->cantidad)) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        // Verificar si ya está en el carrito
        $carritoItem = CarritoItem::where('usuario_id', auth()->id())
            ->where('producto_id', $request->producto_id)
            ->first();

        if ($carritoItem) {
            // Actualizar cantidad
            $nuevaCantidad = $carritoItem->cantidad + $request->cantidad;
            
            if (!$producto->tieneStock($nuevaCantidad)) {
                return back()->with('error', 'No hay suficiente stock disponible.');
            }

            $carritoItem->update(['cantidad' => $nuevaCantidad]);
        } else {
            // Crear nuevo item
            CarritoItem::create([
                'usuario_id' => auth()->id(),
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }

    // Actualizar cantidad en carrito
    public function update(Request $request, CarritoItem $carritoItem)
    {
        $this->authorize('update', $carritoItem);

        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        // Verificar stock
        if (!$carritoItem->producto->tieneStock($request->cantidad)) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        $carritoItem->update(['cantidad' => $request->cantidad]);

        return back()->with('success', 'Carrito actualizado.');
    }

    // Eliminar producto del carrito
    public function destroy(CarritoItem $carritoItem)
    {
        $this->authorize('delete', $carritoItem);

        $carritoItem->delete();

        return back()->with('success', 'Producto removido del carrito.');
    }

    // Vaciar carrito
    public function vaciar()
    {
        CarritoItem::where('usuario_id', auth()->id())->delete();

        return back()->with('success', 'Carrito vaciado.');
    }
}