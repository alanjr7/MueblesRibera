#!/bin/bash
set -e

cd /var/www/html

echo "=== MUEBLES RIBERA - INICIANDO ==="
echo "DB_CONNECTION = $DB_CONNECTION"
echo "DATABASE_URL = ${DATABASE_URL:0:60}..."

# Probar conexión
php -r "
\$url = getenv('DATABASE_URL');
if (!\$url) { echo 'ERROR: DATABASE_URL no definida\n'; exit(1); }
try {
    new PDO(\$url);
    echo 'CONEXIÓN POSTGRESQL EXITOSA\n';
} catch (Exception \$e) {
    echo 'ERROR DB: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

# Composer
[ ! -d "vendor" ] && composer install --no-dev --optimize-autoloader --no-interaction

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== ¡APP LISTA! INICIANDO APACHE ==="
exec apache2-foreground