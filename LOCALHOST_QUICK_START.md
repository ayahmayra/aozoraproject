# 🚀 Localhost Quick Start Guide

Deploy Aozora Education System di localhost dalam 3 langkah mudah.

---

## ✅ Prerequisites

1. **Docker Desktop** installed & running
2. **Terminal** access
3. **Flux Pro credentials** (email & password)

---

## 🎯 Quick Start (3 Steps)

### **Step 1: Setup Flux Pro**
```bash
./setup-flux-credentials.sh
```
Masukkan email & password Flux Pro Anda.

### **Step 2: Run Setup**
```bash
./setup-localhost.sh
```
Script akan otomatis:
- ✅ Create `.env` configuration
- ✅ Build Docker images (5-10 menit)
- ✅ Start all containers
- ✅ Run database migrations
- ✅ Seed test data

### **Step 3: Access Application**
```
URL: http://localhost:8080

Test Accounts:
  Admin:   admin@school.com / password
  Parent:  parent@test.com / password
  Teacher: teacher@test.com / password
  Student: student@test.com / password
```

**Done!** 🎉

---

## 🔧 If You Encounter Errors

### **Permission Denied?**
```bash
./fix-permissions.sh
./setup-localhost.sh
```

### **Port Already in Use?**
Edit `.env`:
```env
APP_PORT=9000
APP_URL=http://localhost:9000
```
Then: `docker compose restart`

### **Other Issues?**
See: `LOCALHOST_TROUBLESHOOTING.md`

---

## 📋 Useful Commands

**View Logs:**
```bash
docker compose logs -f app
```

**Restart Services:**
```bash
docker compose restart
```

**Stop Services:**
```bash
docker compose down
```

**Start Services:**
```bash
docker compose up -d
```

**Check Status:**
```bash
docker compose ps
```

---

## 🔄 Update Application

When you have code updates:

```bash
git pull
docker compose down
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
```

---

## 🗑️ Clean Up

Remove everything:
```bash
docker compose down -v
docker rmi $(docker images 'aozoraproject*' -q)
```

---

## 🚀 Features Enabled

- ✅ **FrankenPHP Worker Mode** - 2 workers, 4 threads
- ✅ **MySQL 8.0** - Persistent database
- ✅ **Redis** - Cache & sessions
- ✅ **Queue Worker** - Background jobs
- ✅ **Scheduler** - Automated tasks

---

## 📊 Performance

With Worker Mode enabled:
- **Response time:** ~50-200ms (vs ~500-1000ms standard)
- **Throughput:** 3-5x faster
- **Memory:** Persistent app state

---

## 📚 More Documentation

- `LOCALHOST_TROUBLESHOOTING.md` - Solutions for common issues
- `DEPLOYMENT.md` - Full deployment guide  
- `PERFORMANCE.md` - Worker mode optimization
- `DOMAIN_SETUP.md` - Production domain setup

---

## ❓ Quick Help

**Container keeps restarting?**
```bash
docker compose logs app
```

**Can't access localhost:8080?**
```bash
docker compose ps  # Check if containers are Up
curl http://localhost:8080  # Test connection
```

**Need to reset database?**
```bash
docker compose down -v
docker compose up -d
sleep 20
docker compose exec app php artisan migrate:fresh --seed
```

---

**That's it!** Happy coding! 🎓✨

