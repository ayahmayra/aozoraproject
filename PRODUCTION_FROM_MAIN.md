# 🚀 Production Deployment from Main Branch

**After merge, production server can now deploy directly from `main` branch!**

---

## ✅ **Option 1: Fresh Production Deployment**

### **On Production Server:**

```bash
# 1. Clone repository (main branch by default)
cd /var/www
git clone https://github.com/ayahmayra/aozoraproject.git
cd aozoraproject

# 2. Switch to production environment
./switch-env.sh
# Select: 3 (Production)

# 3. Update .env with production values
nano .env

# Update these:
# - APP_KEY (generate with: php artisan key:generate)
# - APP_URL=https://your-domain.com
# - DB_PASSWORD=strong_password
# - DB_ROOT_PASSWORD=strong_root_password
# - REDIS_PASSWORD=strong_redis_password
# - MAIL_* settings

# 4. Generate APP_KEY (if not done)
docker run --rm -v $(pwd):/app dunglas/frankenphp:latest-php8.3 php /app/artisan key:generate --show
# Copy the generated key to .env

# 5. Start Docker containers
docker compose up -d

# 6. Wait for containers to be ready
sleep 20

# 7. Run migrations and seed
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=OrganizationSeeder --force

# 8. Optimize for production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# 9. Set up storage permissions
docker compose exec app php artisan storage:link
docker compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache

# 10. Test
curl -I http://localhost:8080
# Should return: HTTP/1.1 200 OK ✅

# 11. Configure Nginx Proxy Manager
# Point to: localhost:8080
# Enable SSL
# Done! ✅
```

---

## 🔄 **Option 2: Update Existing Production (dockerversion → main)**

### **On Production Server:**

```bash
cd /var/www/aozoraproject

# 1. Check current branch
git branch --show-current
# Shows: dockerversion

# 2. Backup current .env
cp .env .env.dockerversion.backup

# 3. Stop containers
docker compose down

# 4. Switch to main branch
git fetch origin
git checkout main
git pull origin main

# 5. Copy production .env settings
cp .env.dockerversion.backup .env

# OR use the template and update:
cp .env.production .env
nano .env
# Update all production values

# 6. Check docker-compose.yml (should exist)
ls -la docker-compose.yml
# ✅ Should be there

# 7. Rebuild containers (to get latest configs)
docker compose build --no-cache

# 8. Start containers
docker compose up -d

# 9. Wait for ready
sleep 20

# 10. Run migrations (if any new)
docker compose exec app php artisan migrate --force

# 11. Clear caches
docker compose exec app php artisan optimize:clear

# 12. Optimize
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# 13. Test
curl -I http://localhost:8080
# ✅ Should work

curl -I https://your-domain.com
# ✅ Should work via NPM

# 14. Check containers
docker compose ps
# All should be healthy ✅

# 15. Check logs
docker compose logs app --tail=50
# No errors ✅

# Done! Production now running from main branch ✅
```

---

## 🔍 **Verification Steps**

### **1. Check Git Branch:**
```bash
cd /var/www/aozoraproject
git branch --show-current
# Should show: main ✅
```

### **2. Check Docker Files Exist:**
```bash
ls -la Dockerfile docker-compose.yml
# Both should exist ✅
```

### **3. Check Containers:**
```bash
docker compose ps
```

Expected output:
```
NAME               STATUS              PORTS
aozora-app         Up (healthy)        0.0.0.0:8080->80/tcp
aozora-db          Up (healthy)        3306/tcp
aozora-redis       Up (healthy)        6379/tcp
aozora-queue       Up                  -
aozora-scheduler   Up                  -
```

### **4. Check Internal Access:**
```bash
curl -I http://localhost:8080
# HTTP/1.1 200 OK ✅
```

### **5. Check External Access (via NPM):**
```bash
curl -I https://your-domain.com
# HTTP/2 200 ✅
```

### **6. Check Logs (no errors):**
```bash
docker compose logs app --tail=100 | grep -i error
# Should be empty or only minor warnings ✅
```

### **7. Test Features:**
- [ ] Login works
- [ ] Dashboard loads
- [ ] Organization settings accessible
- [ ] Can upload logo/favicon
- [ ] Uploaded images display
- [ ] Worker mode active

---

## 📊 **Comparison: dockerversion vs main**

| Aspect | dockerversion Branch | main Branch (After Merge) |
|--------|---------------------|---------------------------|
| **Docker Files** | ✅ Has Docker | ✅ Has Docker |
| **Documentation** | ✅ 30+ docs | ✅ 30+ docs (same) |
| **Environment Templates** | ❌ No | ✅ Yes (.env.local, .env.docker, .env.production) |
| **Switch Script** | ❌ No | ✅ Yes (switch-env.sh) |
| **Local Dev Support** | ❌ Docker only | ✅ Docker OR Local |
| **FrankenPHP Worker** | ✅ Yes | ✅ Yes (same) |
| **Production Ready** | ✅ Yes | ✅ Yes (same) |
| **Recommended For** | Legacy | ✅ **NEW STANDARD** |

---

## 🎯 **Benefits of Using Main Branch**

### **1. Single Source of Truth**
- One branch for everything
- No confusion between branches
- Easier maintenance

### **2. Flexibility**
- Same code runs with Docker OR locally
- Developer chooses environment
- Easy switching

### **3. Better Collaboration**
- Team works on same branch
- Clear git history
- Easier to merge features

### **4. Simplified Workflow**
```bash
# Development
git clone → work on main → git push

# Production
git clone → use main → deploy

# No branch switching needed! ✅
```

---

## ⚠️ **Important Notes**

### **1. Environment Configuration**

**Main branch needs proper .env!**

For production, use `.env.production` template:
```bash
cp .env.production .env
nano .env  # Update all values
```

### **2. Dockerversion Branch**

**Status:** Still exists but deprecated

You CAN still use it:
```bash
git checkout dockerversion
git pull origin dockerversion
```

But **main branch is now preferred** for new deployments!

### **3. No Breaking Changes**

Everything that worked in `dockerversion` still works in `main`:
- Same Docker configs
- Same FrankenPHP setup
- Same performance
- Same everything!

Just with added flexibility! ✅

---

## 🔄 **Git Workflow Examples**

### **Scenario A: Development → Production**

```bash
# Local development
git checkout main
# Make changes
git commit -m "New feature"
git push origin main

# Production deployment
# On production server:
cd /var/www/aozoraproject
git pull origin main
docker compose down
docker compose build --no-cache app
docker compose up -d
# Done! ✅
```

### **Scenario B: Hotfix in Production**

```bash
# On production server
cd /var/www/aozoraproject
git pull origin main  # Get latest
# Apply hotfix
docker compose restart app
```

### **Scenario C: Rollback**

```bash
# On production server
cd /var/www/aozoraproject
git log --oneline -10  # Find good commit
git checkout <commit-hash>
docker compose down
docker compose up -d
# Rolled back! ✅
```

---

## 🎯 **Recommended: Migrate to Main Branch**

### **Why Migrate?**

1. ✅ **Simpler:** One branch, not two
2. ✅ **Flexible:** Works with/without Docker
3. ✅ **Standard:** Industry best practice
4. ✅ **Future-proof:** All new features go to main
5. ✅ **Documented:** Better documentation

### **When to Migrate?**

- ✅ **Now!** No breaking changes
- ✅ **Next deployment** - smooth transition
- ✅ **Any time** - safe to switch

### **Migration Time:**

⏱️ **5-10 minutes** for complete migration!

---

## 📚 **Related Documentation**

- **Environment Switching:** `switch-env.sh`
- **Production Setup:** `PRODUCTION_INSTALLATION.md`
- **Docker Compose:** `docker-compose.yml`
- **Merge Details:** `MERGE_COMPLETE_SUMMARY.md`
- **Troubleshooting:** `QUICK_FIX.md`

---

## ✅ **Quick Answer**

**Q: Can I git pull origin main on production server?**

**A: YES! ✅**

```bash
cd /var/www/aozoraproject
git checkout main
git pull origin main
docker compose up -d
```

**It just works!** 🚀

---

**Main branch is now the single source of truth for:**
- ✅ Local development (Herd)
- ✅ Docker development (localhost)
- ✅ Production deployment (Docker)

**No need for separate branches anymore!** 🎉

