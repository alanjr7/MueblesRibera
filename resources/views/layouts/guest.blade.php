<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layout de Autenticación - Laravel</title>
    
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configuración personalizada de Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                }
            }
        }
    </script>
    
    <!-- Fuente Inter para un aspecto más profesional -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .logo-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header con logo y nombre de la aplicación -->
        <div class="mb-10 text-center">
            <a href="/" class="inline-flex items-center space-x-4 group">
                <div class="logo-placeholder w-16 h-16 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                    <span class="text-white font-bold text-xl">LA</span>
                </div>
                <div class="text-left">
                    <h1 class="text-3xl font-bold text-gray-900">MiAplicación</h1>
                    <p class="text-gray-600 text-sm mt-1">Sistema de gestión profesional</p>
                </div>
            </a>
        </div>

        <!-- Tarjeta de contenido principal -->
        <div class="w-full sm:max-w-xl mt-6 px-8 py-10 bg-white shadow-soft rounded-2xl border border-gray-100 card-hover">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900">Acceso al Sistema</h2>
                <p class="text-gray-600 mt-2">Ingrese sus credenciales para continuar</p>
            </div>
            
            <!-- Ejemplo de formulario de login -->
            <form class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" id="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200" placeholder="usuario@ejemplo.com">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" id="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200" placeholder="••••••••">
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Recordar sesión</label>
                    </div>
                    
                    <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500 transition-colors duration-200">
                        ¿Olvidó su contraseña?
                    </a>
                </div>
                
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        Iniciar Sesión
                    </button>
                </div>
            </form>
            
            <!-- Enlace de registro -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    ¿No tiene una cuenta? 
                    <a href="#" class="font-medium text-primary-600 hover:text-primary-500 transition-colors duration-200">
                        Regístrese aquí
                    </a>
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-12 text-center text-sm text-gray-500">
            <p>&copy; 2023 MiAplicación. Todos los derechos reservados.</p>
            <p class="mt-1">
                <a href="#" class="hover:text-gray-700 transition-colors duration-200">Términos de servicio</a> 
                · 
                <a href="#" class="hover:text-gray-700 transition-colors duration-200">Política de privacidad</a>
            </p>
        </div>
    </div>
</body>
</html>