#!/bin/bash
set -e

cd /var/www/html

echo "=== MUEBLES RIBERA - INICIANDO ==="
echo "DB_CONNECTION = $DB_CONNECTION"
echo "DATABASE_URL = ${DATABASE_URL:0:60}..."

# === ESPERAR A QUE POSTGRESQL ESTÉ LISTO ===
echo "Esperando a que PostgreSQL esté disponible..."
MAX_ATTEMPTS=30
COUNT=0

until php -r "
\$url = getenv('DATABASE_URL');
if (!\$url) { echo 'DATABASE_URL no definida\n'; exit(1); }
try {
    new PDO(\$url);
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" > /dev/null 2>&1; do
    COUNT=$((COUNT + 1))
    if [ $COUNT -ge $MAX_ATTEMPTS ]; then
        echo "ERROR: No se pudo conectar a PostgreSQL después de $MAX_ATTEMPTS intentos"
        exit 1
    fi
    echo "Intento $COUNT/$MAX_ATTEMPTS... esperando 2s"
    sleep 2
done

echo "CONEXIÓN POSTGRESQL EXITOSA"

# === Composer ===
[ ! -d "vendor" ] && composer install --no-dev --optimize-autoloader --no-interaction

# === Permisos ===
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# === Laravel ===
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