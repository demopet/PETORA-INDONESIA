FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash curl git icu-dev libpq-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev exiftool zip unzip nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql intl zip exif pcntl bcmath gd \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
