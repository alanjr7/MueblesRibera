#!/bin/bash
set -e

cd /var/www/html

echo "=== MUEBLES RIBERA - INICIANDO ==="
echo "DB_CONNECTION = $DB_CONNECTION"
echo "DATABASE_URL = ${DATABASE_URL:0:60}..."

# === SIN PRUEBA DE CONEXIÓN (Laravel lo maneja) ===

# Composer
[ ! -d "vendor" ] && composer install --no-dev --optimize-autoloader --no-interaction

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# === ENLACE SIMBÓLICO (CRÍTICO) ===
echo "Creando enlace simbólico storage..."
rm -rf public/storage  # Eliminar si existe mal
php artisan storage:link

# Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== ¡APP LISTA! INICIANDO APACHE ==="
exec apache2-foreground