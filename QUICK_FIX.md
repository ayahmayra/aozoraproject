# ⚡ Quick Fix Reference Card

Common issues dan one-liner solutions.

---

## 🔑 Missing APP_KEY

### Quick Fix (if .env already exists):
```bash
docker compose down && docker compose up -d && sleep 15 && docker compose exec app php artisan key:generate --force && docker compose exec app php artisan optimize:clear
```

### Complete Fix (if .env not mounted):
```bash
# 1. Ensure .env exists
ls -la .env || cp .env.example .env

# 2. Fix permissions
chmod 644 .env

# 3. Restart to re-mount
docker compose down && docker compose up -d && sleep 15

# 4. Generate key
docker compose exec app php artisan key:generate --force

# 5. Clear cache
docker compose exec app php artisan config:clear && docker compose exec app php artisan cache:clear
```

### Verify:
```bash
grep "APP_KEY=" .env && curl -I http://localhost:8080
```

---

## 📁 Permission Denied

```bash
./fix-permissions.sh
```

---

## 🔄 Container Keeps Restarting

```bash
docker compose logs app --tail=50
```

Then fix based on error:
```bash
# If APP_KEY missing
docker compose exec app php artisan key:generate --force

# If storage permission
docker compose exec app chmod -R 777 /app/storage /app/bootstrap/cache

# If database issue
docker compose restart db && sleep 10 && docker compose restart app
```

---

## 🔌 Port Already in Use

```bash
# Edit .env, change APP_PORT
nano .env
# Set: APP_PORT=9000

docker compose down && docker compose up -d
```

---

## 🗄️ Reset Database

```bash
docker compose exec app php artisan migrate:fresh --seed --force
```

---

## 🧹 Clear All Cache

```bash
docker compose exec app php artisan optimize:clear
```

---

## 🔍 Check Logs

```bash
# App logs
docker compose logs -f app

# Database logs
docker compose logs -f db

# All services
docker compose logs -f
```

---

## 🔄 Full Restart

```bash
docker compose down && docker compose up -d && sleep 20 && docker compose ps
```

---

## 🗑️ Complete Reset

```bash
docker compose down -v
docker system prune -a --volumes
./fix-permissions.sh
./setup-localhost.sh
```

---

## 🚀 Access Application

```
URL: http://localhost:8080
Admin: admin@school.com / password
```

---

## 📊 Check Status

```bash
# Container status
docker compose ps

# Health check
curl http://localhost:8080/up

# APP_KEY verification
grep APP_KEY= .env
```

---

## 🔧 Container Shell Access

```bash
# App container
docker compose exec app bash

# Database
docker compose exec db mysql -u aozora_user -p aozora_local
```

---

## 📦 Update Application

```bash
git pull
docker compose down
docker compose build --no-cache
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```

---

## 💾 Backup Database

```bash
docker compose exec db mysqldump -u aozora_user -paozora_password123 aozora_local > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

## 🔄 Restore Database

```bash
docker compose exec -T db mysql -u aozora_user -paozora_password123 aozora_local < backup.sql
```

---

## 🛠️ Rebuild Specific Service

```bash
# Rebuild app only
docker compose build app
docker compose up -d app

# Rebuild all
docker compose build --no-cache
docker compose up -d
```

---

## 🔐 Generate New APP_KEY

```bash
# Method 1: In container
docker compose exec app php artisan key:generate --force

# Method 2: Local (macOS/Linux)
echo "APP_KEY=base64:$(openssl rand -base64 32)"
```

---

## 📋 System Info

```bash
# Docker version
docker --version

# Compose version
docker compose version

# Container info
docker compose exec app php artisan about

# Environment
docker compose exec app env | grep APP_
```

---

## ⚠️ Emergency Recovery

If nothing works:

```bash
# 1. Stop everything
docker compose down -v
docker stop $(docker ps -aq)
docker rm $(docker ps -aq)

# 2. Clean Docker
docker system prune -a --volumes --force

# 3. Fix permissions
sudo rm -rf storage/logs/* bootstrap/cache/*
./fix-permissions.sh

# 4. Fresh start
./setup-localhost.sh
```

---

## 📞 Need Help?

1. Check logs: `docker compose logs app --tail=100`
2. See troubleshooting: `LOCALHOST_TROUBLESHOOTING.md`
3. View full guide: `LOCALHOST_QUICK_START.md`

---

**Keep this file handy for quick reference!** 📌

