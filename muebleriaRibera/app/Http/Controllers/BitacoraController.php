<?php

namespace App\Http\Controllers;

use App\Models\BitacoraLogin;
use App\Models\BitacoraAccion;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    //  public function __construct()
    // {
    //     $this->middleware('auth');
    //      $this->middleware('role:superadmin');
    //  }

    public function index(Request $request)
    {
        $acciones = BitacoraAccion::with('usuario')
            ->latest()
            ->paginate(20);

        $estadisticas = [
            'total_acciones' => BitacoraAccion::count(),
            'total_logins' => BitacoraLogin::count(),
            'logins_exitosos' => BitacoraLogin::exitosos()->count(),
            'logins_fallidos' => BitacoraLogin::fallidos()->count(),
        ];

        return view('bitacora.index', compact('acciones', 'estadisticas'));
    }

    public function logins(Request $request)
    {
        $logins = BitacoraLogin::with('usuario')
            ->latest()
            ->paginate(20);

        return view('bitacora.logins', compact('logins'));
    }

    public function acciones(Request $request)
{
    $query = BitacoraAccion::with('usuario');

    // Filtros - SOLO aplicar si tienen valor
    if ($request->filled('accion')) {
        $query->where('accion', $request->accion);
    }

    if ($request->filled('usuario_id')) {
        $query->where('usuario_id', $request->usuario_id);
    }

    // Validar que las fechas no estén vacías
    if ($request->filled('fecha_desde')) {
        $query->whereDate('created_at', '>=', $request->fecha_desde);
    }

    if ($request->filled('fecha_hasta')) {
        $query->whereDate('created_at', '<=', $request->fecha_hasta);
    }

    $acciones = $query->latest()->paginate(20);

    return view('bitacora.acciones', compact('acciones'));
}
    // Helper function para colores de badges
    private function getBadgeColor($accion)
    {
        $colores = [
            'created' => 'success',
            'updated' => 'warning', 
            'deleted' => 'danger',
            'login' => 'info',
            'logout' => 'secondary',
            'stock_ajustado' => 'primary',
            'login_attempt' => 'light',
            'login_failed_inactive' => 'dark',
            'login_success' => 'success'
        ];
        
        return $colores[$accion] ?? 'secondary';
    }
}