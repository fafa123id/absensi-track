FROM composer:2 AS composer_builder
WORKDIR /app

COPY composer.json composer.lock ./

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts

COPY . .

RUN composer dump-autoload -o --classmap-authoritative


FROM node:20-alpine AS frontend_builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

COPY --from=composer_builder /app/vendor ./vendor

RUN npm run build

FROM php:8.3-fpm-alpine AS runtime

WORKDIR /var/www/absensi-track

RUN apk add --no-cache \
    bash curl git icu-libs icu-dev oniguruma-dev libzip-dev zlib-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libpq-dev autoconf \
    dpkg-dev dpkg file g++ gcc libc-dev make pkgconf re2c \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd intl zip opcache \
 && apk del autoconf dpkg-dev g++ gcc libc-dev make pkgconf re2c icu-dev freetype-dev libpng-dev libjpeg-turbo-dev

COPY --from=composer_builder /app /var/www/absensi-track
COPY --from=frontend_builder /app/public/build /var/www/absensi-track/public/build

RUN chown -R www-data:www-data /var/www/absensi-track/storage /var/www/absensi-track/bootstrap/cache

USER www-data

CMD ["php-fpm"]
