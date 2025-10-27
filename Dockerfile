# ========================
# 1) COMPOSER BUILDER
# ========================
FROM composer:2 AS composer_builder
WORKDIR /app

# pasang vendor tanpa scripts (artisan belum ada)
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts

# setelah vendor, baru copy source
COPY . .
RUN composer dump-autoload -o --classmap-authoritative


# ========================
# 2) FRONTEND BUILDER
# ========================
FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
# supaya import ziggy via vendor tidak error saat build
COPY --from=composer_builder /app/vendor ./vendor
RUN npm run build


# ========================
# 3) RUNTIME (PHP-FPM)
# ========================
FROM php:8.3-fpm-alpine AS runtime
WORKDIR /var/www/absensi-track

# ---- RUNTIME libs (dipertahankan) + BUILD deps (dihapus setelah compile)
RUN apk add --no-cache \
    # runtime
    bash curl git icu-libs oniguruma libzip zlib libpng libjpeg-turbo freetype libpq \
    # build dev
    icu-dev oniguruma-dev libzip-dev zlib-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libpq-dev \
    $PHPIZE_DEPS \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql gd intl zip opcache \
 # hapus HANYA paket dev; runtime libs tetap ada → FIX GD
 && apk del icu-dev oniguruma-dev libzip-dev zlib-dev \
           libpng-dev libjpeg-turbo-dev freetype-dev libpq-dev \
           $PHPIZE_DEPS

# copy app + build assets dari stages
COPY --from=composer_builder /app /var/www/absensi-track
COPY --from=frontend_builder /app/public/build /var/www/absensi-track/public/build

# entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# permission minimal
RUN chown -R www-data:www-data storage bootstrap/cache

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

USER www-data

CMD ["php-fpm"]
