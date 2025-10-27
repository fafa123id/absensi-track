FROM php:8.2-fpm AS php_base
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip netcat-openbsd git curl \
    libonig-dev libxml2-dev supervisor \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:2.8.10 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/absensi_track


# ===== Stage 1: install composer deps (build vendor) =====
FROM composer:2.8.10 AS composer_deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --no-progress


# ===== Stage 2: build Vite =====
FROM node:22-alpine AS vite_build
WORKDIR /var/www/absensi_track
# npm deps dulu biar cache enak
COPY package*.json ./
RUN npm ci
# copy vendor dari stage composer agar import ../../vendor/... resolve
COPY --from=composer_deps /app/vendor ./vendor
# lalu copy config + sumber daya
COPY tailwind.config.js postcss.config.js vite.config.js ./
COPY resources ./resources
RUN npm run build


# ===== Stage 3: runtime app =====
FROM php_base AS app
WORKDIR /var/www/absensi_track

ARG GIT_HASH
RUN echo ${GIT_HASH} > .version

# copy source code
COPY . .
# copy hasil vite
COPY --from=vite_build /var/www/absensi_track/public/build ./public/build
# copy vendor dari stage composer (lebih cepat & konsisten)
COPY --from=composer_deps /app/vendor ./vendor

COPY supervisord.conf /etc/supervisor/supervisord.conf

# kalau tetap ingin jalan composer untuk autoload optimize (tanpa download lagi):
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# artisan opsional saat build (sesuaikan kalau butuh .env)
RUN php artisan key:generate --force || true
RUN php artisan view:clear || true
RUN php artisan route:clear || true
RUN php artisan config:clear || true

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm"]
