# 🚀 Quick Start - Localhost Installation

**Tested and verified installation steps** - Deploy dalam 3 langkah!

---

## ✅ Prerequisites

- **Docker Desktop** - Must be running
- **Git** - For cloning repository
- **macOS / Linux** - (Windows with WSL2)

---

## 📦 Installation Steps

### **Step 1: Clone Repository**

```bash
# Clone the repository
git clone https://github.com/ayahmayra/aozoraproject.git

# Navigate to project directory
cd aozoraproject

# Get specific branch (if needed)
sudo git pull origin dockerversion
```

---

### **Step 2: Run Setup**

```bash
./setup-localhost.sh
```

The script will automatically:
- ✅ Check Docker
- ✅ Setup Flux Pro credentials (if needed)
- ✅ Create `.env` file
- ✅ Build Docker images
- ✅ Start containers
- ✅ Generate APP_KEY
- ✅ Run migrations & seeders
- ✅ Create storage symlink

**Wait for setup to complete (~5-10 minutes)**

---

### **Step 3: Access Application**

```
🌐 URL: http://localhost:8080
```

**Default Login:**
| Role    | Email                | Password   |
|---------|----------------------|------------|
| Admin   | admin@school.com     | password   |
| Parent  | parent@test.com      | password   |
| Teacher | teacher@test.com     | password   |
| Student | student@test.com     | password   |

---

## 🐛 Common Issues & Quick Fixes

### **Issue 1: Flux Pro Credentials Error**

**Error:**
```
composer install failed
# or
Credentials required for composer.fluxui.dev
```

**Fix:**
```bash
# Remove old auth.json
sudo rm -f auth.json

# Run credential setup
./setup-flux-credentials.sh
```

Enter your credentials:
- Email: `your-flux-email@example.com`
- License: `your-flux-license-key`

Then run setup again:
```bash
./setup-localhost.sh
```

---

### **Issue 2: Missing APP_KEY**

**Error:**
```
MissingAppKeyException: No application encryption key has been specified
```

**Quick Fix:**
```bash
docker compose down && docker compose up -d && sleep 15 && docker compose exec app php artisan key:generate --force && docker compose exec app php artisan optimize:clear
```

**Verify:**
```bash
curl -I http://localhost:8080
# Should return: HTTP/1.1 200 OK
```

---

### **Issue 3: Port Already in Use**

**Error:**
```
ports are not available: port 80 already in use
```

**Fix:**
```bash
# Edit .env file
nano .env

# Change port:
APP_PORT=9000
APP_URL=http://localhost:9000

# Restart
docker compose down
docker compose up -d
```

Access at: `http://localhost:9000`

---

### **Issue 4: Permission Denied**

**Error:**
```
Permission denied when creating .env
# or
Permission denied for storage
```

**Fix:**
```bash
./fix-permissions.sh
./setup-localhost.sh
```

---

## 📋 Useful Commands

### **View Logs:**
```bash
docker compose logs -f app
```

### **Restart Application:**
```bash
docker compose restart app
```

### **Stop All Services:**
```bash
docker compose down
```

### **Start All Services:**
```bash
docker compose up -d
```

### **Check Status:**
```bash
docker compose ps
```

### **Access Container Shell:**
```bash
docker compose exec app bash
```

### **Run Artisan Commands:**
```bash
docker compose exec app php artisan <command>
```

### **Reset Database:**
```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

## 🎓 What's Included?

After successful installation:

✅ **FrankenPHP** - High-performance PHP server with Worker Mode  
✅ **MySQL 8.0** - Database server  
✅ **Redis** - Caching and queue backend  
✅ **Laravel 11** - Modern PHP framework  
✅ **Livewire 3** - Dynamic frontend  
✅ **Flux Pro UI** - Beautiful UI components  
✅ **Test Data** - Pre-loaded with sample users and data  

---

## 🔄 Update Application

When you have code updates:

```bash
# Pull latest code
git pull origin dockerversion

# Rebuild and restart
docker compose down
docker compose build --no-cache
docker compose up -d

# Run migrations (if any)
docker compose exec app php artisan migrate --force

# Clear caches
docker compose exec app php artisan optimize:clear
```

---

## 🧹 Clean Up

To completely remove the installation:

```bash
# Stop and remove containers
docker compose down -v

# Remove images (optional)
docker rmi aozoraproject-app aozoraproject-queue aozoraproject-scheduler

# Remove volumes (optional - will delete database data)
docker volume rm aozoraproject_mysql-data aozoraproject_redis-data
```

---

## 📚 More Documentation

- **Detailed Troubleshooting:** [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
- **Quick Fixes:** [`QUICK_FIX.md`](QUICK_FIX.md)
- **Production Deployment:** [`DEPLOYMENT.md`](DEPLOYMENT.md)
- **Performance Tuning:** [`PERFORMANCE.md`](PERFORMANCE.md)
- **Documentation Index:** [`DOCUMENTATION_INDEX.md`](DOCUMENTATION_INDEX.md)

---

## 🆘 Still Having Issues?

1. Check logs: `docker compose logs app --tail=100`
2. View quick fixes: [`QUICK_FIX.md`](QUICK_FIX.md)
3. Read troubleshooting: [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
4. Restart Docker Desktop
5. Run: `./fix-permissions.sh`

---

## ✨ Success!

Your Aozora Education Management System is now running! 🎉

**Next Steps:**
1. Login as Admin: `http://localhost:8080` (admin@school.com / password)
2. Explore the dashboard
3. Create your own users
4. Customize settings

**Happy Teaching! 📚**

