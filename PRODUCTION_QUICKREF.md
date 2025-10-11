# 🚀 Production Deployment - Quick Reference

**One-page reference** - Print this for quick access during deployment!

---

## 📝 Pre-Flight Checklist

- [ ] Server: Ubuntu 20.04+, 2GB RAM, 2 CPU, 20GB disk
- [ ] Domain: Registered & DNS access available
- [ ] Flux Pro: Email + License key ready
- [ ] Passwords: Strong passwords prepared

---

## ⚡ 7-Step Deployment

### **1️⃣ Install Docker (5 min)**
```bash
ssh root@SERVER_IP
curl -fsSL https://get.docker.com -o get-docker.sh && sudo sh get-docker.sh
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### **2️⃣ Setup DNS (5 min + wait)**
```
A Record: @ → SERVER_IP
A Record: subdomain → SERVER_IP (optional)
Test: nslookup your-domain.com
```

### **3️⃣ Clone Repository (2 min)**
```bash
sudo mkdir -p /var/www && cd /var/www
sudo git clone https://github.com/ayahmayra/aozoraproject.git
cd aozoraproject
sudo chown -R $USER:$USER /var/www/aozoraproject
```

### **4️⃣ Configure (5 min)**
```bash
./setup-flux-credentials.sh
# Enter Flux email + license

cp .env.example .env
nano .env
```

**Edit .env - Required:**
```env
APP_NAME="School Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_PASSWORD=STRONG_PASS_123
DB_ROOT_PASSWORD=STRONG_ROOT_456
REDIS_PASSWORD=STRONG_REDIS_789
SERVER_NAME=your-domain.com
LETS_ENCRYPT_EMAIL=your@email.com
```

### **5️⃣ Build & Deploy (15 min)**
```bash
docker compose build --no-cache
docker compose up -d
sleep 20
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### **6️⃣ Verify (5 min)**
```bash
docker compose ps                    # All "Up"?
curl -I https://your-domain.com     # HTTP/2 200?
```
**Browser:** https://your-domain.com → Login: admin@school.com / password

### **7️⃣ Secure (10 min)**
```bash
# Firewall
sudo ufw allow 22/tcp && sudo ufw allow 80/tcp && sudo ufw allow 443/tcp
sudo ufw enable

# Backup cron
chmod +x docker/scripts/backup.sh
crontab -e
# Add: 0 2 * * * cd /var/www/aozoraproject && ./docker/scripts/backup.sh
```

**Change passwords in web UI!**

---

## 🐛 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| Container restarting | `docker compose logs app --tail=100` |
| SSL not working | Check DNS: `nslookup domain.com`, then `docker compose restart app` |
| 502 Bad Gateway | `docker compose restart app` |
| Permission denied | `docker compose exec app chmod -R 775 /app/storage` |

---

## 🔧 Essential Commands

```bash
# View logs
docker compose logs -f app

# Restart
docker compose restart app

# Stop/Start
docker compose down
docker compose up -d

# Status
docker compose ps

# Backup
./docker/scripts/backup.sh

# Shell access
docker compose exec app bash
```

---

## ✅ Success Checklist

- [ ] HTTPS working (green padlock)
- [ ] Admin login successful
- [ ] Dashboard loads
- [ ] Default passwords changed
- [ ] Firewall enabled
- [ ] Backups scheduled

---

## 📞 Help

- **Full Guide:** `PRODUCTION_INSTALLATION.md`
- **Checklist:** `PRODUCTION_CHECKLIST.md`
- **Quick Fixes:** `QUICK_FIX.md`

---

**🎯 Deploy Time: ~40-60 minutes**

