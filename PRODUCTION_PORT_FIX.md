# 🔧 Production Port Conflict Fix

**Error:** `Bind for 0.0.0.0:80 failed: port is already allocated`

---

## 🔍 Diagnose: Cek Service yang Menggunakan Port 80

```bash
# Check what's using port 80
sudo netstat -tulpn | grep :80

# Or using lsof
sudo lsof -i :80

# Or using ss
sudo ss -tulpn | grep :80
```

**Common services yang menggunakan port 80:**
- Apache2
- Nginx
- Other web servers

---

## ✅ Solution 1: Stop Conflicting Service (Recommended)

### **If Apache2:**
```bash
# Stop Apache2
sudo systemctl stop apache2

# Disable from auto-start
sudo systemctl disable apache2

# Verify Apache stopped
sudo systemctl status apache2

# Now start your containers
cd /var/www/aozoraproject
docker compose up -d
```

### **If Nginx:**
```bash
# Stop Nginx
sudo systemctl stop nginx

# Disable from auto-start
sudo systemctl disable nginx

# Verify Nginx stopped
sudo systemctl status nginx

# Now start your containers
cd /var/www/aozoraproject
docker compose up -d
```

### **If Other Service:**
```bash
# Find the service name from netstat/lsof output
# Example: PID 1234 is using port 80

# Kill the process
sudo kill -9 1234

# Or if it's a systemd service
sudo systemctl stop SERVICE_NAME
sudo systemctl disable SERVICE_NAME

# Start containers
cd /var/www/aozoraproject
docker compose up -d
```

---

## ✅ Solution 2: Use Different Port (Not Recommended for Production)

**Only use this if you can't stop the conflicting service.**

### **Step 1: Edit .env**
```bash
cd /var/www/aozoraproject
nano .env
```

**Change:**
```env
# OLD:
APP_PORT=80
APP_PORT_SSL=443

# NEW:
APP_PORT=8080
APP_PORT_SSL=8443
```

### **Step 2: Update URL**
```env
# If using custom ports
APP_URL=https://your-domain.com:8443
```

### **Step 3: Restart**
```bash
docker compose down
docker compose up -d
```

### **Step 4: Update Firewall**
```bash
sudo ufw allow 8080/tcp
sudo ufw allow 8443/tcp
```

**⚠️ Note:** You'll need to access via `https://your-domain.com:8443`

---

## ✅ Solution 3: Configure Reverse Proxy (Advanced)

**Use existing Nginx/Apache as reverse proxy to Docker container.**

Not recommended - better to use Solution 1.

---

## 🎯 Recommended Approach for Production

**For production with domain and SSL, you MUST use standard ports (80, 443):**

1. **Stop conflicting service** (Apache/Nginx)
2. **Let FrankenPHP/Caddy handle everything** (it has built-in web server with SSL)
3. **Use standard ports 80 & 443**

```bash
# 1. Stop Apache (if installed)
sudo systemctl stop apache2
sudo systemctl disable apache2

# 2. Stop Nginx (if installed)
sudo systemctl stop nginx
sudo systemctl disable nginx

# 3. Verify ports are free
sudo netstat -tulpn | grep -E ':80|:443'
# Should return nothing

# 4. Start Docker containers
cd /var/www/aozoraproject
docker compose down
docker compose up -d

# 5. Verify containers running
docker compose ps
# All should be "Up"

# 6. Test
curl -I http://localhost
# Should return: HTTP/1.1 200 OK
```

---

## 🔍 Full Diagnostic Commands

```bash
# 1. Check what's using port 80
echo "=== Port 80 ==="
sudo netstat -tulpn | grep :80
sudo lsof -i :80

# 2. Check what's using port 443
echo "=== Port 443 ==="
sudo netstat -tulpn | grep :443
sudo lsof -i :443

# 3. Check all web servers
echo "=== Apache Status ==="
sudo systemctl status apache2 2>/dev/null || echo "Apache not found"

echo "=== Nginx Status ==="
sudo systemctl status nginx 2>/dev/null || echo "Nginx not found"

# 4. Check Docker containers
echo "=== Docker Containers ==="
docker compose ps
```

---

## ✅ Verification After Fix

```bash
# 1. Ports should be free before starting Docker
sudo netstat -tulpn | grep -E ':80|:443'
# Should return nothing

# 2. Start containers
docker compose up -d

# 3. Check container status
docker compose ps
# All should be "Up" (not "Restarting")

# 4. Test local access
curl -I http://localhost
# Should return: HTTP/1.1 200 OK

# 5. Check logs
docker compose logs app --tail=50
# Should show no errors

# 6. Test from browser
# https://your-domain.com
# Should load with green padlock (SSL)
```

---

## 🚨 Emergency: Quick One-Liner Fix

```bash
# Stop all common web servers and start Docker
sudo systemctl stop apache2 nginx 2>/dev/null; cd /var/www/aozoraproject && docker compose down && docker compose up -d && sleep 15 && docker compose ps
```

---

## 📝 Post-Fix Checklist

- [ ] Conflicting service stopped
- [ ] Ports 80 & 443 free
- [ ] Docker containers all "Up"
- [ ] No errors in logs
- [ ] Can access http://localhost
- [ ] Can access https://your-domain.com
- [ ] SSL working (green padlock)

---

## 🔗 Next Steps

After fixing port conflict:

1. Continue with deployment: [`PRODUCTION_INSTALLATION.md`](PRODUCTION_INSTALLATION.md)
2. Or verify installation: [`PRODUCTION_CHECKLIST.md`](PRODUCTION_CHECKLIST.md)

---

**Need more help?** Check [`QUICK_FIX.md`](QUICK_FIX.md) or [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)

