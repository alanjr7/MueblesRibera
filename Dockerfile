FROM php:8.2-apache

# Instalar extensiones
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring exif pcntl bcmath gd \
    && a2enmod rewrite

# Configurar Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos de Composer PRIMERO (para cache)
COPY composer.json composer.lock /var/www/html/
WORKDIR /var/www/html

# Instalar dependencias (usa cache de Docker)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copiar el resto del código
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Script simple de inicio
CMD bash -c "composer dump-autoload && php artisan migrate --force && apache2-foreground"