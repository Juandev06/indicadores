# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilita mod_rewrite
RUN a2enmod rewrite

# Copia los archivos del proyecto
COPY . /var/www/html

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Instala dependencias de PHP
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Puerto expuesto
EXPOSE 80
