# ✅ Production Deployment Checklist

Quick reference checklist untuk production deployment.

---

## 📋 Pre-Deployment Checklist

### **Server Requirements:**
- [ ] VPS/Server: Ubuntu 20.04+ atau Debian 11+
- [ ] Specs: Min 2 CPU, 2GB RAM, 20GB storage
- [ ] SSH access ke server (root atau sudo)
- [ ] Port 22, 80, 443 accessible

### **Domain & DNS:**
- [ ] Domain terdaftar dan aktif
- [ ] DNS access tersedia
- [ ] A Record: @ atau root → Server IP
- [ ] A Record (optional): subdomain → Server IP
- [ ] DNS propagation complete (test: `nslookup domain.com`)

### **Credentials Ready:**
- [ ] Flux Pro email
- [ ] Flux Pro license key
- [ ] Email untuk Let's Encrypt SSL
- [ ] Strong passwords prepared (DB, Redis)

---

## 🚀 Deployment Steps

### **Step 1: Server Setup** (10 min)

```bash
# Connect
ssh root@SERVER_IP

# Update system
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git nano htop unzip

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo systemctl enable docker
sudo systemctl start docker

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify
docker --version
docker-compose --version
```

**✅ Done: Docker installed**

---

### **Step 2: Clone & Prepare** (5 min)

```bash
# Clone repository
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/ayahmayra/aozoraproject.git
cd aozoraproject
sudo git pull origin dockerversion
sudo chown -R $USER:$USER /var/www/aozoraproject

# Setup Flux credentials
./setup-flux-credentials.sh
# Enter email: your-flux-email
# Enter key: your-flux-license

# Create .env
cp .env.example .env
nano .env
```

**✅ Done: Repository cloned & configured**

---

### **Step 3: Configure .env** (5 min)

**Critical settings to change:**

```env
APP_NAME="Your School Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_DATABASE=aozora_production
DB_USERNAME=aozora_prod_user
DB_PASSWORD=STRONG_PASSWORD_123
DB_ROOT_PASSWORD=STRONG_ROOT_PASSWORD_456

REDIS_PASSWORD=STRONG_REDIS_PASSWORD_789

SERVER_NAME=your-domain.com
LETS_ENCRYPT_EMAIL=your-email@example.com

FRANKENPHP_NUM_THREADS=8
FRANKENPHP_NUM_WORKERS=4
```

**✅ Done: Environment configured**

---

### **Step 4: Deploy** (15 min)

```bash
# Build images
docker compose build --no-cache

# Start services
docker compose up -d

# Wait for containers
sleep 20

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

**✅ Done: Application deployed**

---

### **Step 5: Verify** (5 min)

```bash
# Check containers
docker compose ps

# Check logs
docker compose logs app --tail=50

# Test locally
curl -I http://localhost

# Test from browser
# https://your-domain.com
```

**Expected:**
- [ ] All containers "Up"
- [ ] No errors in logs
- [ ] HTTPS working (green padlock)
- [ ] Login page loads
- [ ] Can login as admin

**✅ Done: Application verified**

---

### **Step 6: Security** (10 min)

```bash
# Configure firewall
sudo apt install ufw
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Change default passwords
# Login to web → Profile → Change Password

# Setup backups
chmod +x docker/scripts/backup.sh
crontab -e
# Add: 0 2 * * * cd /var/www/aozoraproject && ./docker/scripts/backup.sh
```

**✅ Done: Security configured**

---

## 🎯 Quick Verification

**Run these commands to verify everything:**

```bash
# 1. Containers running?
docker compose ps
# All should be "Up"

# 2. APP_KEY set?
grep "APP_KEY=" .env
# Should show: APP_KEY=base64:...

# 3. Database working?
docker compose exec app php artisan migrate:status
# Should show all migrations run

# 4. HTTPS working?
curl -I https://your-domain.com
# Should return: HTTP/2 200

# 5. SSL certificate valid?
echo | openssl s_client -connect your-domain.com:443 2>/dev/null | grep -A2 "Verify return code"
# Should show: Verify return code: 0 (ok)
```

---

## ⚠️ Common Issues

### **Issue 1: Container Restarting**
```bash
docker compose logs app --tail=100
# Check for APP_KEY or database errors
```

### **Issue 2: SSL Not Working**
```bash
# Check domain resolves
nslookup your-domain.com

# Restart Caddy
docker compose restart app
```

### **Issue 3: 502 Bad Gateway**
```bash
# Check if app container is running
docker compose ps app

# Restart if needed
docker compose restart app
```

### **Issue 4: Permission Denied**
```bash
docker compose exec app chmod -R 775 /app/storage /app/bootstrap/cache
docker compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
```

---

## 📊 Post-Deployment Tasks

### **Immediate (Day 1):**
- [ ] Change all default passwords
- [ ] Test all major features
- [ ] Configure email settings
- [ ] Upload school logo
- [ ] Update organization info

### **Week 1:**
- [ ] Remove test data
- [ ] Add real teachers
- [ ] Add real students
- [ ] Test backup restoration
- [ ] Monitor logs daily

### **Month 1:**
- [ ] Review performance metrics
- [ ] Optimize based on usage
- [ ] Setup monitoring alerts
- [ ] Document custom changes
- [ ] Train users

---

## 🔄 Maintenance Schedule

### **Daily:**
```bash
# Check logs
docker compose logs app --tail=50 | grep -i error

# Check disk space
df -h

# Check memory
free -h
```

### **Weekly:**
```bash
# Check backups exist
ls -lh storage/backups/

# Check SSL certificate expiry
echo | openssl s_client -connect your-domain.com:443 2>/dev/null | grep -A2 "Validity"

# Review performance
docker stats
```

### **Monthly:**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Check Docker versions
docker version
docker-compose version

# Review logs
tail -n 1000 storage/logs/laravel.log | grep -i error
```

---

## 🆘 Emergency Commands

### **Restart Everything:**
```bash
docker compose restart
```

### **Stop Everything:**
```bash
docker compose down
```

### **Start Everything:**
```bash
docker compose up -d
```

### **View Real-time Logs:**
```bash
docker compose logs -f app
```

### **Access Container Shell:**
```bash
docker compose exec app bash
```

### **Backup Now:**
```bash
./docker/scripts/backup.sh
```

### **Restore from Backup:**
```bash
# Stop services
docker compose down

# Restore database
docker compose up -d db
docker compose exec -T db mysql -u root -p$DB_ROOT_PASSWORD $DB_DATABASE < backup.sql

# Start all services
docker compose up -d
```

---

## 📞 Support Resources

- **Detailed Guide:** [`PRODUCTION_INSTALLATION.md`](PRODUCTION_INSTALLATION.md)
- **Quick Fixes:** [`QUICK_FIX.md`](QUICK_FIX.md)
- **Troubleshooting:** [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
- **Updates:** [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)
- **Performance:** [`PERFORMANCE.md`](PERFORMANCE.md)

---

## 🎯 Success Criteria

Your deployment is successful when:

- ✅ Domain accessible via HTTPS
- ✅ SSL certificate valid (green padlock)
- ✅ Admin can login
- ✅ Dashboard loads properly
- ✅ Data displays correctly
- ✅ No errors in logs
- ✅ Backups working
- ✅ Firewall configured
- ✅ Default passwords changed
- ✅ Email sending works

---

## 📝 Deployment Info Template

**Fill this after deployment for your records:**

```
Deployment Date: _______________
Server IP: _______________
Domain: _______________
SSL Email: _______________

Database Name: _______________
Database User: _______________
Database Password: _______________

Redis Password: _______________

Admin Email: _______________
Admin Password: _______________

Backup Location: _______________
Backup Schedule: _______________

Notes:
_________________________________
_________________________________
_________________________________
```

---

**🎊 Ready to Deploy? Follow [`PRODUCTION_INSTALLATION.md`](PRODUCTION_INSTALLATION.md)!**

