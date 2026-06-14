FROM php:8.3-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

# System deps
RUN apk add --no-cache \
    bash \
    git \
    curl \
    unzip \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    linux-headers \
    autoconf \
    g++ \
    make

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    mbstring \
    pcntl \
    sockets

# Redis extension
RUN pecl channel-update pecl.php.net \
    && pecl install redis-6.0.2 \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-dev

COPY . .

RUN composer dump-autoload --optimize

EXPOSE 8000
