#!/bin/sh
set -e

php artisan config:clear
php artisan package:discover

php artisan migrate --force

php artisan key:generate --force
php artisan optimize:clear
php artisan optimize


chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "Laravel setup is complete. Starting PHP-FPM..."
exec "$@"
