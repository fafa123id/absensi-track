FROM composer:2 AS composer_builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --classmap-authoritative
COPY . .

RUN php artisan config:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-fpm-alpine AS runtime
WORKDIR /var/www/absensi-track

RUN apk add --no-cache \
    bash curl git icu-libs icu-dev oniguruma-dev libzip-dev zlib-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    libpq-dev $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo pdo_pgsql mbstring intl zip gd exif pcntl bcmath \
 && docker-php-ext-enable opcache

RUN { \
  echo 'opcache.enable=1'; \
  echo 'opcache.enable_cli=1'; \
  echo 'opcache.jit_buffer_size=128M'; \
  echo 'opcache.jit=tracing'; \
  echo 'opcache.validate_timestamps=0'; \
  echo 'opcache.max_accelerated_files=100000'; \
} > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 www && adduser -G www -D -u 1000 www

COPY . .
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=frontend_builder /app/public/build ./public/build

RUN php artisan storage:link || true
RUN chown -R www:www storage bootstrap/cache

RUN php -r "echo 'image ready\n';"

USER www
EXPOSE 9000
CMD ["php-fpm"]
