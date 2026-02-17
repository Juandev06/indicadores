FROM php:8.2-apache

# Instala extensiones necesarias
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN a2enmod rewrite

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Copia solo el contenido de la app, NO archivos de configuración de Apache
COPY ./indicadores/app /var/www/app
COPY ./indicadores/bootstrap /var/www/bootstrap
COPY ./indicadores/config /var/www/config
COPY ./indicadores/database /var/www/database
COPY ./indicadores/public /var/www/html
COPY ./indicadores/resources /var/www/resources
COPY ./indicadores/routes /var/www/routes
COPY ./indicadores/storage /var/www/storage
COPY ./indicadores/composer.json /var/www/composer.json
COPY ./indicadores/composer.lock /var/www/composer.lock
COPY ./indicadores/package.json /var/www/package.json
COPY ./indicadores/package-lock.json /var/www/package-lock.json
COPY ./indicadores/webpack.mix.js /var/www/webpack.mix.js
COPY ./indicadores/artisan /var/www/artisan
COPY ./indicadores/phpunit.xml /var/www/phpunit.xml

WORKDIR /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]