FROM php:8.2-apache

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
# Copia TODO el proyecto Laravel a /var/www/html
COPY ./indicadores/ /var/www/html/

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
FROM php:8.2-apache

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

# Copia TODO el proyecto Laravel a /var/www/html
COPY ./indicadores/ /var/www/html/

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]