# 🚀 Production Installation - Tested & Working

**Complete guide berdasarkan deployment yang sukses dengan Nginx Proxy Manager.**

---

## ✅ Prerequisites

- ✅ VPS/Server Ubuntu 20.04+ (2GB RAM, 2 CPU cores)
- ✅ Domain sudah pointing ke server IP
- ✅ Nginx Proxy Manager sudah installed
- ✅ SSH access ke server
- ✅ Flux Pro credentials

---

## 🎯 Quick Overview

```
Internet (443)
    ↓
Nginx Proxy Manager (handles SSL)
    ↓
Aozora Container (Port 8080 → HTTP only)
    ↓
MySQL 5.7 + Redis
```

---

## 📝 Step-by-Step Installation

### **Step 1: Initial Server Setup**

```bash
# SSH to server
ssh user@server-ip

# Update system
sudo apt update && sudo apt upgrade -y

# Install essentials
sudo apt install -y curl wget git nano

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify
docker --version
docker-compose --version
```

---

### **Step 2: Clone Repository**

```bash
# Create directory
sudo mkdir -p /var/www
cd /var/www

# Clone
sudo git clone https://github.com/ayahmayra/aozoraproject.git
cd aozoraproject

# Pull specific branch
sudo git pull origin dockerversion

# Fix ownership
sudo chown -R $USER:$USER /var/www/aozoraproject
```

---

### **Step 3: Setup Flux Pro Credentials**

```bash
# Run setup
./setup-flux-credentials.sh

# Enter credentials:
# Email: your-flux-email@example.com
# License: your-flux-license-key
```

---

### **Step 4: Configure Environment**

```bash
# Copy example
cp .env.example .env

# Edit configuration
nano .env
```

**Required .env configuration:**

```env
# Application
APP_NAME="Your School Name"
APP_ENV=production
APP_KEY=                              # Will be generated
APP_DEBUG=false
APP_URL=https://your-domain.com       # Your public domain

# Database (MySQL 5.7 for old CPU compatibility)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_production
DB_USERNAME=aozora_prod_user
DB_PASSWORD=YourStrongPassword123!
DB_ROOT_PASSWORD=YourStrongRootPass456!

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=YourRedisPassword789!
REDIS_PORT=6379

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail (configure your SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# FrankenPHP Worker Mode
FRANKENPHP_NUM_THREADS=8
FRANKENPHP_NUM_WORKERS=4

# Server Configuration (NPM Setup)
SERVER_NAME=:80                       # Container listens on :80
APP_PORT=8080                         # Docker maps to 8080 externally
LETS_ENCRYPT_EMAIL=                   # Empty (NPM handles SSL)
```

**Save:** `Ctrl+O`, `Enter`, `Ctrl+X`

---

### **Step 5: Configure Caddyfile for NPM**

```bash
# Backup original
cp Caddyfile Caddyfile.backup

# Create NPM-compatible Caddyfile
cat > Caddyfile << 'EOF'
{
    frankenphp {
        num_threads {$FRANKENPHP_NUM_THREADS:8}
        worker {
            file /app/public/frankenphp-worker.php
            num {$FRANKENPHP_NUM_WORKERS:4}
            env APP_ENV {$APP_ENV:production}
        }
    }
    order php_server before file_server
    auto_https off
}

:80 {
    root * /app/public
    encode gzip zstd
    php_server
    
    header {
        X-Frame-Options "SAMEORIGIN"
        X-Content-Type-Options "nosniff"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
        -Server
        -X-Powered-By
    }
    
    @disallowed {
        path /storage/* /.env* /.git/* /vendor/*
    }
    respond @disallowed 404
    
    @static {
        file
        path *.ico *.css *.js *.gif *.jpg *.jpeg *.png *.svg *.woff *.woff2
    }
    header @static Cache-Control "public, max-age=31536000"
    
    file_server
}
EOF
```

---

### **Step 6: Update docker-compose.yml for MySQL 5.7**

⚠️ **Important:** MySQL 8.0 requires newer CPU (x86-64-v2). For older servers, use MySQL 5.7.

The repository already has this fix, but verify:

```bash
grep "mysql:" docker-compose.yml
# Should show: image: mysql:5.7
```

If not, edit:
```bash
nano docker-compose.yml
# Change: image: mysql:8.0
# To:     image: mysql:5.7
```

---

### **Step 7: Build & Deploy**

```bash
# Build images (takes 5-10 minutes)
docker compose build --no-cache

# Start containers
docker compose up -d

# Wait for initialization
sleep 30

# Check status
docker compose ps
# All should show "Up"
```

---

### **Step 8: Initialize Application**

```bash
# Generate APP_KEY
docker compose exec app php artisan key:generate --force

# Verify
grep "APP_KEY=" .env
# Should show: APP_KEY=base64:...

# Create storage link
docker compose exec app php artisan storage:link

# Run migrations
docker compose exec app php artisan migrate --force

# Seed database
docker compose exec app php artisan db:seed --force

# Optimize for production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

---

### **Step 9: Test Internal Access**

```bash
# Test container
curl -I http://localhost:8080

# Should return: HTTP/1.1 200 OK ✅
```

---

### **Step 10: Configure Nginx Proxy Manager**

#### **Login to NPM:**
```
http://your-server-ip:81
```

**Default credentials:**
- Email: `admin@example.com`
- Password: `changeme`

⚠️ Change password after first login!

---

#### **Add Proxy Host:**

1. **Go to:** `Hosts` → `Proxy Hosts` → `Add Proxy Host`

2. **Details Tab:**
   ```
   Domain Names: your-domain.com
   
   Scheme: http                    ← IMPORTANT: http, not https!
   Forward Hostname/IP: server-ip  ← Your server IP or 172.17.0.1
   Forward Port: 8080
   
   ☑ Cache Assets
   ☑ Block Common Exploits
   ☑ Websockets Support
   ```

3. **SSL Tab:**
   ```
   ☑ Request a new SSL Certificate
   ☑ Force SSL
   ☑ HTTP/2 Support
   ☑ HSTS Enabled
   
   Email Address: your-email@example.com
   
   ☑ I Agree to the Let's Encrypt Terms of Service
   ```

4. **Advanced Tab (Recommended):**
   ```nginx
   proxy_set_header X-Real-IP $remote_addr;
   proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   proxy_set_header X-Forwarded-Proto $scheme;
   proxy_set_header X-Forwarded-Host $host;
   proxy_set_header Host $host;
   
   client_max_body_size 100M;
   ```

5. **Click:** `Save`

---

### **Step 11: Verify Deployment**

```bash
# From server
curl -I https://your-domain.com

# Should return: HTTP/2 200 ✅
```

**From browser:**
```
https://your-domain.com
```

**Expected:**
- ✅ Green padlock (SSL valid)
- ✅ Login page loads
- ✅ No errors

---

### **Step 12: First Login & Security**

**Default credentials:**
```
Email: admin@school.com
Password: password
```

**⚠️ IMMEDIATELY after first login:**
1. Go to Profile/Settings
2. Change password to strong password
3. Update email if needed

---

## 📋 Post-Installation Checklist

- [ ] All containers running (`docker compose ps`)
- [ ] Internal access works (`curl http://localhost:8080`)
- [ ] NPM Proxy Host configured
- [ ] SSL certificate active (green padlock)
- [ ] Can access via `https://your-domain.com`
- [ ] Can login successfully
- [ ] Default password changed
- [ ] Email settings configured

---

## 🔧 Common Issues & Solutions

### **Issue 1: Database Container Restarting**

```bash
# Check logs
docker compose logs db --tail=50

# If "CPU does not support x86-64-v2":
# Already fixed with MySQL 5.7 in docker-compose.yml
```

### **Issue 2: 502 Bad Gateway (NPM)**

```bash
# Try different Forward Hostname in NPM:
# 1. Server IP (10.10.10.42)
# 2. Docker bridge (172.17.0.1)
# 3. localhost (if NPM on same server)
```

### **Issue 3: Redirect Loop**

```bash
# Verify Caddyfile has:
docker compose exec app cat /etc/caddy/Caddyfile | grep "auto_https"
# Should show: auto_https off

# If not, rebuild:
docker compose down
docker rmi aozoraproject-app
docker compose build --no-cache
docker compose up -d
```

### **Issue 4: Container Won't Start**

```bash
# Check logs
docker compose logs app --tail=100

# Common fixes:
# - Generate APP_KEY: docker compose exec app php artisan key:generate --force
# - Fix permissions: docker compose exec app chmod -R 775 /app/storage
# - Check .env settings: cat .env | grep DB_
```

---

## 🎯 Architecture Summary

```
┌─────────────────────────────┐
│ Internet (HTTPS)            │
│ https://your-domain.com     │
└──────────┬──────────────────┘
           │
           │ Port 443 (HTTPS)
           │
┌──────────▼──────────────────┐
│ Nginx Proxy Manager         │
│ - SSL Termination           │
│ - Let's Encrypt Cert        │
│ - Port 80/443               │
└──────────┬──────────────────┘
           │
           │ Port 8080 (HTTP)
           │
┌──────────▼──────────────────┐
│ Docker: aozora-app          │
│ - FrankenPHP + Laravel      │
│ - Worker Mode Enabled       │
│ - Internal: Port 80         │
│ - External: Port 8080       │
└──────────┬──────────────────┘
           │
    ┌──────┴──────┐
    │             │
┌───▼────┐   ┌───▼────┐
│ MySQL  │   │ Redis  │
│  5.7   │   │   7    │
└────────┘   └────────┘
```

---

## 📊 Performance Specs

- **Response Time:** 50-200ms (with Worker Mode)
- **Throughput:** 3-5x faster than standard PHP-FPM
- **Concurrent Users:** Scales with CPU cores
- **Memory Usage:** ~512MB per worker

---

## 🔄 Maintenance Commands

```bash
# View logs
docker compose logs -f app

# Restart application
docker compose restart app

# Stop all
docker compose down

# Start all
docker compose up -d

# Backup database
docker compose exec db mysqldump -u root -p$DB_ROOT_PASSWORD $DB_DATABASE > backup.sql

# Clear cache
docker compose exec app php artisan cache:clear

# Run migrations
docker compose exec app php artisan migrate --force
```

---

## 🆘 Emergency Commands

```bash
# Complete restart
docker compose down && docker compose up -d && sleep 30

# Rebuild from scratch
docker compose down
docker rmi aozoraproject-app aozoraproject-queue aozoraproject-scheduler
docker compose build --no-cache
docker compose up -d

# Check container health
docker compose ps
docker compose logs app --tail=50
```

---

## 📚 Related Documentation

- **NPM Quick Setup:** [`NPM_QUICK_SETUP.md`](NPM_QUICK_SETUP.md)
- **Detailed NPM Guide:** [`NGINX_PROXY_MANAGER_SETUP.md`](NGINX_PROXY_MANAGER_SETUP.md)
- **Port Conflict Fix:** [`PRODUCTION_PORT_FIX.md`](PRODUCTION_PORT_FIX.md)
- **DB Connection Issues:** [`TROUBLESHOOT_DB_CONNECTION.md`](TROUBLESHOOT_DB_CONNECTION.md)
- **Quick Fixes:** [`QUICK_FIX.md`](QUICK_FIX.md)
- **Full Guide:** [`PRODUCTION_INSTALLATION.md`](PRODUCTION_INSTALLATION.md)

---

## 🎊 Deployment Complete!

Your Aozora Education Management System is now running in production!

**Access:** `https://your-domain.com`

**Test Accounts:**
- Admin: `admin@school.com` / `password`
- Parent: `parent@test.com` / `password`
- Teacher: `teacher@test.com` / `password`

**⚠️ Remember to:**
1. Change all default passwords
2. Configure email settings
3. Setup regular backups
4. Monitor logs regularly

---

**🚀 Deployment Time: ~30-45 minutes**

**Need help?** Check [`QUICK_FIX.md`](QUICK_FIX.md) or [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)

