# Stage 1: Build Node assets
FROM node:16-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm config set registry https://registry.npmmirror.com \
    && npm install --legacy-peer-deps --prefer-offline --no-audit --progress=false
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# Stage 2: PHP + Composer
FROM php:8.3-fpm-alpine AS php-base
RUN apk add --no-cache git curl zip unzip libpng-dev libzip-dev oniguruma-dev mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader
COPY . .
COPY --from=node-build /app/dist ./public/dist
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Stage 3: Production
FROM php-base AS production
RUN apk add --no-cache nginx supervisor bash
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
EXPOSE 80 443
ENTRYPOINT ["/entrypoint.sh"]
