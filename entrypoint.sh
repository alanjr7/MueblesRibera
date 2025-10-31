#!/bin/bash
set -e

cd /var/www/html

echo "=== Iniciando Muebles Ribera ==="

if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
fi

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo "Creando .env desde .env.example..."
        cp .env.example .env
    else
        echo "ERROR: No se encontró .env.example"
        exit 1
    fi
else
    echo ".env ya existe"
fi

set_env() {
    local key="$1"
    local value="$2"
    if grep -q "^${key}=" .env 2>/dev/null; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

echo "Inyectando variables de entorno..."
set_env "APP_NAME"          "${APP_NAME:-Muebles Ribera}"
set_env "APP_ENV"           "${APP_ENV:-production}"
set_env "APP_DEBUG"         "${APP_DEBUG:-false}"
set_env "APP_URL"           "${APP_URL:-https://mueblesribera.onrender.com}"
set_env "APP_KEY"           "${APP_KEY:-}"
set_env "APP_LOCALE"        "${APP_LOCALE:-es}"
set_env "APP_TIMEZONE"      "${APP_TIMEZONE:-America/La_Paz}"

set_env "DB_CONNECTION"     "${DB_CONNECTION:-pgsql}"
set_env "DB_HOST"           "${DB_HOST:-}"
set_env "DB_PORT"           "${DB_PORT:-5432}"
set_env "DB_DATABASE"       "${DB_DATABASE:-}"
set_env "DB_USERNAME"       "${DB_USERNAME:-}"
set_env "DB_PASSWORD"       "${DB_PASSWORD:-}"

set_env "SESSION_DRIVER"    "${SESSION_DRIVER:-database}"
set_env "CACHE_DRIVER"      "${CACHE_DRIVER:-array}"
set_env "QUEUE_CONNECTION"  "${QUEUE_CONNECTION:-sync}"

set_env "LOG_CHANNEL"       "${LOG_CHANNEL:-stderr}"
set_env "LOG_LEVEL"         "${LOG_LEVEL:-error}"

echo "Ejecutando package:discover..."
php artisan package:discover --ansi --rebuild

if [ -z "$APP_KEY" ] || grep -q "^APP_KEY=$" .env; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
else
    echo "APP_KEY ya configurada"
fi

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Ejecutando migraciones..."
php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Aplicación lista! Iniciando Apache... ==="
exec apache2-foreground