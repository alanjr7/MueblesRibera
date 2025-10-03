<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\CarritoItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Listar ventas
    public function index(Request $request)
    {
        $query = Venta::with('usuario', 'detalles.producto');

        // Filtrar por usuario si no es superadmin
        if (!auth()->user()->hasRole('superadmin')) {
            $query->where('usuario_id', auth()->id());
        }

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $ventas = $query->latest()->paginate(15);

        return view('ventas.index', compact('ventas'));
    }

    // Mostrar venta
    public function show(Venta $venta)
    {
        // Verificar permisos
        if (!auth()->user()->hasRole('superadmin') && $venta->usuario_id !== auth()->id()) {
            abort(403);
        }

        $venta->load('detalles.producto', 'usuario');

        return view('ventas.show', compact('venta'));
    }

    // Procesar venta desde el carrito
    public function procesarVenta(Request $request)
    {
        $carritoItems = CarritoItem::with('producto')
            ->where('usuario_id', auth()->id())
            ->get();

        if ($carritoItems->isEmpty()) {
            return back()->with('error', 'El carrito está vacío.');
        }

        // Verificar stock de todos los productos
        foreach ($carritoItems as $item) {
            if (!$item->producto->tieneStock($item->cantidad)) {
                return back()->with('error', 
                    "El producto {$item->producto->nombre} no tiene suficiente stock.");
            }
        }

        DB::beginTransaction();

        try {
            // Crear venta
            $venta = Venta::create([
                'usuario_id' => auth()->id(),
                'total' => 0, // Se calculará después
                'estado' => 'completada'
            ]);

            $totalVenta = 0;

            // Crear detalles de venta
            foreach ($carritoItems as $item) {
                $subtotal = $item->producto->precio * $item->cantidad;

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->producto->precio,
                    'subtotal' => $subtotal
                ]);

                // Actualizar stock (se ejecuta el trigger automáticamente)
                $item->producto->decrementarStock($item->cantidad);

                $totalVenta += $subtotal;
            }

            // Actualizar total de venta
            $venta->update(['total' => $totalVenta]);

            // Vaciar carrito
            CarritoItem::where('usuario_id', auth()->id())->delete();

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', 'Venta procesada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    // Cancelar venta
    public function cancelar(Venta $venta)
    {
        if ($venta->estado !== 'completada') {
            return back()->with('error', 'Solo se pueden cancelar ventas completadas.');
        }

        DB::beginTransaction();

        try {
            // Restaurar stock
            foreach ($venta->detalles as $detalle) {
                $detalle->producto->incrementarStock($detalle->cantidad);
            }

            // Marcar como cancelada
            $venta->marcarCancelada();

            DB::commit();

            return back()->with('success', 'Venta cancelada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar la venta: ' . $e->getMessage());
        }
    }
}