# Localhost Deployment Guide - Worker Mode

Panduan lengkap deploy system di localhost dengan FrankenPHP Worker Mode untuk testing.

## 📋 Prerequisites

### 1. Install Docker & Docker Compose

```bash
# Check jika sudah terinstall
docker --version
docker compose version

# Jika belum, install:
# macOS: Download Docker Desktop dari https://www.docker.com/products/docker-desktop
# Linux:
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
```

### 2. Check System Requirements

```bash
# Check CPU cores
sysctl -n hw.ncpu    # macOS
nproc                # Linux

# Check memory
free -h              # Linux
vm_stat | head -5    # macOS

# Minimum:
# - 2 CPU cores
# - 4GB RAM
# - 10GB disk space
```

---

## 🚀 Step-by-Step Deployment

### Step 1: Setup Environment Variables

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Edit .env file
nano .env
```

**Edit `.env` dengan konfigurasi berikut:**

```env
# Application
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=                          # ← Akan di-generate nanti
APP_DEBUG=true                    # ← true untuk localhost testing
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_local
DB_USERNAME=aozora_user
DB_PASSWORD=password123           # ← Ganti dengan password Anda
DB_ROOT_PASSWORD=rootpass123      # ← Ganti dengan password Anda

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=redispass123       # ← Ganti dengan password Anda
REDIS_PORT=6379

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=redis

# Mail (Optional - untuk testing)
MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@localhost
MAIL_FROM_NAME="${APP_NAME}"

# FrankenPHP Worker Mode
FRANKENPHP_NUM_THREADS=2          # ← Adjust sesuai CPU Anda
FRANKENPHP_NUM_WORKERS=2          # ← Adjust sesuai CPU Anda
```

**Save dan exit** (Ctrl+X, Y, Enter)

---

### Step 2: Generate Application Key

```bash
# Option 1: Jika PHP terinstall di localhost
php artisan key:generate

# Option 2: Via Docker (jika PHP belum terinstall)
docker run --rm -v $(pwd):/app composer:latest composer install --working-dir=/app
docker run --rm -v $(pwd):/app -w /app php:8.3-cli php artisan key:generate

# Option 3: Manual
# Generate key di https://generate-random.org/laravel-key-generator
# Lalu tambahkan ke .env:
# APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Verify APP_KEY terisi:**
```bash
grep APP_KEY .env
# Should show: APP_KEY=base64:xxxxxxxxxxxxx
```

---

### Step 3: Build Docker Images

```bash
# Build semua images (will take 5-10 minutes first time)
docker compose build

# Expected output:
# [+] Building 300.0s (25/25) FINISHED
# => => naming to docker.io/library/aozoraproject-app
```

**Troubleshooting:**
```bash
# Jika error "no space left":
docker system prune -a

# Jika error permission:
sudo chmod 666 /var/run/docker.sock
```

---

### Step 4: Start Services

```bash
# Start all containers in background
docker compose up -d

# Expected output:
# [+] Running 5/5
# ✔ Container aozora-db         Started
# ✔ Container aozora-redis      Started
# ✔ Container aozora-app        Started
# ✔ Container aozora-queue      Started
# ✔ Container aozora-scheduler  Started
```

**Verify containers running:**
```bash
docker compose ps

# Should see:
# NAME              STATUS          PORTS
# aozora-app        Up (healthy)    0.0.0.0:80->80/tcp
# aozora-db         Up (healthy)    0.0.0.0:3306->3306/tcp
# aozora-redis      Up (healthy)    0.0.0.0:6379->6379/tcp
# aozora-queue      Up
# aozora-scheduler  Up
```

---

### Step 5: Wait for Services to Be Ready

```bash
# Wait 20 seconds for services to initialize
echo "Waiting for services..."
sleep 20

# Check app container logs
docker compose logs app | tail -20

# Should see:
# ✅ [INFO] FrankenPHP workers started: 2
# ✅ [INFO] Server listening on :80
```

**If not ready:**
```bash
# Check database
docker compose logs db | tail -20

# Wait a bit more
sleep 10
```

---

### Step 6: Initialize Database

```bash
# Run migrations
docker compose exec app php artisan migrate --force

# Expected output:
# Migration table created successfully.
# Migrating: 2014_10_12_000000_create_users_table
# Migrated:  2014_10_12_000000_create_users_table (50.00ms)
# ... (more migrations)
```

**Verify migrations:**
```bash
docker compose exec app php artisan migrate:status

# Should show all migrations as "Ran"
```

---

### Step 7: Seed Database

```bash
# Run seeders
docker compose exec app php artisan db:seed

# Expected output:
# Setting up school management system...
# ✅ Roles & Permissions created
# ✅ Admin user created
# ✅ Organization created
# ✅ Test data created
```

**Verify seeding:**
```bash
# Check users created
docker compose exec app php artisan tinker --execute="echo User::count();"
# Should show: 4 (admin, parent, student, teacher)
```

---

### Step 8: Create Storage Link

```bash
# Create symbolic link for storage
docker compose exec app php artisan storage:link

# Expected output:
# The [public/storage] link has been connected to [storage/app/public].
```

---

### Step 9: Optimize for Performance

```bash
# Clear all caches first
docker compose exec app php artisan optimize:clear

# Then cache configurations
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Expected output:
# Configuration cached successfully!
# Routes cached successfully!
# Blade templates cached successfully!
```

---

### Step 10: Verify Worker Mode Active

```bash
# Check FrankenPHP workers
docker compose logs app | grep -i worker

# Should see:
# ✅ [INFO] FrankenPHP workers started: 2
# ✅ [INFO] Worker mode enabled
# ✅ [INFO] Worker file: /app/public/frankenphp-worker.php
```

**Check Caddyfile:**
```bash
docker compose exec app cat /etc/caddy/Caddyfile | head -20

# Should show worker configuration:
# frankenphp {
#     num_threads 2
#     worker {
#         file /app/public/frankenphp-worker.php
#         num 2
#     }
# }
```

---

### Step 11: Test Application

```bash
# 1. Test health endpoint
curl http://localhost/up

# Expected: {"status":"ok"}

# 2. Test home page
curl -I http://localhost

# Expected:
# HTTP/1.1 200 OK
# Server: Caddy
# Content-Type: text/html

# 3. Test response time
curl -o /dev/null -s -w "Response time: %{time_total}s\n" http://localhost

# Expected: Response time: 0.003s (very fast!)

# 4. Open in browser
open http://localhost        # macOS
xdg-open http://localhost    # Linux
```

---

## 🎯 Verify Everything is Working

### 1. Access Application

```bash
# Open in browser
http://localhost
```

**You should see:**
- ✅ Aozora Education welcome page
- ✅ Login button
- ✅ No errors

### 2. Test Login

**Admin Account:**
- Email: `admin@test.com`
- Password: `password`
- URL: `http://localhost/login`

**Parent Account:**
- Email: `parent@test.com`
- Password: `password`

**Teacher Account:**
- Email: `teacher@test.com`
- Password: `password`

**Student Account:**
- Email: `student@test.com`
- Password: `password`

### 3. Test Performance

```bash
# Simple benchmark
ab -n 100 -c 10 http://localhost/

# Expected results:
# Requests per second: 500-1000+  ← Fast!
# Time per request: 2-10ms        ← Very fast!
# Failed requests: 0               ← Perfect!
```

### 4. Monitor Resources

```bash
# Watch resource usage
docker stats

# Should see:
# aozora-app    ~200MB   10-20%   ← Low memory & CPU
# aozora-db     ~400MB   5-10%
# aozora-redis  ~10MB    1-2%
```

---

## 🔍 Troubleshooting

### Issue 1: Container Won't Start

```bash
# Check logs
docker compose logs app

# Common solutions:
# 1. Port already in use
docker compose down
sudo lsof -i :80  # Find what's using port 80
sudo kill -9 PID  # Kill the process

# 2. Permission issues
sudo chmod -R 777 storage bootstrap/cache

# 3. Rebuild
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Issue 2: Workers Not Starting

```bash
# Check worker logs
docker compose logs app | grep -i error

# Verify worker file exists
docker compose exec app ls -la /app/public/frankenphp-worker.php

# Check Caddyfile
docker compose exec app cat /etc/caddy/Caddyfile

# Restart
docker compose restart app
sleep 10
docker compose logs app | grep worker
```

### Issue 3: Database Connection Error

```bash
# Check database is running
docker compose ps db

# Check database logs
docker compose logs db

# Test connection
docker compose exec app php artisan db:show

# If fails, restart database
docker compose restart db
sleep 10
docker compose exec app php artisan migrate:status
```

### Issue 4: Slow Response Time

```bash
# Check if worker mode is active
docker compose logs app | grep worker

# If not active, check Caddyfile
docker compose exec app cat /etc/caddy/Caddyfile

# Verify .env has worker settings
cat .env | grep FRANKENPHP

# Restart to apply
docker compose restart app
```

### Issue 5: npm/Vite Errors

```bash
# Rebuild assets
docker compose exec app npm install
docker compose exec app npm run build

# Or rebuild container
docker compose build app
docker compose up -d
```

---

## 📊 Testing Worker Mode Performance

### Test 1: Response Time

```bash
# Single request
time curl -s http://localhost/ > /dev/null

# Should be: real 0m0.003s (very fast!)

# Multiple requests
for i in {1..10}; do
  curl -o /dev/null -s -w "Request $i: %{time_total}s\n" http://localhost/
done

# Should consistently show: 0.002-0.005s
```

### Test 2: Load Test

```bash
# Install ApacheBench (if not installed)
# macOS: Already installed
# Linux: sudo apt install apache2-utils

# Run load test
ab -n 1000 -c 10 http://localhost/

# Expected results:
# Requests per second: 800-1500+ 
# Time per request: 1-5ms
# Failed requests: 0
```

### Test 3: Compare with Standard Mode

```bash
# Test with worker mode (current)
ab -n 100 -c 10 http://localhost/ > worker.txt

# Disable worker mode
cp Caddyfile.standard Caddyfile
docker compose restart app
sleep 10

# Test without worker mode
ab -n 100 -c 10 http://localhost/ > standard.txt

# Compare results
echo "Worker Mode:"
grep "Requests per second" worker.txt
grep "Time per request" worker.txt

echo "Standard Mode:"
grep "Requests per second" standard.txt
grep "Time per request" standard.txt

# Re-enable worker mode
cp Caddyfile.worker Caddyfile
docker compose restart app
```

---

## 🎓 Development Workflow

### Making Code Changes

```bash
# 1. Edit your code
nano app/Http/Controllers/YourController.php

# 2. For worker mode to pick up changes, restart:
docker compose restart app

# 3. Verify changes
curl http://localhost/your-route
```

### Database Changes

```bash
# Create new migration
docker compose exec app php artisan make:migration create_something_table

# Edit migration file
nano database/migrations/YYYY_MM_DD_XXXXXX_create_something_table.php

# Run migration
docker compose exec app php artisan migrate
```

### Debugging

```bash
# View logs real-time
docker compose logs -f app

# View last 100 lines
docker compose logs app --tail=100

# Search for errors
docker compose logs app | grep -i error

# Access container shell
docker compose exec app bash
```

### Clear Caches

```bash
# Clear all caches
docker compose exec app php artisan optimize:clear

# Or individually
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

---

## 🛑 Stop Services

```bash
# Stop containers (keep data)
docker compose down

# Stop and remove everything (including data!)
docker compose down -v

# Stop and remove images
docker compose down --rmi all
```

---

## 🔄 Restart After System Reboot

```bash
# Just start again
cd /path/to/aozoraproject
docker compose up -d

# Everything should work!
```

---

## 📋 Quick Reference Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f app

# Check status
docker compose ps

# Restart app
docker compose restart app

# Access shell
docker compose exec app bash

# Run artisan commands
docker compose exec app php artisan [command]

# Check worker mode
docker compose logs app | grep worker

# Test performance
ab -n 100 -c 10 http://localhost/
```

---

## ✅ Success Checklist

- [ ] Docker & Docker Compose installed
- [ ] .env file configured with APP_KEY
- [ ] All containers running (healthy)
- [ ] Database migrated successfully
- [ ] Test data seeded
- [ ] Storage link created
- [ ] Worker mode active (check logs)
- [ ] Application accessible at http://localhost
- [ ] Login working (admin@test.com / password)
- [ ] Response time < 10ms
- [ ] No errors in logs

---

## 🎉 You're All Set!

System is now running on **localhost with FrankenPHP Worker Mode**!

**URLs:**
- Application: http://localhost
- PhpMyAdmin (optional): http://localhost:8080

**Credentials:**
- Admin: admin@test.com / password
- Parent: parent@test.com / password
- Teacher: teacher@test.com / password
- Student: student@test.com / password

**Next Steps:**
- Explore the application
- Test all features
- Check performance with load tests
- Make changes and test
- When ready, deploy to production!

**Enjoy testing! 🚀**

