#!/bin/bash
set -e

cd /var/www/html

echo "=== Muebles Ribera - Iniciando ==="

# Debug: mostrar conexión
echo "DB_CONNECTION = ${DB_CONNECTION}"
echo "DATABASE_URL = ${DATABASE_URL:0:60}..."

# Probar conexión a PostgreSQL
php -r "
echo 'Conectando a PostgreSQL...\n';
\$url = getenv('DATABASE_URL');
if (!\$url) { echo 'DATABASE_URL no definida\n'; exit(1); }
try {
    \$pdo = new PDO(\$url);
    echo 'Conexión exitosa!\n';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

# Composer (solo si falta vendor)
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Permisos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

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

echo "=== ¡Listo! Iniciando Apache ==="
exec apache2-foreground