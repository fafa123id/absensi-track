#!/bin/sh
set -e 


HOST_UID=$(stat -c %u /var/www/absensi-track) || HOST_UID=1000
HOST_GID=$(stat -c %g /var/www/absensi-track) || HOST_GID=1000

if ! getent group "$HOST_GID" > /dev/null 2>&1; then addgroup -g "$HOST_GID" laravel; fi
if ! getent passwd "$HOST_UID" > /dev/null 2>&1; then adduser -D -H -u "$HOST_UID" -G laravel laravel; fi
chown -R "$HOST_UID":"$HOST_GID" /var/www/absensi-track

php artisan key:generate --force 
php artisan migrate --force

chown -R www-data:www-data /var/www/absensi-track/storage /var/www/absensi-track/bootstrap/cache
chmod -R 775 /var/www/absensi-track/storage /var/www/absensi-track/bootstrap/cache

echo "Laravel setup complete. Starting command..."
exec su-exec www-data "$@"