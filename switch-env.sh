#!/bin/bash

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔═══════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   🔄 Environment Switcher            ║${NC}"
echo -e "${BLUE}║   Aozora Education System            ║${NC}"
echo -e "${BLUE}╚═══════════════════════════════════════╝${NC}"
echo ""
echo "Select environment:"
echo "  ${GREEN}1)${NC} Local Development (Herd - MySQL local)"
echo "  ${YELLOW}2)${NC} Docker Development (localhost:8080)"
echo "  ${RED}3)${NC} Production (Docker + Nginx Proxy Manager)"
echo ""
read -p "Enter choice [1-3]: " choice

case $choice in
    1)
        echo -e "${YELLOW}➜ Switching to Local Development...${NC}"
        
        if [ ! -f .env.local ]; then
            echo -e "${RED}✗ Error: .env.local template not found!${NC}"
            exit 1
        fi
        
        # Backup current .env
        if [ -f .env ]; then
            cp .env .env.backup
            echo -e "  ${GREEN}✓${NC} Backed up current .env to .env.backup"
        fi
        
        # Copy local template
        cp .env.local .env
        
        # Generate APP_KEY if not set
        if ! grep -q "APP_KEY=base64:" .env; then
            echo -e "  ${YELLOW}➜${NC} Generating APP_KEY..."
            php artisan key:generate --force
        fi
        
        # Stop Docker if running
        if [ -f docker-compose.yml ]; then
            echo -e "  ${YELLOW}➜${NC} Stopping Docker containers (if any)..."
            docker compose down 2>/dev/null
        fi
        
        # Clear Laravel cache
        echo -e "  ${YELLOW}➜${NC} Clearing Laravel caches..."
        php artisan config:clear > /dev/null 2>&1
        php artisan cache:clear > /dev/null 2>&1
        php artisan view:clear > /dev/null 2>&1
        
        echo ""
        echo -e "${GREEN}╔═══════════════════════════════════════╗${NC}"
        echo -e "${GREEN}║  ✓ Switched to Local Development     ║${NC}"
        echo -e "${GREEN}╚═══════════════════════════════════════╝${NC}"
        echo ""
        echo "Configuration:"
        echo "  • Database:  MySQL (127.0.0.1:3306)"
        echo "  • Cache:     File-based"
        echo "  • Session:   File-based"
        echo "  • Queue:     Database"
        echo "  • Docker:    Not running"
        echo ""
        echo -e "${BLUE}Access:${NC} https://aozoraproject.test"
        echo ""
        ;;
        
    2)
        echo -e "${YELLOW}➜ Switching to Docker Development...${NC}"
        
        if [ ! -f .env.docker ]; then
            echo -e "${RED}✗ Error: .env.docker template not found!${NC}"
            exit 1
        fi
        
        if [ ! -f docker-compose.yml ]; then
            echo -e "${RED}✗ Error: docker-compose.yml not found!${NC}"
            echo -e "${YELLOW}  Have you merged dockerversion branch yet?${NC}"
            exit 1
        fi
        
        # Backup current .env
        if [ -f .env ]; then
            cp .env .env.backup
            echo -e "  ${GREEN}✓${NC} Backed up current .env to .env.backup"
        fi
        
        # Copy Docker template
        cp .env.docker .env
        
        # Generate APP_KEY if not set
        if ! grep -q "APP_KEY=base64:" .env; then
            echo -e "  ${YELLOW}➜${NC} Generating APP_KEY..."
            php artisan key:generate --force
        fi
        
        # Start Docker
        echo -e "  ${YELLOW}➜${NC} Starting Docker containers..."
        docker compose up -d
        
        # Wait for containers
        echo -e "  ${YELLOW}➜${NC} Waiting for containers to be ready..."
        sleep 10
        
        # Check containers status
        if ! docker compose ps | grep -q "healthy"; then
            echo -e "  ${YELLOW}⚠${NC}  Containers are starting... (this may take a moment)"
            sleep 5
        fi
        
        # Run migrations if needed
        echo -e "  ${YELLOW}➜${NC} Checking database migrations..."
        docker compose exec -T app php artisan migrate --force > /dev/null 2>&1
        
        echo ""
        echo -e "${GREEN}╔═══════════════════════════════════════╗${NC}"
        echo -e "${GREEN}║  ✓ Switched to Docker Development    ║${NC}"
        echo -e "${GREEN}╚═══════════════════════════════════════╝${NC}"
        echo ""
        echo "Configuration:"
        echo "  • Database:  MySQL (Docker container 'db')"
        echo "  • Cache:     Redis (Docker container 'redis')"
        echo "  • Session:   Redis"
        echo "  • Queue:     Redis"
        echo "  • Worker:    FrankenPHP (4 threads, 2 workers)"
        echo ""
        echo "Containers:"
        docker compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"
        echo ""
        echo -e "${BLUE}Access:${NC} http://localhost:8080"
        echo ""
        ;;
        
    3)
        echo -e "${YELLOW}➜ Switching to Production...${NC}"
        
        if [ ! -f .env.production ]; then
            echo -e "${RED}✗ Error: .env.production template not found!${NC}"
            exit 1
        fi
        
        if [ ! -f docker-compose.yml ]; then
            echo -e "${RED}✗ Error: docker-compose.yml not found!${NC}"
            exit 1
        fi
        
        # Backup current .env
        if [ -f .env ]; then
            cp .env .env.backup
            echo -e "  ${GREEN}✓${NC} Backed up current .env to .env.backup"
        fi
        
        # Copy production template
        cp .env.production .env
        
        echo ""
        echo -e "${RED}╔═══════════════════════════════════════╗${NC}"
        echo -e "${RED}║  ⚠  PRODUCTION MODE                   ║${NC}"
        echo -e "${RED}╚═══════════════════════════════════════╝${NC}"
        echo ""
        echo -e "${YELLOW}⚠  IMPORTANT: Update these values in .env:${NC}"
        echo "  • APP_KEY            (run: php artisan key:generate)"
        echo "  • APP_URL            (your domain)"
        echo "  • DB_PASSWORD        (strong password)"
        echo "  • DB_ROOT_PASSWORD   (strong password)"
        echo "  • REDIS_PASSWORD     (strong password)"
        echo "  • MAIL_* settings    (your mail server)"
        echo ""
        
        read -p "Have you updated all sensitive values? [y/N]: " confirm
        
        if [[ ! $confirm =~ ^[Yy]$ ]]; then
            echo -e "${YELLOW}ℹ  .env updated but deployment cancelled.${NC}"
            echo -e "   Edit .env file and run this script again."
            exit 0
        fi
        
        read -p "Continue with Docker deployment? [y/N]: " deploy
        
        if [[ $deploy =~ ^[Yy]$ ]]; then
            # Rebuild and start Docker
            echo -e "  ${YELLOW}➜${NC} Stopping old containers..."
            docker compose down
            
            echo -e "  ${YELLOW}➜${NC} Building fresh images..."
            docker compose build --no-cache app
            
            echo -e "  ${YELLOW}➜${NC} Starting production containers..."
            docker compose up -d
            
            # Wait for containers
            echo -e "  ${YELLOW}➜${NC} Waiting for containers..."
            sleep 15
            
            # Run migrations and seed
            echo -e "  ${YELLOW}➜${NC} Running database migrations..."
            docker compose exec -T app php artisan migrate --force
            
            echo -e "  ${YELLOW}➜${NC} Seeding organization data..."
            docker compose exec -T app php artisan db:seed --class=OrganizationSeeder --force
            
            # Optimize
            echo -e "  ${YELLOW}➜${NC} Optimizing for production..."
            docker compose exec -T app php artisan config:cache
            docker compose exec -T app php artisan route:cache
            docker compose exec -T app php artisan view:cache
            
            echo ""
            echo -e "${GREEN}╔═══════════════════════════════════════╗${NC}"
            echo -e "${GREEN}║  ✓ Production Environment Started    ║${NC}"
            echo -e "${GREEN}╚═══════════════════════════════════════╝${NC}"
            echo ""
            echo "Containers:"
            docker compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"
            echo ""
            echo -e "${BLUE}Next steps:${NC}"
            echo "  1. Configure Nginx Proxy Manager to point to localhost:8080"
            echo "  2. Test internal: curl -I http://localhost:8080"
            echo "  3. Test external: curl -I https://your-domain.com"
            echo ""
        else
            echo -e "${YELLOW}ℹ  Deployment cancelled. .env updated.${NC}"
        fi
        ;;
        
    *)
        echo -e "${RED}✗ Invalid choice!${NC}"
        exit 1
        ;;
esac

echo ""
echo -e "${GREEN}✓ Environment switch complete!${NC}"
echo -e "  Backup saved: .env.backup"

