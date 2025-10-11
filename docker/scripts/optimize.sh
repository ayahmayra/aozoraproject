#!/bin/bash
set -e

echo "========================================"
echo "Optimizing for Production"
echo "========================================"

# Clear all caches first
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize configurations
echo "Caching configuration..."
php artisan config:cache

# Optimize routes
echo "Caching routes..."
php artisan route:cache

# Optimize views
echo "Compiling views..."
php artisan view:cache

# Optimize autoloader
echo "Optimizing Composer autoloader..."
composer install --optimize-autoloader --no-dev

# Generate IDE helper files (optional)
if [ -f "artisan" ]; then
    echo "Generating IDE helper files..."
    php artisan ide-helper:generate 2>/dev/null || true
    php artisan ide-helper:models --nowrite 2>/dev/null || true
    php artisan ide-helper:meta 2>/dev/null || true
fi

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "========================================"
echo "Optimization completed!"
echo "========================================"

