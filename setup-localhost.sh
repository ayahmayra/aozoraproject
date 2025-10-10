#!/bin/bash

# Aozora Education System - Localhost Setup Script
# This script automates the localhost deployment with worker mode

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_header() {
    echo ""
    echo -e "${BLUE}========================================"
    echo "$1"
    echo -e "========================================${NC}"
    echo ""
}

print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_error() { echo -e "${RED}❌ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
print_info() { echo -e "ℹ️  $1"; }

# Check if Docker is installed
check_docker() {
    print_header "Checking Prerequisites"
    
    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed!"
        echo "Please install Docker Desktop from:"
        echo "https://www.docker.com/products/docker-desktop"
        exit 1
    fi
    
    if ! command -v docker compose &> /dev/null; then
        print_error "Docker Compose is not installed!"
        echo "Please install Docker Compose V2"
        exit 1
    fi
    
    print_success "Docker installed: $(docker --version)"
    print_success "Docker Compose installed: $(docker compose version)"
}

# Setup environment file
setup_env() {
    print_header "Setting Up Environment"
    
    if [ -f .env ]; then
        print_warning ".env file already exists"
        read -p "Do you want to overwrite it? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_info "Keeping existing .env file"
            return
        fi
    fi
    
    print_info "Creating .env file..."
    
    # Remove any existing .env file first (dengan permission handling)
    if [ -f .env ]; then
        if rm -f .env 2>/dev/null; then
            print_info "Removed existing .env"
        else
            print_warning "Need sudo to remove existing .env..."
            sudo rm -f .env || {
                print_error "Cannot remove existing .env file"
                exit 1
            }
        fi
    fi
    
    # Create .env directly to avoid permission issues
    if [ -f .env.example ]; then
        # Try normal copy first
        if ! cp .env.example .env 2>/dev/null; then
            print_warning "Need elevated permissions to create .env..."
            # Try with sudo, then fix ownership
            if sudo cp .env.example .env; then
                sudo chown $(whoami):$(id -gn) .env
                print_info "Created .env with sudo and fixed ownership"
            else
                print_error "Failed to create .env file"
                exit 1
            fi
        fi
    else
        # If .env.example doesn't exist, create minimal .env
        print_info ".env.example not found, creating default .env..."
        
        if cat > .env << 'ENVEOF' 2>/dev/null
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_local
DB_USERNAME=aozora_user
DB_PASSWORD=aozora_password123
DB_ROOT_PASSWORD=root_password123

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=redis_password123
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@localhost
MAIL_FROM_NAME="${APP_NAME}"

FRANKENPHP_NUM_THREADS=4
FRANKENPHP_NUM_WORKERS=2
ENVEOF
        then
            print_info "Default .env created successfully"
        else
            # Try with sudo
            print_warning "Need elevated permissions to create .env..."
            sudo tee .env > /dev/null << 'ENVEOF'
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_local
DB_USERNAME=aozora_user
DB_PASSWORD=aozora_password123
DB_ROOT_PASSWORD=root_password123

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=redis_password123
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@localhost
MAIL_FROM_NAME="${APP_NAME}"

FRANKENPHP_NUM_THREADS=4
FRANKENPHP_NUM_WORKERS=2
ENVEOF
            sudo chown $(whoami):$(id -gn) .env
            print_info "Created .env with sudo and fixed ownership"
        fi
    fi
    
    # Ensure .env has correct permissions and ownership
    if ! chmod 644 .env 2>/dev/null; then
        print_warning "Setting permissions with sudo..."
        sudo chmod 644 .env 2>/dev/null || true
        sudo chown $(whoami):$(id -gn) .env 2>/dev/null || true
    fi
    
    print_success ".env file created"
}

# Check Flux Pro credentials
check_flux_credentials() {
    print_header "Checking Flux Pro Credentials"
    
    if [ ! -f "auth.json" ]; then
        print_warning "auth.json not found!"
        echo ""
        print_info "Flux Pro requires authentication credentials."
        echo ""
        read -p "Do you want to setup Flux Pro credentials now? (y/n) " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            ./setup-flux-credentials.sh
        else
            print_warning "Skipping Flux Pro setup. Build may fail."
            print_info "You can setup later with: ./setup-flux-credentials.sh"
        fi
    else
        print_success "auth.json found"
        print_info "Email: $(grep username auth.json | cut -d'"' -f4)"
    fi
}

# Generate APP_KEY
generate_app_key() {
    print_header "Generating Application Key"
    
    if grep -q "APP_KEY=base64:" .env; then
        print_info "APP_KEY already exists"
        return
    fi
    
    # Check if vendor exists
    if [ -d "vendor" ] && command -v php &> /dev/null; then
        print_info "Generating APP_KEY with PHP..."
        if php artisan key:generate 2>/dev/null; then
            print_success "APP_KEY generated"
            return
        fi
    fi
    
    print_warning "Will generate APP_KEY after containers start"
}

# Prepare storage directories
prepare_storage() {
    print_header "Preparing Storage Directories"
    
    print_info "Creating storage directories..."
    
    # Create storage directories (skip public/storage, akan dibuat oleh artisan storage:link)
    mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache 2>/dev/null || {
        print_warning "Some directories exist, checking permissions..."
    }
    
    # Remove existing public/storage jika ada (symlink lama)
    if [ -e public/storage ]; then
        print_info "Removing existing public/storage..."
        rm -rf public/storage 2>/dev/null || sudo rm -rf public/storage 2>/dev/null || {
            print_warning "Could not remove public/storage, will try in container"
        }
    fi
    
    print_info "Setting permissions..."
    chmod -R 777 storage bootstrap/cache 2>/dev/null || {
        print_warning "Could not set all permissions, trying with sudo..."
        sudo chmod -R 777 storage bootstrap/cache 2>/dev/null || {
            print_warning "Some permission issues, will be fixed in container"
        }
    }
    
    print_success "Storage directories prepared"
}

# Build Docker images
build_images() {
    print_header "Building Docker Images"
    
    print_info "This may take 5-10 minutes on first run..."
    print_info "Building images..."
    
    if docker compose build; then
        print_success "Docker images built successfully"
    else
        print_error "Failed to build Docker images"
        exit 1
    fi
}

# Start services
start_services() {
    print_header "Starting Services"
    
    print_info "Starting containers..."
    
    if docker compose up -d; then
        print_success "Containers started"
    else
        print_error "Failed to start containers"
        exit 1
    fi
    
    print_info "Waiting for services to be ready (20 seconds)..."
    sleep 20
    
    # Check container status
    if docker compose ps | grep -q "Up"; then
        print_success "All services are running"
    else
        print_error "Some services failed to start"
        docker compose ps
        exit 1
    fi
}

# Generate APP_KEY in container if needed
generate_app_key_container() {
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        print_info "Generating APP_KEY..."
        
        # Try to generate with openssl locally
        if command -v openssl &> /dev/null; then
            APP_KEY="base64:$(openssl rand -base64 32)"
            
            # Try to update .env with proper permission handling
            if sed -i.bak "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env 2>/dev/null; then
                rm -f .env.bak
                print_success "APP_KEY generated locally: $APP_KEY"
                return
            elif sudo sed -i.bak "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env 2>/dev/null; then
                sudo rm -f .env.bak
                sudo chmod 644 .env
                print_success "APP_KEY generated locally (with sudo): $APP_KEY"
                return
            fi
        fi
        
        # Fallback: Generate in container
        print_info "Generating APP_KEY in container..."
        if docker compose exec -T app php artisan key:generate --force; then
            print_success "APP_KEY generated in container"
        else
            print_warning "Could not generate APP_KEY automatically"
            print_warning "Please run manually: docker compose exec app php artisan key:generate"
        fi
    else
        print_info "APP_KEY already exists"
    fi
}

# Initialize database
init_database() {
    print_header "Initializing Database"
    
    print_info "Running migrations..."
    if docker compose exec -T app php artisan migrate --force; then
        print_success "Migrations completed"
    else
        print_error "Migration failed"
        print_info "Check logs: docker compose logs app"
        exit 1
    fi
    
    print_info "Seeding database..."
    if docker compose exec -T app php artisan db:seed; then
        print_success "Database seeded"
    else
        print_warning "Seeding failed (might be already seeded)"
    fi
}

# Create storage link
create_storage_link() {
    print_header "Creating Storage Link"
    
    # Force remove existing link first (di container)
    docker compose exec -T app rm -rf /app/public/storage 2>/dev/null || true
    
    # Create fresh symlink
    if docker compose exec -T app php artisan storage:link; then
        print_success "Storage link created"
    else
        print_warning "Storage link creation failed, but continuing..."
    fi
    
    # Set proper permissions in container
    print_info "Setting storage permissions in container..."
    docker compose exec -T app chmod -R 777 /app/storage /app/bootstrap/cache 2>/dev/null || true
}

# Optimize application
optimize_app() {
    print_header "Optimizing Application"
    
    print_info "Clearing caches..."
    docker compose exec -T app php artisan optimize:clear
    
    print_info "Caching configurations..."
    docker compose exec -T app php artisan config:cache
    docker compose exec -T app php artisan route:cache
    docker compose exec -T app php artisan view:cache
    
    print_success "Application optimized"
}

# Verify worker mode
verify_worker_mode() {
    print_header "Verifying Worker Mode"
    
    sleep 3
    
    if docker compose logs app 2>&1 | grep -qi "worker"; then
        print_success "Worker mode is ACTIVE"
        docker compose logs app 2>&1 | grep -i "worker" | tail -3
    else
        print_warning "Worker mode status unclear"
        print_info "Check manually: docker compose logs app | grep worker"
    fi
}

# Test application
test_application() {
    print_header "Testing Application"
    
    print_info "Testing health endpoint..."
    if curl -f http://localhost/up > /dev/null 2>&1; then
        print_success "Health check passed"
    else
        print_warning "Health check failed (app might still be starting)"
    fi
    
    print_info "Testing response time..."
    RESPONSE_TIME=$(curl -o /dev/null -s -w "%{time_total}" http://localhost 2>&1 || echo "error")
    
    if [ "$RESPONSE_TIME" != "error" ]; then
        print_success "Response time: ${RESPONSE_TIME}s"
    else
        print_warning "Could not measure response time"
    fi
}

# Print success message
print_final_message() {
    print_header "🎉 Setup Complete!"
    
    echo "Your Aozora Education System is now running on localhost!"
    echo ""
    echo "📍 URLs:"
    echo "   Application: http://localhost"
    echo "   Health Check: http://localhost/up"
    echo ""
    echo "👤 Test Accounts:"
    echo "   Admin:   admin@test.com / password"
    echo "   Parent:  parent@test.com / password"
    echo "   Teacher: teacher@test.com / password"
    echo "   Student: student@test.com / password"
    echo ""
    echo "🚀 Worker Mode: ACTIVE (2 workers, 2 threads)"
    echo ""
    echo "📋 Useful Commands:"
    echo "   View logs:    docker compose logs -f app"
    echo "   Check status: docker compose ps"
    echo "   Stop:         docker compose down"
    echo "   Restart:      docker compose restart app"
    echo ""
    echo "📚 Documentation:"
    echo "   Full guide:   LOCALHOST_DEPLOYMENT.md"
    echo "   Worker mode:  FRANKENPHP_WORKER.md"
    echo "   Performance:  PERFORMANCE.md"
    echo ""
    print_success "Happy testing! 🎓"
}

# Main execution
main() {
    print_header "🚀 Aozora Education - Localhost Setup"
    
    check_docker
    setup_env
    check_flux_credentials
    generate_app_key
    prepare_storage
    build_images
    start_services
    generate_app_key_container
    init_database
    create_storage_link
    optimize_app
    verify_worker_mode
    test_application
    print_final_message
}

# Run main function
main

