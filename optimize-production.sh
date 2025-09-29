#!/bin/bash

# Laravel Production Optimization Script
echo "🚀 Starting Laravel Production Optimization..."

# 1. Clear all caches
echo "📦 Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 2. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Generate optimized autoloader
echo "🔄 Optimizing autoloader..."
composer install --optimize-autoloader --no-dev

# 4. Clear and optimize database
echo "🗄️ Optimizing database..."
php artisan migrate --force

# 5. Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 6. Enable OPcache (if available)
echo "💾 Checking OPcache..."
php -m | grep -i opcache && echo "✅ OPcache is enabled" || echo "⚠️ OPcache not found"

# 7. Check PHP version and extensions
echo "🐘 PHP Information:"
php -v
echo "📋 Required Extensions:"
php -m | grep -E "(pdo|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd|curl|zip)"

echo "✅ Production optimization completed!"
echo ""
echo "📊 Performance Tips:"
echo "1. Enable OPcache in php.ini"
echo "2. Use Redis for sessions and cache"
echo "3. Enable database query caching"
echo "4. Use CDN for static assets"
echo "5. Enable Gzip compression"
echo "6. Monitor database performance"
