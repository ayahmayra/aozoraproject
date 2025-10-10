# Deployment Guide - Aozora Education System

Panduan lengkap untuk deploy sistem menggunakan FrankenPHP dan Docker.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose V2
- Git
- Minimal 4GB RAM
- Minimal 20GB disk space

## Quick Start

### 1. Clone Repository

```bash
git clone <repository-url>
cd aozoraproject
```

### 2. Setup Environment

Copy file environment template:

```bash
cp .env.example .env
```

Edit `.env` dengan konfigurasi production Anda:

```env
APP_NAME="Aozora Education"
APP_ENV=production
APP_KEY=  # Generate dengan: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=aozora_production
DB_USERNAME=aozora_user
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_root_password

REDIS_PASSWORD=your_redis_password
```

### 3. Generate APP_KEY

Jika belum ada APP_KEY, generate dulu:

```bash
# Jika PHP dan Composer sudah terinstall locally
php artisan key:generate

# Atau gunakan Docker
docker run --rm -v $(pwd):/app composer:latest composer install
docker run --rm -v $(pwd):/app php:8.3-cli php /app/artisan key:generate
```

### 4. Build dan Start Containers

```bash
# Build images
docker compose build

# Start services
docker compose up -d

# Check status
docker compose ps
```

### 5. Initialize Application

```bash
# Run migrations
docker compose exec app php artisan migrate --force

# Create storage link
docker compose exec app php artisan storage:link

# Seed database (optional)
docker compose exec app php artisan db:seed

# Cache configurations
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 6. Verify Deployment

Akses aplikasi di browser:
- HTTP: `http://your-domain.com`
- HTTPS: `https://your-domain.com`

Check health:
```bash
curl http://your-domain.com/up
```

## Services

### Application (FrankenPHP)
- Port: 80 (HTTP), 443 (HTTPS)
- Container: `aozora-app`

### MySQL
- Port: 3306
- Container: `aozora-db`
- Data: Volume `db-data`

### Redis
- Port: 6379
- Container: `aozora-redis`
- Data: Volume `redis-data`

### Queue Worker
- Container: `aozora-queue`
- Auto-restart on failure

### Scheduler
- Container: `aozora-scheduler`
- Runs Laravel schedule every minute

## Common Commands

### View Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f db
docker compose logs -f queue
```

### Access Container Shell

```bash
# App container
docker compose exec app bash

# Database container
docker compose exec db bash
```

### Run Artisan Commands

```bash
docker compose exec app php artisan <command>

# Examples:
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan queue:work
docker compose exec app php artisan cache:clear
```

### Restart Services

```bash
# Restart all
docker compose restart

# Restart specific service
docker compose restart app
docker compose restart queue
```

### Update Application

```bash
# Pull latest code
git pull origin main

# Rebuild and restart
docker compose build --no-cache
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force

# Clear cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Restart queue workers
docker compose restart queue
```

## Backup & Restore

### Database Backup

```bash
# Manual backup
docker compose exec db mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} | gzip > backup_$(date +%Y%m%d).sql.gz

# Using backup script
./docker/scripts/backup.sh
```

### Database Restore

```bash
# Restore from backup
gunzip < backup_YYYYMMDD.sql.gz | docker compose exec -T db mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}
```

### Storage Backup

```bash
# Backup storage directory
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/

# Restore storage
tar -xzf storage_backup_YYYYMMDD.tar.gz
```

## Monitoring

### Health Checks

```bash
# Application health
curl http://localhost/up

# Container health
docker compose ps
```

### Resource Usage

```bash
# View resource usage
docker stats

# View specific container
docker stats aozora-app
```

## Troubleshooting

### Permission Issues

```bash
docker compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
docker compose exec app chmod -R 775 /app/storage /app/bootstrap/cache
```

### Clear All Caches

```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

### Database Connection Issues

```bash
# Check database is running
docker compose ps db

# Check database logs
docker compose logs db

# Test connection
docker compose exec app php artisan db:show
```

### Queue Not Processing

```bash
# Check queue worker
docker compose logs queue

# Restart queue worker
docker compose restart queue

# Manually run queue
docker compose exec app php artisan queue:work --tries=3
```

## Security Checklist

- [ ] Change all default passwords
- [ ] Set `APP_DEBUG=false` in production
- [ ] Generate strong `APP_KEY`
- [ ] Use HTTPS with valid SSL certificate
- [ ] Set secure Redis password
- [ ] Restrict database access
- [ ] Enable firewall rules
- [ ] Regular backups
- [ ] Keep Docker images updated
- [ ] Monitor logs regularly

## Performance Optimization

### Enable OPcache

Already enabled in Dockerfile.

### Queue Workers

Adjust queue workers based on load:

```yaml
# In docker-compose.yml
queue:
  deploy:
    replicas: 3  # Run 3 queue workers
```

### Database Optimization

Adjust MySQL settings in `docker/mysql/my.cnf` based on your server specs.

### Redis Memory

Set Redis max memory:

```bash
redis-cli CONFIG SET maxmemory 256mb
redis-cli CONFIG SET maxmemory-policy allkeys-lru
```

## Scaling

### Horizontal Scaling

Use Docker Swarm or Kubernetes for multi-server deployment.

### Load Balancing

Add Nginx or Traefik as reverse proxy:

```yaml
services:
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
```

## Maintenance Mode

```bash
# Enable maintenance mode
docker compose exec app php artisan down --render="errors::503"

# Disable maintenance mode
docker compose exec app php artisan up
```

## Support

For issues and questions, contact: support@aozora.edu

## License

Proprietary - Aozora Education Center

