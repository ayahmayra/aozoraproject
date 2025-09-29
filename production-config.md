# Production Configuration Guide

## 🚀 Laravel Production Optimization

### 1. Environment Variables (.env)

```bash
# Production Environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Session Configuration (Use Redis)
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

# Cache Configuration (Use Redis)
CACHE_DRIVER=redis
CACHE_PREFIX=aozora_cache_

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2

# Database Optimization
DB_CONNECTION=mysql
# Add these to your database connection
PDO::ATTR_PERSISTENT => true
PDO::ATTR_TIMEOUT => 30
```

### 2. PHP Configuration (php.ini)

```ini
# OPcache Configuration
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.enable_cli=1

# Memory and Execution
memory_limit=256M
max_execution_time=30
max_input_time=30

# Session Configuration
session.gc_maxlifetime=7200
session.cookie_lifetime=0
session.cookie_secure=1
session.cookie_httponly=1
```

### 3. Web Server Configuration

#### Nginx Configuration:
```nginx
# Gzip Compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

# Browser Caching
location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# PHP-FPM Optimization
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_read_timeout 30;
    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;
}
```

#### Apache Configuration (.htaccess):
```apache
# Enable Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

### 4. Database Optimization

#### MySQL Configuration (my.cnf):
```ini
[mysqld]
# Performance Settings
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query Cache
query_cache_type = 1
query_cache_size = 64M
query_cache_limit = 2M

# Connection Settings
max_connections = 200
max_connect_errors = 1000
connect_timeout = 10
wait_timeout = 600
interactive_timeout = 600
```

#### Database Indexes:
```sql
-- Add indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);
CREATE INDEX idx_organizations_active ON organizations(is_active);
```

### 5. Laravel Optimizations

#### Run these commands on production:
```bash
# Clear and optimize caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Monitoring and Debugging

#### Install Laravel Debugbar (Development only):
```bash
composer require barryvdh/laravel-debugbar --dev
```

#### Use Laravel Telescope (Development only):
```bash
composer require laravel/telescope --dev
php artisan telescope:install
```

### 7. Common Issues and Solutions

#### Issue: Slow Login Redirect
**Solution:**
1. Use Redis for sessions
2. Enable OPcache
3. Optimize database queries
4. Use database indexes

#### Issue: Memory Issues
**Solution:**
1. Increase PHP memory_limit
2. Use Redis for caching
3. Optimize database queries
4. Enable OPcache

#### Issue: Database Connection Issues
**Solution:**
1. Use persistent connections
2. Optimize database configuration
3. Use connection pooling
4. Monitor database performance

### 8. Performance Monitoring

#### Use Laravel Horizon (Redis Queue):
```bash
composer require laravel/horizon
php artisan horizon:install
```

#### Monitor with Laravel Telescope:
```bash
composer require laravel/telescope
php artisan telescope:install
```

### 9. Security Considerations

#### Production Security:
1. Set APP_DEBUG=false
2. Use HTTPS (SESSION_SECURE_COOKIE=true)
3. Set proper file permissions
4. Use environment variables for secrets
5. Enable CSRF protection
6. Use secure session configuration

### 10. Deployment Checklist

- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Configure Redis for sessions and cache
- [ ] Enable OPcache
- [ ] Optimize database configuration
- [ ] Set proper file permissions
- [ ] Configure web server caching
- [ ] Enable Gzip compression
- [ ] Set up monitoring
- [ ] Test performance
