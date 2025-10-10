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

# Copy composer files first
COPY composer.json composer.lock /app/

# Copy auth.json for Flux Pro authentication
COPY auth.json /app/auth.json

# Setup Composer authentication for Flux Pro
RUN mkdir -p /root/.composer && \
    cp /app/auth.json /root/.composer/auth.json && \
    echo "✅ Auth.json copied to Composer home" && \
    cat /root/.composer/auth.json

# Install PHP dependencies with detailed error output
RUN composer diagnose && \
    composer install --no-dev --optimize-autoloader --no-interaction -vvv 2>&1 | tee /tmp/composer-install.log || \
    (echo "❌ Composer install failed. Last 50 lines of log:" && tail -50 /tmp/composer-install.log && exit 1)

# Copy rest of application
COPY . /app

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

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

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

