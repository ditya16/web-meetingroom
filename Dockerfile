FROM php:8.2-fpm-bullseye

# Accept DNS from build args (optional)
ARG DNS=8.8.8.8

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev default-mysql-client bash \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --timeout=600 || true

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 storage bootstrap/cache
