FROM php:8.2-fpm AS php_base

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip unzip \
    netcat-openbsd \
    git curl \
    libxml2-dev \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    mbstring exif pcntl bcmath gd pdo_mysql pdo_pgsql pgsql

COPY --from=composer:2.8.10 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/absensi_track


FROM composer:2.8.10 AS composer_deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --no-progress

FROM node:22-alpine AS vite_build
WORKDIR /var/www/absensi_track
COPY package*.json ./
RUN npm ci
COPY --from=composer_deps /app/vendor ./vendor
COPY tailwind.config.js postcss.config.js vite.config.js ./
COPY resources ./resources
RUN npm run build

FROM php_base AS app

COPY . .

COPY --from=vite_build /var/www/absensi_track/public/build ./public/build
COPY --from=composer_deps /app/vendor ./vendor

COPY supervisord.conf /etc/supervisor/supervisord.conf
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

RUN php artisan key:generate --force || true
RUN php artisan view:clear || true
RUN php artisan route:clear || true
RUN php artisan config:clear || true

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]

