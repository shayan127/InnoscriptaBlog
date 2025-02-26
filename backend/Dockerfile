FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    unzip \
    git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd mbstring zip pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www

COPY .env.example .env
RUN chmod -R 777 storage bootstrap
RUN composer install --no-dev --optimize-autoloader --prefer-dist
RUN php artisan config:clear && php artisan cache:clear && php artisan optimize:clear


CMD ["php-fpm"]
