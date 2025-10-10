# 🔧 Localhost Deployment Troubleshooting Guide

Quick solutions untuk masalah umum saat deployment di localhost.

---

## ❌ Error: Permission Denied

### **Problem: `.env: Permission denied`**
```bash
cp: .env: Permission denied
sed: .env: Permission denied
```

**Solution 1 - Quick Fix (Recommended):**
```bash
./fix-permissions.sh
./setup-localhost.sh
```

**Solution 2 - Manual Fix:**
```bash
sudo chown -R $(whoami):$(id -gn) .
sudo chmod 644 .env .env.example
./setup-localhost.sh
```

**Solution 3 - Clean Start:**
```bash
sudo rm -f .env
cp .env.example .env
sudo chown $(whoami):$(id -gn) .env
chmod 644 .env
./setup-localhost.sh
```

---

## ❌ Error: Storage Permission Denied

### **Problem: `mkdir: storage: Permission denied`**

**Solution:**
```bash
sudo chmod -R 777 storage bootstrap/cache
sudo chown -R $(whoami):$(id -gn) storage bootstrap/cache
./setup-localhost.sh
```

---

## ❌ Error: Public/Storage Permission Denied

### **Problem: `mkdir: public/storage: Permission denied`**

**Solution:**
```bash
sudo rm -rf public/storage
sudo chmod -R 755 public
./setup-localhost.sh
```

Note: `public/storage` akan dibuat otomatis sebagai symlink oleh `artisan storage:link`.

---

## ❌ Error: Missing App Key

### **Problem: `MissingAppKeyException - No application encryption key`**
```bash
file_get_contents(/app/.env): Failed to open stream: No such file or directory
```

**Root Cause:** `.env` file tidak ter-mount ke container atau APP_KEY kosong.

---

### **Complete Solution (Recommended):**

**Step 1: Verify & Create .env**
```bash
# Check if .env exists
ls -la .env

# If NOT exist, create it from example
cp .env.example .env

# OR create manually if .env.example doesn't exist:
cat > .env << 'EOF'
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
MAIL_FROM_ADDRESS=noreply@localhost
MAIL_FROM_NAME="${APP_NAME}"

FRANKENPHP_NUM_THREADS=4
FRANKENPHP_NUM_WORKERS=2
EOF
```

**Step 2: Fix Permissions**
```bash
chmod 644 .env
```

**Step 3: Restart Containers (Re-mount .env)**
```bash
docker compose down
docker compose up -d
sleep 15
```

**Step 4: Verify & Generate Key**
```bash
# Verify .env is mounted
docker compose exec app ls -la /app/.env

# Generate APP_KEY
docker compose exec app php artisan key:generate --force

# Clear all caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
```

**Step 5: Test Application**
```bash
curl -I http://localhost:8080
# Should return: HTTP/1.1 200 OK
```

---

### **Quick One-Liner Solution:**
```bash
docker compose down && docker compose up -d && sleep 15 && docker compose exec app php artisan key:generate --force && docker compose exec app php artisan optimize:clear && echo "✅ Done! Test: curl http://localhost:8080"
```

---

### **Alternative Solutions:**

**Solution A - If .env Already Exists:**
```bash
docker compose down
docker compose up -d
sleep 15
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan config:clear
```

**Solution B - Manual APP_KEY Generation:**
```bash
# Generate key with openssl
APP_KEY="base64:$(openssl rand -base64 32)"
echo "APP_KEY=$APP_KEY"

# Edit .env and add the key
nano .env
# or
echo "APP_KEY=$APP_KEY" >> .env

# Restart
docker compose restart app
```

**Solution C - Using Helper Script:**
```bash
./fix-permissions.sh
docker compose down
docker compose up -d
sleep 20
docker compose exec app php artisan key:generate --force
```

---

### **Verification:**
```bash
# Check APP_KEY in .env
grep "APP_KEY=" .env  # Should show: APP_KEY=base64:...

# Check in container
docker compose exec app php artisan tinker --execute="echo config('app.key');"

# Test application
curl http://localhost:8080  # Should return HTML
```

---

## ❌ Error: Docker Build Failed (Composer)

### **Problem: `livewire/flux-pro` authentication**
```bash
exit code: 100
Could not find package livewire/flux-pro
```

**Solution:**
```bash
./setup-flux-credentials.sh
# Masukkan email & password Flux Pro
./setup-localhost.sh
```

---

## ❌ Error: Port Already in Use

### **Problem: Port 80/443/8080 sudah dipakai**
```bash
Error: bind: address already in use
```

**Solution 1 - Gunakan Port Lain:**

Edit `.env`:
```bash
APP_PORT=9000  # Ganti dengan port yang available
APP_URL=http://localhost:9000
```

**Solution 2 - Stop Service yang Conflict:**
```bash
# Cek port yang digunakan
sudo lsof -i :80
sudo lsof -i :8080

# Kill process (contoh)
sudo kill -9 <PID>
```

---

## ❌ Error: Container Keeps Restarting

### **Problem: Container restart terus-menerus**

**Check Logs:**
```bash
docker compose logs app
docker compose logs db
```

**Common Causes:**

1. **APP_KEY kosong** - Generate dengan:
   ```bash
   docker compose exec app php artisan key:generate
   ```

2. **Database connection failed** - Check `.env`:
   ```bash
   DB_HOST=db  # Harus "db" bukan "localhost"
   ```

3. **Storage permission** - Fix dengan:
   ```bash
   docker compose exec app chmod -R 777 /app/storage /app/bootstrap/cache
   ```

---

## ❌ Error: Composer Install Failed (artisan not found)

### **Problem: `Could not open input file: artisan`**

**Cause:** Dockerfile issue - `artisan` belum ada saat composer post-install scripts run.

**Already Fixed in Dockerfile:**
```dockerfile
# Copy entire application BEFORE composer install
COPY . /app
RUN composer install
```

Jika masih error, rebuild:
```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

---

## ✅ Complete Fresh Start

Jika semua solusi gagal, lakukan full cleanup:

```bash
# 1. Stop & remove containers
docker compose down -v

# 2. Remove Docker images
docker system prune -a --volumes

# 3. Fix all permissions
./fix-permissions.sh

# 4. Remove build artifacts
sudo rm -rf vendor node_modules public/storage
sudo rm -f .env

# 5. Fresh start
./setup-localhost.sh
```

---

## 🔍 Useful Debug Commands

**Check Container Status:**
```bash
docker compose ps
```

**View Logs:**
```bash
docker compose logs -f app          # Follow app logs
docker compose logs -f db           # Follow database logs
docker compose logs --tail=50 app   # Last 50 lines
```

**Check Inside Container:**
```bash
docker compose exec app bash
ls -la storage/
php artisan about
```

**Check Permissions:**
```bash
ls -la .env
ls -la storage/
ls -la bootstrap/cache/
```

**Test Application:**
```bash
curl -I http://localhost:8080
curl http://localhost:8080/up  # Health check
```

---

## 📞 Still Having Issues?

1. Run full diagnostics:
   ```bash
   docker compose ps
   docker compose logs app --tail=100
   ls -la
   cat .env | grep APP_KEY
   ```

2. Check Docker resources:
   - Docker Desktop → Settings → Resources
   - Pastikan cukup memory (min 4GB)
   - Pastikan cukup disk space

3. Restart Docker Desktop

4. Try different terminal (bash vs zsh)

5. Check if any antivirus blocking Docker

---

## 🎉 Success Indicators

Deployment berhasil jika:
- ✅ All containers status: `Up (healthy)`
- ✅ `curl http://localhost:8080` returns HTML
- ✅ Can login to application
- ✅ No errors in `docker compose logs app`

**Access Application:**
```
URL: http://localhost:8080
Admin: admin@school.com / password
```

---

## 📚 Related Documentation

- `DEPLOYMENT.md` - Full deployment guide
- `PERFORMANCE.md` - FrankenPHP Worker Mode
- `setup-localhost.sh` - Main setup script
- `fix-permissions.sh` - Permission fix utility

