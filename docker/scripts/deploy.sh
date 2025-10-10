#!/bin/bash
set -e

echo "========================================"
echo "Deploying Aozora Education System"
echo "========================================"

# Pull latest changes
echo "Pulling latest code..."
git pull origin main

# Build and start containers
echo "Building Docker images..."
docker compose build --no-cache

echo "Starting containers..."
docker compose up -d

# Wait for containers to be healthy
echo "Waiting for services to be healthy..."
sleep 10

# Run initialization
echo "Running initialization..."
docker compose exec -T app /docker/scripts/init.sh

# Seed database if needed
read -p "Do you want to seed the database? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Seeding database..."
    docker compose exec -T app php artisan db:seed
fi

# Restart queue workers
echo "Restarting queue workers..."
docker compose restart queue

echo "========================================"
echo "Deployment completed successfully!"
echo "========================================"
echo ""
echo "Application URL: ${APP_URL}"
echo ""
echo "Useful commands:"
echo "  - View logs: docker compose logs -f app"
echo "  - Access shell: docker compose exec app bash"
echo "  - Stop services: docker compose down"
echo "  - View status: docker compose ps"

