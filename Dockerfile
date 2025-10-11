FROM dunglas/frankenphp:latest-php8.3

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    redis \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy auth.json for Flux Pro authentication
COPY auth.json /app/auth.json

# Setup Composer authentication for Flux Pro
RUN mkdir -p /root/.composer && \
    cp /app/auth.json /root/.composer/auth.json && \
    echo "✅ Auth.json copied to Composer home"

# Copy entire application first (needed for artisan commands)
COPY . /app

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy existing application directory permissions
RUN chown -R www-data:www-data /app

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Create necessary directories
RUN mkdir -p /app/storage/logs \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/bootstrap/cache

# Remove any existing public/storage (akan dibuat ulang sebagai symlink)
RUN rm -rf /app/public/storage 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public
RUN chmod -R 775 /app/storage /app/bootstrap/cache /app/public

# Expose port
EXPOSE 80 443

# Set environment variables
ENV SERVER_NAME=:80
ENV CADDY_GLOBAL_OPTIONS=""

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

# Start FrankenPHP
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

