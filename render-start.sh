#!/bin/bash
set -e

cd /var/www/html

echo "=== Iniciando Muebles Ribera ==="

# Verificar que vendor existe
if [ ! -d "vendor" ]; then
    echo "⚠️  Vendor no encontrado, instalando dependencias..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Verificar que autoload.php existe
if [ ! -f "vendor/autoload.php" ]; then
    echo "❌ ERROR: vendor/autoload.php no existe después de composer install"
    exit 1
fi

echo "✅ Dependencias de Composer verificadas"

# Configurar permisos
echo "=== Configurando permisos ==="
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar cache de Laravel
echo "=== Limpiando cache ==="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generar key de Laravel si no existe
echo "=== Verificando APP_KEY ==="
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generando nueva APP_KEY..."
    php artisan key:generate --force
else
    echo "APP_KEY ya configurada"
fi

# Ejecutar migraciones
echo "=== Ejecutando migraciones ==="
php artisan migrate --force

# Optimizar para producción
echo "=== Optimizando para producción ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== ✅ Aplicación lista! Iniciando Apache... ==="
exec apache2-foreground