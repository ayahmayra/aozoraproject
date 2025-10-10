# 🔧 Setup dengan Nginx Proxy Manager

**Guide untuk deploy Aozora dengan Nginx Proxy Manager sebagai reverse proxy.**

---

## 🎯 Architecture

```
Internet
    ↓
Domain (your-domain.com:443)
    ↓
Nginx Proxy Manager (Port 80/443)
    ↓
FrankenPHP Container (Port 8080)
    ↓
Laravel Application
```

**Benefits:**
- ✅ NPM handles SSL certificates
- ✅ Easy SSL management via web UI
- ✅ Can manage multiple domains/apps
- ✅ FrankenPHP focuses on PHP processing

---

## 📋 Step 1: Configure Application for Internal Port

### **1.1 Update .env**

```bash
cd /var/www/aozoraproject
nano .env
```

**Configure these values:**

```env
# ============================================
# APPLICATION
# ============================================
APP_NAME="Your School Name"
APP_ENV=production
APP_KEY=                              # Will be generated
APP_DEBUG=false
APP_URL=https://your-domain.com       # Public URL (no port!)

# ============================================
# DATABASE
# ============================================
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_production
DB_USERNAME=aozora_prod_user
DB_PASSWORD=AOZORADBPASS2025
DB_ROOT_PASSWORD=AZORADBROOTPASS2025!

# ============================================
# CACHE & SESSION
# ============================================
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# ============================================
# REDIS
# ============================================
REDIS_HOST=redis
REDIS_PASSWORD=AOZORAREDISPASS2025!
REDIS_PORT=6379

# ============================================
# MAIL
# ============================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_ENCRYPTION=tls
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# ============================================
# FRANKENPHP
# ============================================
FRANKENPHP_NUM_THREADS=8
FRANKENPHP_NUM_WORKERS=4

# ============================================
# DOCKER PORTS (Internal)
# ============================================
APP_PORT=8080                         # Internal HTTP port
APP_PORT_SSL=8443                     # Not used with NPM

# ============================================
# SERVER (Important!)
# ============================================
SERVER_NAME=:80                       # Listen on port 80 inside container
LETS_ENCRYPT_EMAIL=                   # Leave empty (NPM handles SSL)
```

**⚠️ Important:**
- `APP_URL` = public URL (https://your-domain.com) - **NO PORT NUMBER**
- `APP_PORT` = 8080 (external mapping)
- `SERVER_NAME` = `:80` (internal container listens on 80, but Docker maps to 8080)

---

### **1.2 Update Caddyfile**

```bash
nano Caddyfile
```

**Replace with:**

```caddyfile
# Caddyfile for Nginx Proxy Manager setup
# Container listens on :80 internally, Docker exposes as 8080

{
    # No email needed - NPM handles SSL
    auto_https off
}

:80 {
    # Root directory
    root * /app/public

    # FrankenPHP Worker Mode
    php_server {
        worker /app/public/frankenphp-worker.php
        num_threads {$FRANKENPHP_NUM_THREADS:4}
        num_workers {$FRANKENPHP_NUM_WORKERS:2}
    }

    # Compression
    encode gzip zstd

    # Security headers
    header {
        # Remove sensitive info
        -Server
        -X-Powered-By
        
        # Security headers
        X-Content-Type-Options "nosniff"
        X-Frame-Options "SAMEORIGIN"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
        Permissions-Policy "geolocation=(), microphone=(), camera=()"
    }

    # Static files
    @static {
        path *.css *.js *.ico *.gif *.jpg *.jpeg *.png *.svg *.woff *.woff2 *.ttf *.eot
    }
    header @static {
        Cache-Control "public, max-age=31536000, immutable"
    }

    # Logging
    log {
        output stdout
        format console
        level INFO
    }

    # File server for static files
    file_server
}
```

---

### **1.3 Update docker-compose.yml**

```bash
nano docker-compose.yml
```

**Find the `app` service ports section and update:**

```yaml
services:
  app:
    # ... other config ...
    ports:
      - "${APP_PORT:-8080}:80"        # Map external 8080 to internal 80
      # HTTPS not needed - NPM handles it
    # ... rest of config ...
```

**Full example of app service:**

```yaml
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: aozora-app
    restart: unless-stopped
    working_dir: /app
    environment:
      - APP_ENV=${APP_ENV:-production}
      - APP_DEBUG=${APP_DEBUG:-false}
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=redis
      - REDIS_PASSWORD=${REDIS_PASSWORD}
      - CACHE_DRIVER=${CACHE_DRIVER:-redis}
      - SESSION_DRIVER=${SESSION_DRIVER:-redis}
      - QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
      - SERVER_NAME=${SERVER_NAME:-:80}
      - FRANKENPHP_NUM_THREADS=${FRANKENPHP_NUM_THREADS:-4}
      - FRANKENPHP_NUM_WORKERS=${FRANKENPHP_NUM_WORKERS:-2}
    volumes:
      - ./.env:/app/.env
      - ./storage:/app/storage
      - ./bootstrap/cache:/app/bootstrap/cache
      - ./Caddyfile:/etc/caddy/Caddyfile
    ports:
      - "${APP_PORT:-8080}:80"
    depends_on:
      - db
      - redis
    networks:
      - aozora-network
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/"]
      interval: 30s
      timeout: 10s
      retries: 3
```

---

## 📋 Step 2: Deploy Application

```bash
# Build and start containers
cd /var/www/aozoraproject
docker compose build --no-cache
docker compose up -d

# Wait for containers
sleep 20

# Check status
docker compose ps
# All should be "Up"

# Generate APP_KEY
docker compose exec app php artisan key:generate --force

# Create storage link
docker compose exec app php artisan storage:link

# Run migrations
docker compose exec app php artisan migrate --force

# Seed database
docker compose exec app php artisan db:seed --force

# Optimize
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Fix permissions
docker compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
```

---

## 📋 Step 3: Test Internal Access

```bash
# Test from server
curl -I http://localhost:8080

# Should return: HTTP/1.1 200 OK
```

**If you get 200 OK, application is ready for NPM!** ✅

---

## 📋 Step 4: Configure Nginx Proxy Manager

### **4.1 Login to NPM**

Access your Nginx Proxy Manager:
```
http://your-server-ip:81
```

**Default credentials:**
```
Email: admin@example.com
Password: changeme
```

**⚠️ Change default password after first login!**

---

### **4.2 Add Proxy Host**

1. **Go to:** `Hosts` → `Proxy Hosts` → `Add Proxy Host`

2. **Details Tab:**
   ```
   Domain Names: your-domain.com
                 www.your-domain.com  (optional)
   
   Scheme: http
   Forward Hostname/IP: aozora-app
                        (or use: 172.17.0.1 or server IP)
   
   Forward Port: 8080
   
   ☑ Cache Assets
   ☑ Block Common Exploits
   ☑ Websockets Support
   ```

3. **SSL Tab:**
   ```
   SSL Certificate: Request a new SSL Certificate
   
   ☑ Force SSL
   ☑ HTTP/2 Support
   ☑ HSTS Enabled
   ☑ HSTS Subdomains
   
   Email Address: your-email@example.com
   
   ☑ I Agree to the Let's Encrypt Terms of Service
   ```

4. **Advanced Tab (Optional but Recommended):**
   ```nginx
   # Increase timeouts for long-running requests
   proxy_read_timeout 300;
   proxy_connect_timeout 300;
   proxy_send_timeout 300;
   
   # Additional headers
   proxy_set_header X-Real-IP $remote_addr;
   proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   proxy_set_header X-Forwarded-Proto $scheme;
   proxy_set_header X-Forwarded-Host $host;
   proxy_set_header X-Forwarded-Port $server_port;
   
   # Increase upload size if needed
   client_max_body_size 100M;
   ```

5. **Click:** `Save`

---

### **4.3 Alternative: Using Server IP instead of Container Name**

If `aozora-app` doesn't work, use server IP:

1. **Get Docker bridge IP:**
   ```bash
   ip addr show docker0 | grep inet
   # Usually: 172.17.0.1
   ```

2. **Or use server's internal IP:**
   ```bash
   hostname -I | awk '{print $1}'
   ```

3. **In NPM, use:**
   ```
   Forward Hostname/IP: 172.17.0.1
   Forward Port: 8080
   ```

---

### **4.4 Alternative: Add to Same Docker Network**

**Make NPM and Aozora on same network:**

```bash
# Find NPM network
docker network ls | grep npm

# Connect Aozora to NPM network
docker network connect npm-network aozora-app

# In NPM, use container name:
# Forward Hostname/IP: aozora-app
# Forward Port: 8080
```

---

## 📋 Step 5: Verify & Test

### **5.1 Check NPM Status**

In NPM dashboard:
- ✅ Proxy Host status = **Online** (green)
- ✅ SSL status = **Active** (green padlock)

---

### **5.2 Test from Browser**

```
https://your-domain.com
```

**Expected:**
- ✅ HTTPS working (green padlock)
- ✅ Login page loads
- ✅ No certificate errors
- ✅ Can login as admin (admin@school.com / password)

---

### **5.3 Check Headers**

```bash
curl -I https://your-domain.com
```

**Should show:**
```
HTTP/2 200
server: nginx
x-content-type-options: nosniff
x-frame-options: SAMEORIGIN
...
```

---

## 🔧 Troubleshooting

### **Issue 1: 502 Bad Gateway**

**Cause:** NPM can't reach container

**Solutions:**

```bash
# Check container is running
docker compose ps

# Check container IP
docker inspect aozora-app | grep IPAddress

# Test internal connection
curl -I http://172.17.0.1:8080
```

**In NPM:**
- Try using `172.17.0.1` instead of `aozora-app`
- Or use server's main IP: `192.168.x.x`

---

### **Issue 2: SSL Certificate Failed**

**Cause:** DNS not propagated or domain not pointing to server

**Solutions:**

```bash
# Verify DNS
nslookup your-domain.com

# Should return your server IP
```

**Wait for DNS propagation (5-60 minutes)**

Then in NPM:
- Delete SSL certificate
- Request new certificate

---

### **Issue 3: Redirect Loop**

**Cause:** Mixed HTTP/HTTPS headers

**Solution in NPM Advanced:**

```nginx
# Force HTTPS scheme
proxy_set_header X-Forwarded-Proto https;
```

**And in Laravel .env:**
```env
APP_URL=https://your-domain.com
```

---

### **Issue 4: Assets Not Loading**

**Cause:** Mixed content (HTTP assets on HTTPS page)

**Solution:**

```bash
# Clear cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear

# Regenerate assets
docker compose exec app php artisan config:cache
```

**Check .env:**
```env
APP_URL=https://your-domain.com  # Must be HTTPS
```

---

## 📊 Network Diagram

```
┌─────────────────────────────────────────┐
│ Internet                                 │
└────────────────┬────────────────────────┘
                 │
                 │ HTTPS (443)
                 │
┌────────────────▼────────────────────────┐
│ Nginx Proxy Manager                     │
│ - Port 80/443 (public)                  │
│ - SSL Termination                       │
│ - Reverse Proxy                         │
└────────────────┬────────────────────────┘
                 │
                 │ HTTP (8080)
                 │
┌────────────────▼────────────────────────┐
│ Docker Container: aozora-app            │
│ - Internal: Port 80                     │
│ - External: Port 8080                   │
│ - FrankenPHP + Laravel                  │
└────────────────┬────────────────────────┘
                 │
          ┌──────┴──────┐
          │             │
┌─────────▼───────┐ ┌──▼──────────┐
│ MySQL           │ │ Redis       │
│ Container       │ │ Container   │
└─────────────────┘ └─────────────┘
```

---

## ✅ Final Checklist

- [ ] `.env` configured with `APP_PORT=8080` and `APP_URL=https://domain.com`
- [ ] `Caddyfile` configured for `:80` (no SSL, no email)
- [ ] `docker-compose.yml` ports set to `8080:80`
- [ ] Containers running: `docker compose ps`
- [ ] Internal access works: `curl http://localhost:8080`
- [ ] NPM Proxy Host created
- [ ] NPM SSL certificate active
- [ ] External access works: `https://your-domain.com`
- [ ] Login working
- [ ] Default passwords changed

---

## 🎯 Summary of Ports

| Service | Internal Port | External Port | Access |
|---------|--------------|---------------|--------|
| NPM | - | 80, 443, 81 | Public |
| Aozora App | 80 | 8080 | Via NPM |
| MySQL | 3306 | - | Internal only |
| Redis | 6379 | - | Internal only |

---

## 🔗 Next Steps

After setup complete:
1. Change default admin password
2. Configure email settings
3. Setup backups: [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)
4. Monitor logs: `docker compose logs -f app`

---

**Need help?** Check [`PRODUCTION_INSTALLATION.md`](PRODUCTION_INSTALLATION.md) or [`QUICK_FIX.md`](QUICK_FIX.md)

