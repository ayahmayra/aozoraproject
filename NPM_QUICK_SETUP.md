# ⚡ Quick Setup untuk Nginx Proxy Manager

**Quick reference untuk setup Aozora dengan NPM yang sudah ada.**

---

## 🚀 Quick Commands

### **Step 1: Update Configuration (di server)**

```bash
cd /var/www/aozoraproject

# Make script executable
chmod +x setup-npm.sh

# Run setup
./setup-npm.sh
```

**Script akan:**
- ✅ Backup konfigurasi lama
- ✅ Update Caddyfile (listen :80 saja)
- ✅ Update .env (port 8080, APP_URL)
- ✅ Restart containers

**Atau manual:**

```bash
# 1. Use NPM Caddyfile
cp Caddyfile.npm Caddyfile

# 2. Update .env
nano .env
```

**Edit .env - Important lines:**
```env
APP_URL=https://your-domain.com      # Your public domain (HTTPS, no port)
APP_PORT=8080                         # External port
SERVER_NAME=:80                       # Internal listen port
LETS_ENCRYPT_EMAIL=                   # Leave empty (NPM handles SSL)
```

```bash
# 3. Restart containers
docker compose down
docker compose up -d
sleep 20

# 4. Test
curl -I http://localhost:8080
# Should return: HTTP/1.1 200 OK
```

---

### **Step 2: Configure NPM**

**Login to NPM:**
```
http://your-server-ip:81
```

**Add Proxy Host:**
1. Click: `Hosts` → `Proxy Hosts` → `Add Proxy Host`

2. **Details Tab:**
   ```
   Domain Names: your-domain.com
   
   Scheme: http
   Forward Hostname/IP: aozora-app
                        (If doesn't work, use: 172.17.0.1 or your server IP)
   
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
   
   Email: your-email@example.com
   
   ☑ I Agree to Let's Encrypt ToS
   ```

4. **Advanced Tab (Optional):**
   ```nginx
   proxy_read_timeout 300;
   proxy_connect_timeout 300;
   proxy_send_timeout 300;
   
   proxy_set_header X-Real-IP $remote_addr;
   proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   proxy_set_header X-Forwarded-Proto $scheme;
   proxy_set_header X-Forwarded-Host $host;
   
   client_max_body_size 100M;
   ```

5. Click: `Save`

---

### **Step 3: Test**

```bash
# From server
curl -I https://your-domain.com

# From browser
https://your-domain.com
```

**Expected:**
- ✅ Green padlock (SSL active)
- ✅ Login page loads
- ✅ Can login (admin@school.com / password)

---

## 🔧 Troubleshooting

### **502 Bad Gateway?**

```bash
# Try different Forward Hostname in NPM:

# Option 1: Container name
aozora-app

# Option 2: Docker bridge IP
172.17.0.1

# Option 3: Server IP
ip addr | grep inet
# Use the main IP (e.g., 192.168.1.100)

# Test from server:
curl -I http://172.17.0.1:8080
curl -I http://localhost:8080
```

---

### **SSL Failed?**

```bash
# Check DNS
nslookup your-domain.com
# Must return your server IP

# Wait for DNS propagation (5-60 minutes)
# Then try SSL certificate again in NPM
```

---

### **Container won't start?**

```bash
# Check logs
docker compose logs app --tail=50

# Common issues:
# - Port still in use
# - APP_KEY not set
# - Database connection failed

# Check ports
sudo netstat -tulpn | grep 8080

# Restart
docker compose down
docker compose up -d
```

---

## 📊 Port Summary

```
Internet (443) 
    ↓
NPM (80/443) 
    ↓
Docker Port Mapping (8080:80)
    ↓
FrankenPHP Container (listens on 80 internally)
```

---

## ✅ Configuration Checklist

- [ ] `Caddyfile` = Listen :80 only
- [ ] `.env` APP_URL = `https://domain.com` (no port!)
- [ ] `.env` APP_PORT = `8080`
- [ ] `.env` SERVER_NAME = `:80`
- [ ] Containers running on port 8080
- [ ] Can access: `http://localhost:8080`
- [ ] NPM Proxy Host created
- [ ] NPM SSL certificate active
- [ ] Can access: `https://your-domain.com`

---

## 📚 Full Guide

For detailed explanation: [`NGINX_PROXY_MANAGER_SETUP.md`](NGINX_PROXY_MANAGER_SETUP.md)

---

**Need help?** Check [`QUICK_FIX.md`](QUICK_FIX.md)

