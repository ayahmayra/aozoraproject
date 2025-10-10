# 🚀 Production Installation Guide

**Complete step-by-step guide** untuk deploy ke production server dengan domain.

---

## 📋 Prerequisites Checklist

Sebelum mulai, pastikan Anda memiliki:

- ✅ **VPS/Server** - Ubuntu 20.04+ atau Debian 11+ (Minimal: 2 CPU, 2GB RAM, 20GB storage)
- ✅ **Domain Name** - Domain yang sudah terdaftar (misal: `school.example.com`)
- ✅ **DNS Access** - Akses untuk mengubah DNS records
- ✅ **Server Access** - SSH access ke server (root atau sudo user)
- ✅ **Flux Pro License** - Email dan license key dari https://fluxui.dev
- ✅ **Email untuk SSL** - Email untuk Let's Encrypt certificate notifications

---

## 🎯 Installation Overview

```
Step 1: Server Preparation     (5 min)
Step 2: DNS Configuration       (5 min, propagation: 5-60 min)
Step 3: Clone Repository        (2 min)
Step 4: Configure Environment   (5 min)
Step 5: Setup & Deploy          (10-15 min)
Step 6: Verify & Test           (5 min)
Step 7: Security & Optimization (5 min)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total Time: ~40-60 minutes
```

---

## 📝 Step 1: Server Preparation

### **1.1 Connect to Server**

```bash
# SSH ke server (dari komputer lokal)
ssh root@YOUR_SERVER_IP

# Atau jika menggunakan user biasa:
ssh username@YOUR_SERVER_IP
```

---

### **1.2 Update System**

```bash
# Update package list
sudo apt update

# Upgrade existing packages
sudo apt upgrade -y

# Install essential tools
sudo apt install -y curl wget git nano htop unzip
```

---

### **1.3 Install Docker**

```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add current user to docker group (if not root)
sudo usermod -aG docker $USER

# Start Docker service
sudo systemctl enable docker
sudo systemctl start docker

# Verify installation
docker --version
```

---

### **1.4 Install Docker Compose**

```bash
# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Make it executable
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker-compose --version
```

**✅ Checkpoint:** Docker dan Docker Compose terinstall dengan baik.

---

## 🌐 Step 2: DNS Configuration

### **2.1 Configure DNS Records**

Login ke DNS provider Anda (Cloudflare, Namecheap, GoDaddy, dll) dan tambahkan:

**A Record:**
```
Type: A
Name: @ (atau root domain)
Value: YOUR_SERVER_IP
TTL: 300 (or Auto)
```

**Optional - Subdomain:**
```
Type: A
Name: school (atau subdomain pilihan Anda)
Value: YOUR_SERVER_IP
TTL: 300
```

**Example:**
- Domain: `example.com` → Points to `203.0.113.10`
- Subdomain: `school.example.com` → Points to `203.0.113.10`

---

### **2.2 Verify DNS Propagation**

```bash
# Test dari server
nslookup your-domain.com

# Atau
dig your-domain.com

# Should return your SERVER_IP
```

**⚠️ Important:** DNS propagation bisa memakan waktu 5-60 menit. Tunggu sampai domain resolve ke IP server Anda sebelum melanjutkan.

**✅ Checkpoint:** Domain sudah resolve ke IP server.

---

## 📦 Step 3: Clone Repository

### **3.1 Navigate to Install Directory**

```bash
# Create directory for application
sudo mkdir -p /var/www
cd /var/www

# Clone repository
sudo git clone https://github.com/ayahmayra/aozoraproject.git

# Enter project directory
cd aozoraproject

# Get specific branch (if needed)
sudo git pull origin dockerversion

# Fix permissions
sudo chown -R $USER:$USER /var/www/aozoraproject
```

---

### **3.2 Verify Files**

```bash
# List files
ls -la

# Should see:
# - docker-compose.yml
# - Dockerfile
# - setup-localhost.sh
# - Caddyfile
# - etc.
```

**✅ Checkpoint:** Repository berhasil di-clone.

---

## ⚙️ Step 4: Configure Environment

### **4.1 Setup Flux Pro Credentials**

```bash
# Run credential setup
./setup-flux-credentials.sh
```

**Enter your credentials:**
- Email: `your-email@example.com`
- License Key: `your-flux-license-key`

**Get credentials from:** https://fluxui.dev → Account → Licenses

---

### **4.2 Create Production .env File**

```bash
# Copy example
cp .env.example .env

# Edit .env
nano .env
```

**Configure these REQUIRED values:**

```env
# ============================================
# APPLICATION
# ============================================
APP_NAME="Your School Name"
APP_ENV=production
APP_KEY=                      # Leave empty, will be generated
APP_DEBUG=false               # IMPORTANT: Set to false!
APP_URL=https://your-domain.com

# ============================================
# DATABASE
# ============================================
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_production
DB_USERNAME=aozora_prod_user
DB_PASSWORD=CHANGE_THIS_STRONG_DB_PASSWORD_123!
DB_ROOT_PASSWORD=CHANGE_THIS_STRONG_ROOT_PASSWORD_456!

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
REDIS_PASSWORD=CHANGE_THIS_STRONG_REDIS_PASSWORD_789!
REDIS_PORT=6379

# ============================================
# MAIL (Configure your mail service)
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
# FRANKENPHP (Worker Mode)
# ============================================
FRANKENPHP_NUM_THREADS=8      # Adjust based on CPU cores
FRANKENPHP_NUM_WORKERS=4      # Usually: cores / 2

# ============================================
# SERVER
# ============================================
SERVER_NAME=your-domain.com
LETS_ENCRYPT_EMAIL=your-email@example.com
APP_PORT=80
APP_PORT_SSL=443
```

**⚠️ SECURITY: Change all passwords!**
- DB_PASSWORD
- DB_ROOT_PASSWORD  
- REDIS_PASSWORD

**Save file:** Press `Ctrl+O`, then `Enter`, then `Ctrl+X`

---

### **4.3 Update Caddyfile for Production**

```bash
# Backup original
cp Caddyfile Caddyfile.backup

# Use production Caddyfile
cp Caddyfile.production Caddyfile

# Edit if needed
nano Caddyfile
```

**Verify content:**
```caddyfile
{
    email your-email@example.com
}

{$SERVER_NAME:localhost} {
    # ... rest of config
}
```

Make sure `email` matches your `.env` `LETS_ENCRYPT_EMAIL`.

**✅ Checkpoint:** Environment configured.

---

## 🚀 Step 5: Setup & Deploy

### **5.1 Make Scripts Executable**

```bash
chmod +x setup-localhost.sh
chmod +x setup-flux-credentials.sh
chmod +x fix-permissions.sh
chmod +x docker/scripts/*.sh
```

---

### **5.2 Build Docker Images**

```bash
# Build images (this will take 5-10 minutes)
docker compose build --no-cache

# Verify images created
docker images | grep aozoraproject
```

**Expected output:**
```
aozoraproject-app
aozoraproject-queue
aozoraproject-scheduler
```

---

### **5.3 Start Services**

```bash
# Start all containers
docker compose up -d

# Wait for containers to initialize
sleep 20

# Check container status
docker compose ps
```

**All containers should be "Up" (not "Restarting").**

---

### **5.4 Generate Application Key**

```bash
# Generate APP_KEY
docker compose exec app php artisan key:generate --force

# Verify key generated
grep "APP_KEY=" .env

# Should show: APP_KEY=base64:...
```

---

### **5.5 Create Storage Link**

```bash
# Create symbolic link
docker compose exec app php artisan storage:link

# Verify
docker compose exec app ls -la /app/public/storage
```

---

### **5.6 Run Database Migrations**

```bash
# Run migrations
docker compose exec app php artisan migrate --force

# Expected output: All migrations should run successfully
```

---

### **5.7 Seed Database (Optional but Recommended)**

```bash
# Seed with test data
docker compose exec app php artisan db:seed --force

# This creates:
# - Admin user: admin@school.com / password
# - Sample teachers, parents, students
# - Sample subjects, schedules
```

**⚠️ Important:** Change default passwords after first login!

---

### **5.8 Optimize for Production**

```bash
# Cache configuration
docker compose exec app php artisan config:cache

# Cache routes
docker compose exec app php artisan route:cache

# Cache views
docker compose exec app php artisan view:cache

# Cache events
docker compose exec app php artisan event:cache
```

---

### **5.9 Set Correct Permissions**

```bash
# Inside container
docker compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
docker compose exec app chmod -R 775 /app/storage /app/bootstrap/cache
```

**✅ Checkpoint:** Application deployed!

---

## ✅ Step 6: Verify & Test

### **6.1 Check Container Health**

```bash
# All containers should be "healthy" or "Up"
docker compose ps

# Check logs for errors
docker compose logs app --tail=50
docker compose logs db --tail=50
docker compose logs redis --tail=50
```

**No errors should appear.**

---

### **6.2 Test HTTP/HTTPS Access**

```bash
# From server
curl -I http://localhost

# Should return: HTTP/1.1 200 OK
```

**From browser:**
```
https://your-domain.com
```

**Expected:**
- ✅ HTTPS (SSL) working (green padlock)
- ✅ Login page loads
- ✅ No certificate errors

---

### **6.3 Test Login**

**Default credentials:**
```
Email: admin@school.com
Password: password
```

**Test:**
1. Login as admin
2. Navigate to dashboard
3. Check if data loads properly

---

### **6.4 Check SSL Certificate**

```bash
# Check certificate
docker compose exec app curl -I https://your-domain.com

# Or from browser: Click padlock → Certificate info
```

**Should show:**
- Issued by: Let's Encrypt
- Valid for your domain
- Expires in ~90 days

**✅ Checkpoint:** Application accessible via HTTPS!

---

## 🔒 Step 7: Security & Optimization

### **7.1 Change Default Passwords**

**Via Web Interface:**
1. Login as admin
2. Go to Profile/Settings
3. Change password
4. Repeat for other test accounts

**Or via command line:**
```bash
docker compose exec app php artisan tinker

# Inside tinker:
$user = User::where('email', 'admin@school.com')->first();
$user->password = Hash::make('NewStrongPassword123!');
$user->save();
exit
```

---

### **7.2 Configure Firewall**

```bash
# Install UFW (if not installed)
sudo apt install ufw

# Allow SSH (IMPORTANT: Do this first!)
sudo ufw allow 22/tcp

# Allow HTTP
sudo ufw allow 80/tcp

# Allow HTTPS
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

---

### **7.3 Setup Automatic Backups**

```bash
# Make backup script executable
chmod +x docker/scripts/backup.sh

# Test backup
./docker/scripts/backup.sh

# Setup cron for daily backups
crontab -e

# Add this line (backup daily at 2 AM):
0 2 * * * cd /var/www/aozoraproject && ./docker/scripts/backup.sh >> /var/log/aozora-backup.log 2>&1
```

Backups akan tersimpan di `storage/backups/`.

---

### **7.4 Setup Log Rotation**

```bash
# Create logrotate config
sudo nano /etc/logrotate.d/aozoraproject
```

**Add:**
```
/var/www/aozoraproject/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

### **7.5 Monitor Resources**

```bash
# Check disk usage
df -h

# Check memory
free -h

# Check container stats
docker stats

# Check logs in real-time
docker compose logs -f app
```

---

### **7.6 Enable Production Monitoring**

**Inside container:**
```bash
# Enable opcache status
docker compose exec app php -i | grep opcache

# Should show opcache enabled
```

---

## 🎉 Installation Complete!

Your production system is now running at:
```
🌐 https://your-domain.com
```

---

## 📊 Post-Installation Checklist

- [ ] Application accessible via HTTPS
- [ ] SSL certificate valid (green padlock)
- [ ] Admin login working
- [ ] Dashboard loads properly
- [ ] Default passwords changed
- [ ] Firewall configured
- [ ] Automatic backups setup
- [ ] DNS propagated completely
- [ ] Email sending configured
- [ ] Log rotation configured

---

## 🔄 Maintenance Tasks

### **Daily:**
- Monitor logs for errors
- Check disk space

### **Weekly:**
- Review backup files
- Check SSL certificate expiry
- Monitor application performance

### **Monthly:**
- Update Docker images
- Review security logs
- Test backup restoration

---

## 🆘 Troubleshooting

### **Issue: SSL Certificate Not Generated**

```bash
# Check Caddy logs
docker compose logs app | grep -i caddy

# Check if domain resolves
nslookup your-domain.com

# Restart to retry
docker compose restart app
```

---

### **Issue: Container Keeps Restarting**

```bash
# Check logs
docker compose logs app --tail=100

# Common causes:
# - APP_KEY not set
# - Database connection failed
# - Permission issues
```

---

### **Issue: Can't Access from Browser**

```bash
# Check if ports are open
sudo netstat -tulpn | grep -E '80|443'

# Check firewall
sudo ufw status

# Check DNS
nslookup your-domain.com
```

---

### **Issue: Database Connection Failed**

```bash
# Check database container
docker compose ps db

# Check database logs
docker compose logs db --tail=50

# Verify credentials in .env
cat .env | grep DB_
```

---

## 📚 Next Steps

1. **Customize Application:**
   - Update organization settings
   - Upload school logo
   - Configure email templates

2. **Add Real Data:**
   - Remove test data
   - Add real teachers
   - Add real students
   - Import subjects

3. **Configure Backups:**
   - Setup off-site backup storage
   - Test backup restoration
   - Document recovery procedures

4. **Monitor & Optimize:**
   - Setup monitoring tools
   - Configure alerts
   - Tune performance based on usage

---

## 🔗 Related Documentation

- **Update Guide:** [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)
- **Domain Setup Details:** [`DOMAIN_SETUP.md`](DOMAIN_SETUP.md)
- **Full Deployment Guide:** [`DEPLOYMENT.md`](DEPLOYMENT.md)
- **Performance Tuning:** [`PERFORMANCE.md`](PERFORMANCE.md)

---

## 🎯 Quick Commands Reference

```bash
# View logs
docker compose logs -f app

# Restart application
docker compose restart app

# Stop all services
docker compose down

# Start all services
docker compose up -d

# Backup database
./docker/scripts/backup.sh

# Update application
./docker/scripts/update.sh

# Check status
docker compose ps

# Access shell
docker compose exec app bash

# Clear cache
docker compose exec app php artisan cache:clear
```

---

**🎊 Congratulations! Your Aozora Education System is now running in production!**

Need help? Check [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md) or [`QUICK_FIX.md`](QUICK_FIX.md)

