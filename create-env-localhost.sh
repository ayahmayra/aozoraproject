#!/bin/bash

# Create .env file for localhost development

cat > .env << 'EOF'
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8080
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_local
DB_USERNAME=aozora_user
DB_PASSWORD=aozora_password123
DB_ROOT_PASSWORD=root_password123

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=aozora

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=redis_password123
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@localhost"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

# FrankenPHP Worker Mode
# IMPORTANT: num_threads MUST be > num_workers
FRANKENPHP_NUM_THREADS=4
FRANKENPHP_NUM_WORKERS=2
EOF

echo "✅ .env file created for localhost!"
echo ""
echo "⚠️  Important: Generate APP_KEY with:"
echo "   docker compose exec app php artisan key:generate"
echo ""

