# Domain Setup Guide

Panduan lengkap untuk mengatur domain pada production deployment.

## 📋 Checklist Setup Domain

- [ ] Domain sudah terdaftar
- [ ] DNS sudah dikonfigurasi
- [ ] Server sudah bisa diakses via IP
- [ ] Port 80 dan 443 terbuka di firewall
- [ ] SSL certificate (otomatis via Let's Encrypt atau manual)

---

## 🌐 Langkah 1: Konfigurasi DNS

### A. DNS Records yang Dibutuhkan

Tambahkan record berikut di DNS provider Anda (Cloudflare, Namecheap, dll):

```
Type    Name    Value               TTL
A       @       123.456.789.10      Auto
A       www     123.456.789.10      Auto
AAAA    @       2001:db8::1         Auto (optional, untuk IPv6)
```

Ganti `123.456.789.10` dengan IP address server production Anda.

### B. Verifikasi DNS Propagation

Tunggu DNS propagate (1-48 jam), cek dengan:

```bash
# Check A record
dig yourdomain.com +short

# Check dengan online tools
# - https://dnschecker.org
# - https://www.whatsmydns.net
```

---

## 🔧 Langkah 2: Konfigurasi Environment (.env)

Edit file `.env` di server production:

```env
# ===========================================
# DOMAIN CONFIGURATION
# ===========================================

# Main application URL
APP_URL=https://yourdomain.com

# Ports (default untuk production)
APP_PORT=80
APP_PORT_SSL=443

# ===========================================
# SESSION & COOKIE (penting untuk HTTPS!)
# ===========================================

SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_DOMAIN=yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# ===========================================
# SANCTUM (jika pakai API)
# ===========================================

SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com

# ===========================================
# MAIL
# ===========================================

MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# ===========================================
# TRUSTED PROXIES (jika pakai load balancer/CDN)
# ===========================================

# Jika pakai Cloudflare
# TRUSTED_PROXIES=173.245.48.0/20,103.21.244.0/22,103.22.200.0/22,103.31.4.0/22,141.101.64.0/18,108.162.192.0/18,190.93.240.0/20,188.114.96.0/20,197.234.240.0/22,198.41.128.0/17,162.158.0.0/15,104.16.0.0/13,104.24.0.0/14,172.64.0.0/13,131.0.72.0/22
```

---

## 📝 Langkah 3: Konfigurasi Caddy (FrankenPHP)

### Option A: Single Domain (Recommended)

Gunakan `Caddyfile.production`:

```bash
# Ganti Caddyfile default dengan production version
cp Caddyfile.production Caddyfile
```

Edit `Caddyfile`:

```caddy
{
    frankenphp
    order php_server before file_server
    email admin@yourdomain.com  # <-- Ganti dengan email Anda untuk Let's Encrypt
}

# HTTP Redirect
http://yourdomain.com, http://www.yourdomain.com {
    redir https://yourdomain.com{uri} permanent
}

# HTTPS Main Site
https://yourdomain.com, https://www.yourdomain.com {
    root * /app/public
    encode gzip zstd
    php_server
    
    # ... rest of config
}
```

### Option B: Multiple Subdomains

```caddy
# Main site
https://yourdomain.com {
    root * /app/public
    php_server
}

# Admin subdomain
https://admin.yourdomain.com {
    root * /app/public
    php_server
}

# API subdomain
https://api.yourdomain.com {
    root * /app/public
    php_server
}
```

---

## 🔐 Langkah 4: SSL Certificate

### A. Automatic SSL (Let's Encrypt) - Recommended

Caddy/FrankenPHP otomatis menghandle SSL via Let's Encrypt!

**Requirements:**
- Domain sudah pointing ke server (DNS resolved)
- Port 80 dan 443 terbuka
- Email valid di Caddyfile

**Tidak perlu konfigurasi tambahan!** SSL akan otomatis:
- Generate certificate
- Auto-renew sebelum expire
- Redirect HTTP ke HTTPS

### B. Manual SSL Certificate

Jika pakai SSL certificate sendiri (misal dari provider):

```caddy
https://yourdomain.com {
    tls /path/to/cert.pem /path/to/key.pem
    root * /app/public
    php_server
}
```

Mount certificate ke container:

```yaml
# docker-compose.yml
services:
  app:
    volumes:
      - ./certs:/certs:ro
```

---

## 🚀 Langkah 5: Rebuild dan Deploy

```bash
# 1. Stop containers
docker compose down

# 2. Update .env file
nano .env

# 3. Update Caddyfile
cp Caddyfile.production Caddyfile
nano Caddyfile  # Edit domain

# 4. Rebuild dengan no-cache
docker compose build --no-cache

# 5. Start dengan production config
docker compose up -d

# 6. Check logs
docker compose logs -f app

# 7. Verify SSL
curl -I https://yourdomain.com

# 8. Test application
curl https://yourdomain.com/up
```

---

## ✅ Langkah 6: Verifikasi

### A. Check DNS Resolution

```bash
dig yourdomain.com
nslookup yourdomain.com
```

### B. Check SSL Certificate

```bash
# Check SSL info
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Or use online tools
# - https://www.ssllabs.com/ssltest/
# - https://www.sslshopper.com/ssl-checker.html
```

### C. Check Application

```bash
# Health check
curl https://yourdomain.com/up

# Full test
curl -I https://yourdomain.com

# Check redirect
curl -I http://yourdomain.com  # Should redirect to https
```

### D. Check Logs

```bash
# Application logs
docker compose logs app | tail -100

# Caddy logs (inside container)
docker compose exec app cat /var/log/caddy/access.log
```

---

## 🔧 Troubleshooting

### SSL Certificate Not Working

**Symptom:** Error "certificate is not valid" atau "ERR_CERT_COMMON_NAME_INVALID"

**Solutions:**

1. **Verify DNS:**
   ```bash
   dig yourdomain.com +short
   # Should return your server IP
   ```

2. **Check Caddy logs:**
   ```bash
   docker compose logs app | grep -i "tls\|certificate\|acme"
   ```

3. **Port 80 must be accessible** (Let's Encrypt needs it for verification):
   ```bash
   # Check from external
   telnet yourdomain.com 80
   ```

4. **Wait for propagation:**
   Let's Encrypt may take a few minutes to issue certificate.

5. **Force SSL renew:**
   ```bash
   docker compose exec app caddy reload --config /etc/caddy/Caddyfile
   ```

### Domain Not Accessible

**Symptom:** Cannot access site via domain

**Solutions:**

1. **Check DNS:**
   ```bash
   dig yourdomain.com
   # Should return server IP
   ```

2. **Check firewall:**
   ```bash
   # Ubuntu/Debian
   sudo ufw status
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   
   # CentOS/RHEL
   sudo firewall-cmd --list-all
   sudo firewall-cmd --permanent --add-port=80/tcp
   sudo firewall-cmd --permanent --add-port=443/tcp
   sudo firewall-cmd --reload
   ```

3. **Check container:**
   ```bash
   docker compose ps
   # app should be "healthy"
   ```

4. **Check from server itself:**
   ```bash
   curl localhost
   curl http://yourdomain.com
   ```

### Redirect Loop

**Symptom:** Too many redirects

**Solutions:**

1. **Check Cloudflare SSL mode** (if using Cloudflare):
   - Set to "Full (strict)" NOT "Flexible"
   
2. **Check TRUSTED_PROXIES in .env:**
   ```env
   TRUSTED_PROXIES=*  # For testing only!
   ```

3. **Check Caddyfile:**
   Ensure HTTP redirect is correct:
   ```caddy
   http://yourdomain.com {
       redir https://yourdomain.com{uri} permanent
   }
   ```

### Mixed Content Warnings

**Symptom:** Assets loading via HTTP on HTTPS page

**Solutions:**

1. **Check APP_URL:**
   ```env
   APP_URL=https://yourdomain.com  # Must be https://
   ```

2. **Rebuild assets:**
   ```bash
   docker compose exec app npm run build
   ```

3. **Clear cache:**
   ```bash
   docker compose exec app php artisan cache:clear
   docker compose exec app php artisan config:cache
   ```

---

## 📊 Monitoring Domain

### Check Domain Expiry

Set reminder untuk renew domain sebelum expire!

```bash
whois yourdomain.com | grep -i "expir"
```

### Monitor SSL Certificate

```bash
# Check expiry date
openssl s_client -connect yourdomain.com:443 2>/dev/null | openssl x509 -noout -dates
```

Let's Encrypt certificates valid 90 days, auto-renew 30 days before expiry.

---

## 🔄 Changing Domain

Jika perlu ganti domain:

1. Update DNS ke server baru
2. Update `.env`:
   ```env
   APP_URL=https://newdomain.com
   SESSION_DOMAIN=newdomain.com
   ```
3. Update `Caddyfile`
4. Rebuild dan restart:
   ```bash
   docker compose down
   docker compose build --no-cache
   docker compose up -d
   ```
5. Clear cache:
   ```bash
   docker compose exec app php artisan config:cache
   ```

---

## 🛡️ Security Best Practices

1. **Always use HTTPS** in production
2. **Enable HSTS** (already in Caddyfile)
3. **Set secure cookies**:
   ```env
   SESSION_SECURE_COOKIE=true
   ```
4. **Use strong SESSION_DOMAIN**:
   ```env
   SESSION_DOMAIN=.yourdomain.com  # Include subdomain
   ```
5. **Monitor certificate expiry**
6. **Keep Caddy/FrankenPHP updated**

---

## 📞 Support

Jika masih ada masalah, check:
- Caddy documentation: https://caddyserver.com/docs/
- FrankenPHP documentation: https://frankenphp.dev/
- Laravel documentation: https://laravel.com/docs/deployment

---

**Happy Deploying! 🚀**

