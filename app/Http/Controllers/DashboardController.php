<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\User;
use App\Models\BitacoraLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Dashboard principal según rol
    public function index()
    {
        $user = auth()->user();
        
        switch ($user->rol->nombre) {
            case 'superadmin':
                return $this->adminDashboard();
            case 'vendedor':
                return $this->vendedorDashboard();
            case 'soldador':
                return $this->soldadorDashboard();
            default:
                return $this->defaultDashboard();
        }
    }

    // Dashboard específico para admin
    public function admin()
    {
        return $this->adminDashboard();
    }

    // Dashboard específico para vendedor
    public function vendedor()
    {
        return $this->vendedorDashboard();
    }

    // Dashboard específico para soldador
    public function soldador()
    {
        return $this->soldadorDashboard();
    }

    // Dashboard para superadmin
    private function adminDashboard()
    {
        $stats = [
            'total_usuarios' => User::count(),
            'total_productos' => Producto::count(),
            'ventas_hoy' => Venta::whereDate('created_at', today())->count(),
            'ingresos_hoy' => Venta::whereDate('created_at', today())->sum('total') ?? 0,
        ];

        $ventas_recientes = Venta::with('usuario')
            ->latest()
            ->take(5)
            ->get();

        $productos_bajo_stock = Producto::where('stock', '<', 10)
            ->where('activo', true)
            ->get();

        return view('dashboard.admin', compact('stats', 'ventas_recientes', 'productos_bajo_stock'));
    }

    // Dashboard para vendedor
    private function vendedorDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'ventas_hoy' => Venta::where('usuario_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'ingresos_hoy' => Venta::where('usuario_id', $user->id)
                ->whereDate('created_at', today())
                ->sum('total') ?? 0,
            'productos_vendidos' => DB::table('venta_detalles')
                ->join('ventas', 'venta_detalles.venta_id', '=', 'ventas.id')
                ->where('ventas.usuario_id', $user->id)
                ->whereDate('ventas.created_at', today())
                ->sum('venta_detalles.cantidad') ?? 0,
        ];

        $mis_ventas = Venta::where('usuario_id', $user->id)
            ->with('detalles.producto')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.vendedor', compact('stats', 'mis_ventas'));
    }

    // Dashboard para soldador
    private function soldadorDashboard()
    {
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->get();

        return view('dashboard.soldador', compact('productos'));
    }

    // Dashboard por defecto
    private function defaultDashboard()
    {
        $user = auth()->user();
        return view('dashboard.index', compact('user'));
    }
}