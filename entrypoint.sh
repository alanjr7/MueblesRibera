#!/bin/bash
set -e

cd /var/www/html

echo "=== Iniciando Muebles Ribera ==="

# Verificar dependencias de Composer
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
fi

# NO creamos .env: Usamos directamente las env vars de Render

# Ejecutar package:discover (Laravel leerá de $_ENV)
echo "Ejecutando package:discover..."
php artisan package:discover --ansi --rebuild

# Verificar APP_KEY (ya está en Render, pero por si acaso)
if [ -z "${APP_KEY}" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
else
    echo "APP_KEY ya configurada"
fi

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Migraciones (usa DB_* de Render)
echo "Ejecutando migraciones..."
php artisan migrate --force

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Aplicación lista! Iniciando Apache... ==="
exec apache2-foreground