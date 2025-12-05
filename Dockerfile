FROM php:8.2-fpm-bullseye

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev default-mysql-client bash \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy only composer files first (for caching)
COPY composer.json composer.lock ./

# Install composer dependencies, with cache layer
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-progress || true

# Now copy whole application
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 storage bootstrap/cache
