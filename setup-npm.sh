#!/bin/bash

# Setup for Nginx Proxy Manager Configuration
# This configures the application to work behind NPM

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
print_error() { echo -e "${RED}❌ $1${NC}"; }

echo "=========================================="
echo "🔧 Nginx Proxy Manager Setup"
echo "=========================================="
echo ""

# Check if running in project directory
if [ ! -f "docker-compose.yml" ]; then
    print_error "docker-compose.yml not found!"
    print_info "Please run this script from the project root directory"
    exit 1
fi

# Backup current configuration
print_info "Backing up current configuration..."
cp Caddyfile Caddyfile.backup
cp .env .env.backup 2>/dev/null || print_warning ".env not found, will be created"
print_success "Backups created: Caddyfile.backup, .env.backup"
echo ""

# Use NPM-specific Caddyfile
print_info "Updating Caddyfile for NPM..."
cp Caddyfile.npm Caddyfile
print_success "Caddyfile updated (listens on :80 only)"
echo ""

# Update .env for NPM
print_info "Configuring .env for NPM..."

if [ ! -f ".env" ]; then
    cp .env.example .env
    print_success ".env created from .env.example"
fi

# Get domain from user
read -p "Enter your domain (e.g., school.example.com): " DOMAIN
if [ -z "$DOMAIN" ]; then
    print_error "Domain cannot be empty!"
    exit 1
fi

# Update .env
print_info "Updating .env configuration..."

# Set APP_URL
if grep -q "^APP_URL=" .env; then
    sed -i.bak "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
else
    echo "APP_URL=https://${DOMAIN}" >> .env
fi

# Set APP_PORT
if grep -q "^APP_PORT=" .env; then
    sed -i.bak "s|^APP_PORT=.*|APP_PORT=8080|" .env
else
    echo "APP_PORT=8080" >> .env
fi

# Set SERVER_NAME
if grep -q "^SERVER_NAME=" .env; then
    sed -i.bak "s|^SERVER_NAME=.*|SERVER_NAME=:80|" .env
else
    echo "SERVER_NAME=:80" >> .env
fi

# Remove LETS_ENCRYPT_EMAIL if exists (NPM handles SSL)
if grep -q "^LETS_ENCRYPT_EMAIL=" .env; then
    sed -i.bak "s|^LETS_ENCRYPT_EMAIL=.*|LETS_ENCRYPT_EMAIL=|" .env
fi

# Clean up backup files
rm -f .env.bak

print_success ".env configured for NPM"
echo ""

# Show configuration
print_info "Current configuration:"
echo "  Domain: https://${DOMAIN}"
echo "  Internal Port: 80"
echo "  External Port: 8080"
echo "  SSL: Handled by Nginx Proxy Manager"
echo ""

# Ask to restart containers
print_warning "Docker containers need to be restarted for changes to take effect."
read -p "Restart containers now? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_info "Stopping containers..."
    docker compose down
    
    print_info "Starting containers..."
    docker compose up -d
    
    print_info "Waiting for containers to be ready..."
    sleep 20
    
    print_info "Checking container status..."
    docker compose ps
    
    echo ""
    print_info "Testing internal access..."
    if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200"; then
        print_success "Application is responding on port 8080!"
    else
        print_warning "Application might not be ready yet. Check logs: docker compose logs app"
    fi
    
    echo ""
    print_success "Setup complete!"
    echo ""
    print_info "Next steps:"
    echo "  1. Login to Nginx Proxy Manager (usually http://server-ip:81)"
    echo "  2. Add Proxy Host:"
    echo "     - Domain: ${DOMAIN}"
    echo "     - Scheme: http"
    echo "     - Forward Hostname/IP: aozora-app (or use: 172.17.0.1)"
    echo "     - Forward Port: 8080"
    echo "     - Enable: Block Common Exploits, Websockets Support"
    echo "     - SSL: Request new SSL Certificate"
    echo "  3. Access your application: https://${DOMAIN}"
    echo ""
    print_info "For detailed NPM setup guide, see: NGINX_PROXY_MANAGER_SETUP.md"
else
    print_info "Containers not restarted. Run manually:"
    echo "  docker compose down"
    echo "  docker compose up -d"
fi

echo ""
print_success "Configuration complete! ✨"

