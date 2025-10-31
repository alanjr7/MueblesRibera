#!/bin/bash
set -e

cd /var/www/html

echo "=== Configurando Muebles Ribera ==="

# Verificar variables críticas
echo "APP_URL: ${APP_URL}"
echo "DB_CONNECTION: ${DB_CONNECTION}"

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generar key si no existe
if [ -z "$(grep 'APP_KEY=base64:' .env 2>/dev/null)" ]; then
    php artisan key:generate --force
fi

# Migraciones
php artisan migrate --force

# Cache para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== ¡Aplicación lista! Iniciando servidor... ==="
exec apache2-foreground