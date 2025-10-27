#!/bin/sh
set -e # Keluar kalo ada error

# --- Benerin Permission Dulu (Udah bener dari sebelumnya) ---
HOST_UID=$(stat -c %u /var/www/absensi-track) || HOST_UID=1000
HOST_GID=$(stat -c %g /var/www/absensi-track) || HOST_GID=1000
if ! getent group "$HOST_GID" > /dev/null 2>&1; then addgroup -g "$HOST_GID" laravel; fi
if ! getent passwd "$HOST_UID" > /dev/null 2>&1; then adduser -D -H -u "$HOST_UID" -G laravel laravel; fi
chown -R "$HOST_UID":"$HOST_GID" /var/www/absensi-track
# ------------------------------------

# --- PINDAHIN PERINTAH ARTISAN KE SINI ---
echo "Running Laravel setup commands (after network is up)..."
php artisan migrate --force # Butuh DB
php artisan optimize:clear # Gak butuh DB, tapi bagus di sini
php artisan config:cache # Butuh DB kalo ada relasi di config
php artisan route:cache # Gak butuh DB
php artisan view:cache # Gak butuh DB
# php artisan storage:link # Sebaiknya dijalanin manual sekali aja, atau pastiin target link-nya bener di dalem container

# Balikin kepemilikan storage & cache ke www-data (udah bener)
chown -R www-data:www-data /var/www/absensi-track/storage /var/www/absensi-track/bootstrap/cache
chmod -R 775 /var/www/absensi-track/storage /var/www/absensi-track/bootstrap/cache

echo "Laravel setup complete. Starting command..."
# Jalankan perintah asli dari docker-compose SEBAGAI www-data (udah bener)
exec su-exec www-data "$@"