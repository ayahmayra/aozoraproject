#!/bin/bash
set -e

echo "Starting Laravel initialization..."

# Wait for database to be ready
echo "Waiting for database..."
while ! php artisan db:show > /dev/null 2>&1; do
    echo "Database not ready yet. Waiting..."
    sleep 2
done

echo "Database is ready!"

# Create storage link if not exists
if [ ! -L /app/public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear and cache config
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

echo "Laravel initialization completed!"

