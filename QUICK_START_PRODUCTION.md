# Quick Start - Production Deployment

Panduan cepat deploy ke production dengan domain custom.

## 🎯 Persiapan (Di Server Production)

```bash
# 1. Install Docker & Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# 2. Clone repository
git clone <your-repo-url>
cd aozoraproject

# 3. Copy dan edit .env
cp .env.example .env
nano .env
```

## 📝 Edit .env - Yang WAJIB Diubah

```env
# Domain Anda
APP_URL=https://school.yourdomain.com

# Security
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxx  # Generate: php artisan key:generate

# Database
DB_DATABASE=aozora_production
DB_USERNAME=aozora_user
DB_PASSWORD=PASSWORD_KUAT_ANDA  # <-- GANTI INI!
DB_ROOT_PASSWORD=ROOT_PASSWORD_KUAT  # <-- GANTI INI!

# Redis
REDIS_PASSWORD=REDIS_PASSWORD_KUAT  # <-- GANTI INI!

# Session (untuk domain)
SESSION_DOMAIN=yourdomain.com  # <-- GANTI dengan domain Anda
SESSION_SECURE_COOKIE=true

# Email untuk Let's Encrypt
# (Edit di Caddyfile)
```

## 🌐 Setup Domain

### 1. DNS Configuration

Di DNS provider Anda (Cloudflare, Namecheap, dll):

```
Type    Name    Value               TTL
A       @       IP_SERVER_ANDA      Auto
A       www     IP_SERVER_ANDA      Auto
```

### 2. Edit Caddyfile

```bash
cp Caddyfile.production Caddyfile
nano Caddyfile
```

Ubah semua `yourdomain.com` menjadi domain Anda:

```caddy
{
    frankenphp
    order php_server before file_server
    email admin@yourdomain.com  # <-- GANTI EMAIL
}

# HTTP Redirect
http://yourdomain.com, http://www.yourdomain.com {  # <-- GANTI DOMAIN
    redir https://yourdomain.com{uri} permanent      # <-- GANTI DOMAIN
}

# HTTPS
https://yourdomain.com, https://www.yourdomain.com {  # <-- GANTI DOMAIN
    root * /app/public
    encode gzip zstd
    php_server
    
    # Security Headers
    header {
        X-Frame-Options "SAMEORIGIN"
        X-Content-Type-Options "nosniff"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
        Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
        -Server
        -X-Powered-By
    }
    
    @disallowed {
        path /storage/*
        path /.env*
        path /.git/*
    }
    respond @disallowed 404
    
    file_server
}
```

## 🚀 Deploy Commands

```bash
# 1. Build images
docker compose build

# 2. Start services
docker compose up -d

# 3. Wait for services to be ready
sleep 20

# 4. Run migrations
docker compose exec app php artisan migrate --force

# 5. Create storage link
docker compose exec app php artisan storage:link

# 6. Seed database (opsional)
docker compose exec app php artisan db:seed

# 7. Optimize for production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# 8. Check status
docker compose ps
```

## ✅ Verifikasi

```bash
# 1. Check DNS (dari komputer Anda)
dig yourdomain.com
# Harus return IP server

# 2. Test HTTP (akan redirect ke HTTPS)
curl -I http://yourdomain.com

# 3. Test HTTPS
curl -I https://yourdomain.com

# 4. Health check
curl https://yourdomain.com/up
# Harus return: {"status":"ok"}

# 5. Open in browser
# https://yourdomain.com
```

## 🔧 Troubleshooting Cepat

### SSL Not Working?

```bash
# Check logs
docker compose logs app | grep -i "tls\|certificate"

# Pastikan port 80 terbuka (Let's Encrypt butuh ini)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Wait 2-3 menit untuk certificate generation
```

### Cannot Access Site?

```bash
# 1. Check container running
docker compose ps

# 2. Check from server
curl localhost

# 3. Check firewall
sudo ufw status
```

### Database Connection Error?

```bash
# Check database
docker compose logs db

# Restart database
docker compose restart db

# Wait then test
docker compose exec app php artisan migrate:status
```

## 📊 Monitoring

```bash
# View logs real-time
docker compose logs -f app

# Check resource usage
docker stats

# Database backup
docker compose exec db mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} > backup.sql
```

## 🔄 Update Application

```bash
# Pull latest code
git pull origin main

# Rebuild
docker compose build --no-cache

# Restart with zero downtime
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force

# Clear cache
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize

# Restart queue
docker compose restart queue
```

## 🛑 Stop & Remove

```bash
# Stop services
docker compose down

# Stop and remove volumes (DANGER: deletes data!)
docker compose down -v
```

## 📞 Need Help?

Lihat dokumentasi lengkap:
- `DOMAIN_SETUP.md` - Detail setup domain & SSL
- `DEPLOYMENT.md` - Complete deployment guide
- `README.md` - Application documentation

---

**Selamat! Sistem Anda sekarang live di production! 🎉**

