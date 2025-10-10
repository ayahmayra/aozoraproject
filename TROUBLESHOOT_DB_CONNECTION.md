# 🔧 Troubleshooting: Database Connection Failed

**Error:** `getaddrinfo for db failed: Temporary failure in name resolution`

**Cause:** App container tidak bisa resolve hostname "db" (database container).

---

## ✅ Solution Steps

### **Step 1: Check All Containers Running**

```bash
docker compose ps
```

**Expected output:**
```
NAME                STATUS
aozora-app          Up
aozora-db           Up
aozora-redis        Up
aozora-queue        Up
aozora-scheduler    Up
```

**If database is NOT running or "Restarting":**

```bash
# Check database logs
docker compose logs db --tail=50

# Common issues in logs:
# - Authentication plugin error
# - Insufficient memory
# - Port conflict
```

---

### **Step 2: Restart Database Container**

```bash
# Restart database
docker compose restart db

# Wait for database to be ready
sleep 15

# Check status
docker compose ps db
```

---

### **Step 3: Verify Database is Ready**

```bash
# Check database logs for "ready for connections"
docker compose logs db | grep -i "ready for connections"

# Should see something like:
# [Server] /usr/sbin/mysqld: ready for connections
```

---

### **Step 4: Check Network Connectivity**

```bash
# Check if containers are on same network
docker network inspect aozoraproject_aozora-network

# Should show both app and db containers
```

---

### **Step 5: Test Connection from App Container**

```bash
# Ping database from app container
docker compose exec app ping -c 3 db

# Should get replies
```

---

### **Step 6: Verify .env Database Settings**

```bash
# Check .env
cat .env | grep DB_

# Should show:
# DB_HOST=db          <- MUST be "db" not "localhost"
# DB_PORT=3306
# DB_DATABASE=aozora_production
# DB_USERNAME=aozora_prod_user
# DB_PASSWORD=your_password
```

---

## 🚀 Complete Fix

If above checks fail, do a **complete restart**:

```bash
# 1. Stop all containers
docker compose down

# 2. Remove any orphaned containers
docker compose down --remove-orphans

# 3. Start fresh
docker compose up -d

# 4. Wait for database to be ready
echo "Waiting for database..."
sleep 30

# 5. Check all containers
docker compose ps

# 6. Verify database is ready
docker compose logs db | grep -i "ready for connections"

# 7. Try migration again
docker compose exec app php artisan migrate --force
```

---

## 🔍 Detailed Diagnostics

### **Check Database Container Health:**

```bash
# Check container status
docker compose ps db

# Check container logs
docker compose logs db --tail=100

# Check if database is responding
docker compose exec db mysql -u root -p$DB_ROOT_PASSWORD -e "SELECT 1;"
```

---

### **Check App Container can reach DB:**

```bash
# Try to connect from app container
docker compose exec app php artisan tinker

# Inside tinker, run:
DB::connection()->getPdo();

# Should return PDO object, not error
exit
```

---

### **Check Network:**

```bash
# List networks
docker network ls

# Inspect app network
docker network inspect aozoraproject_aozora-network

# Should show both containers in "Containers" section
```

---

## 🐛 Common Issues & Fixes

### **Issue 1: Database Container Not Starting**

**Symptoms:**
- `docker compose ps db` shows "Restarting"
- Database logs show errors

**Fix:**

```bash
# Check logs for specific error
docker compose logs db --tail=100

# Common fixes:
# - If authentication plugin error:
docker compose down
docker volume rm aozoraproject_db-data
docker compose up -d

# - If port conflict:
# Edit .env, change DB_PORT to different port
# Then restart
```

---

### **Issue 2: Wrong DB_HOST in .env**

**Symptoms:**
- DB_HOST is set to "localhost" or "127.0.0.1"

**Fix:**

```bash
# Edit .env
nano .env

# Change to:
DB_HOST=db

# Restart app
docker compose restart app

# Clear config cache
docker compose exec app php artisan config:clear
```

---

### **Issue 3: Database Not Ready Yet**

**Symptoms:**
- Database container shows "Up" but migrations fail
- Database logs don't show "ready for connections"

**Fix:**

```bash
# Wait longer for database
sleep 30

# Check database is ready
docker compose logs db | grep -i "ready"

# Try again
docker compose exec app php artisan migrate --force
```

---

### **Issue 4: Network Issue**

**Symptoms:**
- `ping db` fails from app container
- Containers not on same network

**Fix:**

```bash
# Recreate network
docker compose down
docker network prune
docker compose up -d

# Verify network
docker network inspect aozoraproject_aozora-network
```

---

## ✅ One-Liner Complete Fix

```bash
docker compose down && sleep 5 && docker compose up -d && sleep 30 && docker compose ps && docker compose logs db | grep -i "ready" && docker compose exec app php artisan migrate --force
```

---

## 📋 Verification Checklist

- [ ] Database container status = "Up" (not "Restarting")
- [ ] Database logs show "ready for connections"
- [ ] `ping db` works from app container
- [ ] `.env` has `DB_HOST=db`
- [ ] All containers on same network
- [ ] Can connect to database from app
- [ ] Migration successful

---

## 🆘 Still Not Working?

### **Get detailed info:**

```bash
# Container status
docker compose ps

# Database logs
docker compose logs db --tail=100

# App logs
docker compose logs app --tail=100

# Network info
docker network inspect aozoraproject_aozora-network

# Environment check
docker compose exec app env | grep DB_
```

**Then share the output for further diagnosis.**

---

**Quick Help:** [`QUICK_FIX.md`](QUICK_FIX.md) | [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)

