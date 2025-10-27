
# ========================
# 1) PHP BASE + BUILD DEPS
# ========================
FROM php:8.3-fpm-alpine AS php_base
WORKDIR /var/www/absensi-track # <-- Konsisten pake path ini

RUN apk add --no-cache \
    bash curl git icu-libs oniguruma libzip zlib libpng libjpeg-turbo freetype libpq \
    supervisor su-exec \
    icu-dev oniguruma-dev libzip-dev zlib-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libpq-dev \
    $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql gd intl zip opcache bcmath exif pcntl mbstring \
 && apk del --purge icu-dev oniguruma-dev libzip-dev zlib-dev \
             libpng-dev libjpeg-turbo-dev freetype-dev libpq-dev \
             $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


COPY supervisord.conf /etc/supervisor/supervisord.conf

# ========================
# 2) COMPOSER DEPENDENCIES
# ========================
FROM php_base AS composer_deps
WORKDIR /var/www/absensi-track
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-autoloader --no-scripts

# ========================
# 3) NODE BUILDER
# ========================
FROM node:20-alpine AS node_builder
WORKDIR /var/www/absensi-track
COPY package*.json ./
RUN npm ci
COPY tailwind.config.js postcss.config.js vite.config.js ./
COPY resources ./resources
COPY public ./public
# Copy vendor dari composer biar Ziggy gak error (kalo pake)
COPY --from=composer_deps /var/www/absensi-track/vendor ./vendor
# Copy sisa kode
COPY . .
# Pastikan VITE_* vars dari .env bisa dibaca pas build kalo perlu
RUN npm run build

# ========================
# 4) FINAL APP IMAGE (RUNTIME)
# ========================
FROM php_base AS app
WORKDIR /var/www/absensi-track

ARG GIT_HASH=unknown
RUN echo ${GIT_HASH} > .version

# Copy vendor & aset dari stage sebelumnya
COPY --from=composer_deps /var/www/absensi-track/vendor /var/www/absensi-track/vendor
COPY --from=node_builder /var/www/absensi-track/public/build /var/www/absensi-track/public/build
# Copy sisa kode aplikasi
COPY . .

# Generate autoload & optimize
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev \
 && php artisan optimize:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan storage:link

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"] 

# Perintah default buat service 'app'
EXPOSE 9000
CMD ["php-fpm"]