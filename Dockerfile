FROM php:8.2-fpm-bullseye

# Fix DNS supaya composer tidak timeout
RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf

# Composer environment
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev default-mysql-client bash \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./

# Install composer dependencies (anti timeout)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --timeout=600 || true

# Copy full project
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 storage bootstrap/cache
