<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ==================== PÁGINA PÚBLICA ====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalogo', [HomeController::class, 'catalogo'])->name('catalogo');
Route::get('/producto/{id}', [HomeController::class, 'verProducto'])->name('producto.show');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');

// ==================== AUTENTICACIÓN ====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ÁREA PRIVADA (DASHBOARD) ====================
Route::middleware(['auth'])->group(function () {
    // Dashboard principal según rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboards específicos por rol
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard')
         ->middleware('role:superadmin');
    
    Route::get('/vendedor/dashboard', [DashboardController::class, 'vendedor'])->name('vendedor.dashboard')
         ->middleware('role:vendedor');
    
    Route::get('/soldador/dashboard', [DashboardController::class, 'soldador'])->name('soldador.dashboard')
         ->middleware('role:soldador');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de productos (superadmin y vendedor)
    Route::middleware(['role:superadmin,vendedor'])->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::post('productos/{producto}/ajustar-stock', [ProductoController::class, 'ajustarStock'])
             ->name('productos.ajustar-stock');
    });

    // Bitácoras (solo superadmin)
        Route::middleware(['role:superadmin'])->group(function () {
            Route::prefix('bitacora')->group(function () {
                Route::get('/', [BitacoraController::class, 'index'])->name('bitacora.index');
                Route::get('/acciones', [BitacoraController::class, 'acciones'])->name('bitacora.acciones');
                Route::get('/logins', [BitacoraController::class, 'logins'])->name('bitacora.logins');
            });
        });
    //rutas de autentificacion personalizadas
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Gestión de categorías (superadmin)
    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('categorias', CategoriaController::class);
    });

    // Carrito de compras (todos los usuarios autenticados)
    Route::prefix('carrito')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('carrito.index');
        Route::post('/agregar', [CarritoController::class, 'store'])->name('carrito.store');
        Route::put('/actualizar/{carritoItem}', [CarritoController::class, 'update'])->name('carrito.update');
        Route::delete('/eliminar/{carritoItem}', [CarritoController::class, 'destroy'])->name('carrito.destroy');
        Route::post('/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');
    });

    // Ventas (superadmin y vendedor)
    Route::middleware(['role:superadmin,vendedor'])->group(function () {
        Route::resource('ventas', VentaController::class)->except(['create', 'edit']);
        Route::post('/ventas/procesar', [VentaController::class, 'procesarVenta'])->name('ventas.procesar');
        Route::post('/ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
    });

    // Gestión de usuarios (solo superadmin)
    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/activar', [UserController::class, 'activar'])->name('users.activar');
        
        Route::resource('roles', RoleController::class);
    });

    // Reportes (superadmin y vendedor)
    Route::middleware(['role:superadmin,vendedor'])->group(function () {
        Route::prefix('reportes')->group(function () {
            Route::get('/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
            Route::get('/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
            Route::get('/usuarios', [ReporteController::class, 'usuarios'])->name('reportes.usuarios');
            Route::post('/generar', [ReporteController::class, 'generarReporte'])->name('reportes.generar');
        });
    });

    // API para sesión
    Route::get('/check-session', [AuthController::class, 'checkSession'])->name('session.check');
    Route::post('/extend-session', [AuthController::class, 'extendSession'])->name('session.extend');
});

require __DIR__.'/auth.php';