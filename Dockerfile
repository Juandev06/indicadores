# Usa una imagen oficial de PHP con Apache

# Imagen base PHP con Apache
FROM php:8.2-apache

# Instala dependencias del sistema y extensiones PHP necesarias para Laravel y paquetes comunes
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Habilita mod_rewrite
RUN a2enmod rewrite

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer


# Copia el proyecto Laravel (excepto public/) a /var/www
COPY ./indicadores /var/www
# Copia el contenido de public/ a /var/www/html
COPY ./indicadores/public /var/www/html

WORKDIR /var/www

# Instala dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Puerto expuesto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
