#!/bin/bash
set -e

# Dynamic port binding (Render assigns $PORT, e.g. 10000; defaults to 80)
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port: $PORT"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Setup SQLite if using sqlite connection
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    echo "==> Initializing SQLite database..."
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
fi

# Ensure storage & cache directory permissions
echo "==> Setting directory permissions..."
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if empty
if [ -z "$APP_KEY" ]; then
    echo "==> Generating application encryption key..."
    php artisan key:generate --force
fi

# Storage symlink
php artisan storage:link || true

# Run database migrations
echo "==> Running database migrations..."
php artisan migrate --force || true

# Seed database
if [ "${SEED_ON_DEPLOY:-true}" = "true" ]; then
    echo "==> Seeding initial Netafarm data..."
    php artisan db:seed --class=DatabaseSeeder --force || true
fi

# Optimize Laravel caches
echo "==> Optimizing configuration and view cache..."
php artisan optimize:clear
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Netafarm Motoris is ready! Starting Apache web server..."
exec apache2-foreground
