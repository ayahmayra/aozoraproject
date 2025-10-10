# 🚀 Localhost Quick Start (5 Minutes!)

## Super Cepat - Automated Setup

```bash
# Just run this one command!
./setup-localhost.sh
```

**That's it!** Script akan otomatis:
- ✅ Setup environment
- ✅ Build Docker images
- ✅ Start all services
- ✅ Initialize database
- ✅ Enable worker mode
- ✅ Run tests

**Access:** http://localhost

---

## Manual Setup (Step by Step)

### 1. Setup Environment (30 seconds)
```bash
cp .env.example .env
nano .env
# Edit: APP_URL=http://localhost, APP_DEBUG=true
```

### 2. Generate Key (10 seconds)
```bash
php artisan key:generate
# Or: docker run --rm -v $(pwd):/app -w /app php:8.3-cli php artisan key:generate
```

### 3. Build & Start (5-10 minutes first time)
```bash
docker compose build
docker compose up -d
```

### 4. Initialize (2 minutes)
```bash
# Wait for services
sleep 20

# Run migrations
docker compose exec app php artisan migrate --force

# Seed data
docker compose exec app php artisan db:seed

# Create storage link
docker compose exec app php artisan storage:link

# Optimize
docker compose exec app php artisan optimize
```

### 5. Verify (10 seconds)
```bash
# Check worker mode
docker compose logs app | grep worker
# ✅ Should see: "FrankenPHP workers started: 2"

# Test response
curl http://localhost/up
# ✅ Should return: {"status":"ok"}
```

---

## 🎯 Access Application

**URL:** http://localhost

**Login Credentials:**
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@test.com | password |
| Parent | parent@test.com | password |
| Teacher | teacher@test.com | password |
| Student | student@test.com | password |

---

## 📊 Check Performance

```bash
# Test response time
curl -o /dev/null -s -w "Time: %{time_total}s\n" http://localhost

# Expected: Time: 0.003s ⚡ (super fast!)

# Load test (optional)
ab -n 100 -c 10 http://localhost/

# Expected: 
# - Requests/sec: 500-1500+ 🚀
# - Time/request: 2-10ms ⚡
# - Failed: 0 ✅
```

---

## 🔧 Common Commands

```bash
# View logs
docker compose logs -f app

# Check status
docker compose ps

# Restart app
docker compose restart app

# Stop all
docker compose down

# Start again
docker compose up -d

# Access shell
docker compose exec app bash

# Clear cache
docker compose exec app php artisan optimize:clear
```

---

## ⚠️ Troubleshooting

### Port 80 Already in Use?

```bash
# Find what's using port 80
sudo lsof -i :80

# Stop it or change port in docker-compose.yml:
ports:
  - "8080:80"  # Use port 8080 instead

# Then access: http://localhost:8080
```

### Workers Not Starting?

```bash
# Check logs
docker compose logs app | tail -50

# Restart
docker compose restart app
sleep 10
docker compose logs app | grep worker
```

### Database Connection Error?

```bash
# Restart database
docker compose restart db
sleep 10

# Test connection
docker compose exec app php artisan db:show
```

---

## 🛑 Stop & Clean Up

```bash
# Stop (keep data)
docker compose down

# Stop & remove data
docker compose down -v

# Full cleanup
docker compose down --rmi all -v
docker system prune -a
```

---

## 📚 Need More Help?

- **Full Guide:** `LOCALHOST_DEPLOYMENT.md` (detailed step-by-step)
- **Worker Mode:** `FRANKENPHP_WORKER.md` (performance guide)
- **Performance:** `PERFORMANCE.md` (optimization tips)
- **Deployment:** `DEPLOYMENT.md` (production deployment)

---

## ✅ Success Indicators

Your setup is successful if you see:

- ✅ All containers "Up (healthy)" in `docker compose ps`
- ✅ "FrankenPHP workers started" in logs
- ✅ `http://localhost` shows welcome page
- ✅ Login works (admin@test.com / password)
- ✅ Response time < 10ms
- ✅ No errors in logs

**Enjoy! 🎉**

