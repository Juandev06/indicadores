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

# Copia los archivos del proyecto desde la carpeta indicadores
COPY ./indicadores /var/www/html
WORKDIR /var/www/html

# Instala dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Variables de entorno para producción
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Puerto expuesto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
