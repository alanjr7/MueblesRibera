<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-authenticated" content="true">
    <meta name="user-role" content="{{ auth()->user()->rol->nombre }}">
    @endauth
    <title>@yield('title', 'Sistema E-commerce')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
        }
        .stat-card {
            border-left: 4px solid #007bff;
        }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-primary">
                            <i class="fas fa-store"></i> E-commerce App
                        </h5>
                        @auth
                        <small class="text-muted">
                            {{ auth()->user()->nombre }}<br>
                            <span class="badge bg-secondary">{{ auth()->user()->rol->nombre }}</span>
                        </small>
                        @endauth
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        @can('viewAny', App\Models\Producto::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('productos.index') }}">
                                <i class="fas fa-box"></i> Productos
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', App\Models\Categoria::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categorias.index') }}">
                                <i class="fas fa-tags"></i> Categorías
                            </a>
                        </li>
                        @endcan

                        @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('carrito.index') }}">
                                <i class="fas fa-shopping-cart"></i> Carrito
                                @if(auth()->user()->carritoItems && auth()->user()->carritoItems->count() > 0)
                                    <span class="badge bg-danger">{{ auth()->user()->carritoItems->count() }}</span>
                                @endif
                            </a>
                        </li>
                        @endauth

                        @can('viewAny', App\Models\Venta::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('ventas.index') }}">
                                <i class="fas fa-chart-bar"></i> Ventas
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', App\Models\User::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> Usuarios
                            </a>
                        </li>
                        @endcan

                        @can('viewAny', App\Models\Reporte::class)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-chart-pie"></i> Reportes
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('reportes.ventas') }}">Ventas</a></li>
                                <li><a class="dropdown-item" href="{{ route('reportes.inventario') }}">Inventario</a></li>
                                @can('viewAny', App\Models\User::class)
                                <li><a class="dropdown-item" href="{{ route('reportes.usuarios') }}">Usuarios</a></li>
                                @endcan
                            </ul>
                        </li>
                        @endcan
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Navbar superior -->
                <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                @auth
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user"></i> {{ auth()->user()->nombre }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-edit"></i> Perfil
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                                @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt"></i> Login
                                    </a>
                                </li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Alertas -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Contenido principal -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Sistema E-commerce. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script de sesión -->
    @auth
    <script src="{{ asset('js/session.js') }}"></script>
    @endauth
    
    @stack('scripts')
</body>
</html>