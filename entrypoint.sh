#!/bin/sh
set -e # Keluar kalo ada error

# --- PINDAH KE DAPUR DULU, ANJING! ---
cd /var/www/absensi_track
# ------------------------------------

echo "Current directory: $(pwd)"
echo "Running Laravel setup commands..."

# Sekarang PHP pasti nemu file 'artisan' karena udah di folder yang bener
php artisan migrate --force
php artisan key:generate --force # Kalo key belum ada di .env
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
# php artisan storage:link # Jalanin ini manual aja sekali di host, atau benerin path symlink-nya

# Benerin permission (setelah artisan jalan)
echo "Setting permissions..."
# Ambil UID/GID dari host (biar gak bentrok pas mount volume)
HOST_UID=$(stat -c %u .) || HOST_UID=1000
HOST_GID=$(stat -c %g .) || HOST_GID=1000
# Ganti kepemilikan storage/cache ke UID/GID host DULU biar bisa ditulis pas awal
chown -R "$HOST_UID":"$HOST_GID" storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
# Ganti kepemilikan akhir ke www-data buat FPM/Worker/Reverb
chown -R www-data:www-data storage bootstrap/cache

echo "Laravel setup complete. Starting command as www-data..."
# Jalankan perintah asli dari docker-compose (CMD) SEBAGAI www-data
exec su-exec www-data "$@"