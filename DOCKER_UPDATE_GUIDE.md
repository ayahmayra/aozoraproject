# 🔄 Docker Update Guide After Git Pull

**What to do after `git pull origin main`?**

---

## 🎯 **Quick Reference**

| Changed Files | Action Required | Command | Time |
|---------------|----------------|---------|------|
| **PHP code only** (app/, resources/, routes/) | ✅ **RESTART** | `docker compose restart app` | ~5 sec |
| **Blade views** (resources/views/) | ✅ **RESTART** or just clear cache | `docker compose exec app php artisan view:clear` | ~2 sec |
| **Config files** (config/) | ✅ **RESTART** | `docker compose restart app` | ~5 sec |
| **Migrations** (database/migrations/) | ⚠️ **RESTART + MIGRATE** | `docker compose restart app && docker compose exec app php artisan migrate --force` | ~10 sec |
| **.env file** | ⚠️ **DOWN/UP** (reload env) | `docker compose down && docker compose up -d` | ~30 sec |
| **Dockerfile** | ⚠️⚠️ **REBUILD** | `docker compose build --no-cache app && docker compose up -d` | ~3-5 min |
| **docker-compose.yml** | ⚠️⚠️ **DOWN/UP** | `docker compose down && docker compose up -d` | ~30 sec |
| **Composer packages** (composer.json) | ⚠️⚠️ **REBUILD** | `docker compose build --no-cache app` | ~3-5 min |
| **Node packages** (package.json) | ⚠️⚠️ **REBUILD** | `docker compose build --no-cache app` | ~3-5 min |
| **Caddyfile** | ✅ **RESTART** | `docker compose restart app` | ~5 sec |
| **Documentation only** (*.md) | ✅ **NOTHING!** | No action needed | 0 sec |

---

## 🚀 **Scenario-Based Actions**

### **Scenario 1: Normal Code Update (Most Common)**

**Changes:**
- PHP files updated
- Blade templates changed
- Routes modified
- Minor config changes

**Action:** **RESTART ONLY**

```bash
cd /var/www/aozoraproject
git pull origin main

# Quick restart (FrankenPHP worker reloads automatically)
docker compose restart app

# Optional: Clear caches
docker compose exec app php artisan optimize:clear

# Test
curl -I http://localhost:8080
```

**Time:** ⏱️ ~10 seconds

---

### **Scenario 2: Database Schema Change**

**Changes:**
- New migration files
- Model changes
- Seeder updates

**Action:** **RESTART + MIGRATE**

```bash
cd /var/www/aozoraproject
git pull origin main

# Restart
docker compose restart app

# Run new migrations
docker compose exec app php artisan migrate --force

# Optional: Seed new data
docker compose exec app php artisan db:seed --force

# Clear caches
docker compose exec app php artisan optimize:clear

# Test
curl -I http://localhost:8080
```

**Time:** ⏱️ ~30 seconds

---

### **Scenario 3: Environment Variable Change**

**Changes:**
- `.env` file updated
- New environment variables added
- Redis/Database credentials changed

**Action:** **DOWN/UP** (to reload environment)

```bash
cd /var/www/aozoraproject
git pull origin main

# Update .env if needed
nano .env

# Down and up (reloads environment)
docker compose down
docker compose up -d

# Wait for healthy
sleep 15

# Test
docker compose ps
curl -I http://localhost:8080
```

**Time:** ⏱️ ~1 minute

---

### **Scenario 4: Dockerfile Changed**

**Changes:**
- Dockerfile modified
- PHP version updated
- System packages added
- Build steps changed

**Action:** **REBUILD** (fresh image)

```bash
cd /var/www/aozoraproject
git pull origin main

# Stop containers
docker compose down

# Rebuild images (no cache for clean build)
docker compose build --no-cache app queue scheduler

# Start with new images
docker compose up -d

# Wait for ready
sleep 20

# Verify
docker compose ps
docker compose logs app --tail=50

# Test
curl -I http://localhost:8080
```

**Time:** ⏱️ ~5 minutes

---

### **Scenario 5: docker-compose.yml Changed**

**Changes:**
- New containers added
- Port mappings changed
- Volume configurations changed
- Network settings changed

**Action:** **DOWN/UP** (recreate containers)

```bash
cd /var/www/aozoraproject
git pull origin main

# Down (removes containers)
docker compose down

# Up (creates new containers with new config)
docker compose up -d

# Wait for ready
sleep 20

# Verify all containers
docker compose ps

# Test
curl -I http://localhost:8080
```

**Time:** ⏱️ ~1 minute

---

### **Scenario 6: Composer/NPM Dependencies Changed**

**Changes:**
- composer.json updated (new PHP packages)
- composer.lock changed
- package.json updated (new JS packages)
- package-lock.json changed

**Action:** **REBUILD** (dependencies installed during build)

```bash
cd /var/www/aozoraproject
git pull origin main

# Stop
docker compose down

# Rebuild app image (includes composer install)
docker compose build --no-cache app

# Start
docker compose up -d

# Wait
sleep 20

# Clear Laravel caches
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache

# Test
curl -I http://localhost:8080
```

**Time:** ⏱️ ~5 minutes

---

### **Scenario 7: Documentation/README Only**

**Changes:**
- Only *.md files changed
- No code changes
- No config changes

**Action:** **NOTHING!** 🎉

```bash
cd /var/www/aozoraproject
git pull origin main

# That's it! Nothing to restart ✅
# Documentation changes don't affect running containers

# Optional: Just verify still running
docker compose ps
```

**Time:** ⏱️ 0 seconds

---

## 🔍 **How to Check What Changed**

### **Before Git Pull:**

```bash
# See what will change
git fetch origin main
git diff HEAD origin/main --name-status

# Check specific file types
git diff HEAD origin/main --name-only | grep -E "\.(php|blade\.php)$"  # Code
git diff HEAD origin/main --name-only | grep "Dockerfile"              # Docker
git diff HEAD origin/main --name-only | grep "docker-compose"          # Compose
git diff HEAD origin/main --name-only | grep "\.env"                   # Env
git diff HEAD origin/main --name-only | grep "composer\."              # Dependencies
```

### **After Git Pull:**

```bash
# See what changed
git log --oneline -5
git diff HEAD~1 --name-status

# Count changes by type
echo "PHP files: $(git diff HEAD~1 --name-only | grep '\.php$' | wc -l)"
echo "Blade files: $(git diff HEAD~1 --name-only | grep '\.blade\.php$' | wc -l)"
echo "Docker files: $(git diff HEAD~1 --name-only | grep -E '(Dockerfile|docker-compose)' | wc -l)"
```

---

## 🎯 **Smart Update Script**

Save this as `update-production.sh`:

```bash
#!/bin/bash

echo "🔄 Smart Production Update Script"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check what will change
echo "📊 Checking changes..."
git fetch origin main

CHANGED_FILES=$(git diff HEAD origin/main --name-only)

# Analyze changes
HAS_CODE=$(echo "$CHANGED_FILES" | grep -E "\.(php|blade\.php)$" | wc -l)
HAS_DOCKERFILE=$(echo "$CHANGED_FILES" | grep "Dockerfile" | wc -l)
HAS_COMPOSE=$(echo "$CHANGED_FILES" | grep "docker-compose" | wc -l)
HAS_ENV=$(echo "$CHANGED_FILES" | grep "\.env" | wc -l)
HAS_COMPOSER=$(echo "$CHANGED_FILES" | grep "composer\." | wc -l)
HAS_MIGRATIONS=$(echo "$CHANGED_FILES" | grep "migrations" | wc -l)

echo ""
echo "Changes detected:"
echo "  - Code files: $HAS_CODE"
echo "  - Dockerfile: $HAS_DOCKERFILE"
echo "  - docker-compose: $HAS_COMPOSE"
echo "  - .env: $HAS_ENV"
echo "  - composer.*: $HAS_COMPOSER"
echo "  - migrations: $HAS_MIGRATIONS"
echo ""

# Determine action
if [ $HAS_DOCKERFILE -gt 0 ] || [ $HAS_COMPOSER -gt 0 ]; then
    ACTION="REBUILD"
elif [ $HAS_COMPOSE -gt 0 ] || [ $HAS_ENV -gt 0 ]; then
    ACTION="DOWN_UP"
elif [ $HAS_CODE -gt 0 ] || [ $HAS_MIGRATIONS -gt 0 ]; then
    ACTION="RESTART"
else
    ACTION="NOTHING"
fi

echo -e "${YELLOW}Recommended action: $ACTION${NC}"
echo ""

# Confirm
read -p "Continue with git pull? [y/N]: " confirm
if [[ ! $confirm =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 0
fi

# Pull
echo -e "${YELLOW}➜ Pulling changes...${NC}"
git pull origin main

# Execute action
case $ACTION in
    REBUILD)
        echo -e "${RED}⚠ REBUILD required (Dockerfile or dependencies changed)${NC}"
        read -p "Rebuild images? This will take ~5 minutes [y/N]: " rebuild
        if [[ $rebuild =~ ^[Yy]$ ]]; then
            docker compose down
            docker compose build --no-cache app queue scheduler
            docker compose up -d
            sleep 20
        fi
        ;;
    DOWN_UP)
        echo -e "${YELLOW}⚠ DOWN/UP required (.env or compose changed)${NC}"
        docker compose down
        docker compose up -d
        sleep 15
        ;;
    RESTART)
        echo -e "${GREEN}✓ RESTART only${NC}"
        docker compose restart app
        if [ $HAS_MIGRATIONS -gt 0 ]; then
            echo -e "${YELLOW}➜ Running migrations...${NC}"
            sleep 5
            docker compose exec app php artisan migrate --force
        fi
        docker compose exec app php artisan optimize:clear
        ;;
    NOTHING)
        echo -e "${GREEN}✓ No action needed (docs only)${NC}"
        ;;
esac

# Verify
echo ""
echo -e "${GREEN}✓ Update complete!${NC}"
echo ""
echo "Verification:"
docker compose ps
echo ""
curl -I http://localhost:8080

echo ""
echo "Check logs if needed:"
echo "  docker compose logs app --tail=50"
```

**Usage:**
```bash
chmod +x update-production.sh
./update-production.sh
```

---

## ⚡ **Quick Commands Reference**

### **Just Restart (Fastest)**
```bash
docker compose restart app
```

### **Restart + Clear Cache**
```bash
docker compose restart app
docker compose exec app php artisan optimize:clear
```

### **Down/Up (Reload Everything)**
```bash
docker compose down && docker compose up -d
```

### **Rebuild (New Image)**
```bash
docker compose down
docker compose build --no-cache app
docker compose up -d
```

### **Full Reset (Nuclear Option)**
```bash
docker compose down -v  # Remove volumes too!
docker compose build --no-cache
docker compose up -d
# Then re-migrate and seed
```

---

## 🎯 **Best Practices**

### **1. Check Before Update**
```bash
# Always check what will change
git fetch origin main
git diff HEAD origin/main --name-status | head -20
```

### **2. Backup Before Major Changes**
```bash
# Backup database
docker compose exec db mysqldump -u root -p$DB_ROOT_PASSWORD $DB_DATABASE > backup_$(date +%Y%m%d).sql

# Backup .env
cp .env .env.backup.$(date +%Y%m%d)
```

### **3. Test After Update**
```bash
# Internal test
curl -I http://localhost:8080

# External test (via NPM)
curl -I https://your-domain.com

# Check logs
docker compose logs app --tail=100 | grep -i error

# Check containers
docker compose ps
```

### **4. Monitor Performance**
```bash
# Check if worker mode is active
docker compose logs app | grep -i "worker"

# Check resource usage
docker stats --no-stream
```

---

## ⚠️ **Common Mistakes to Avoid**

### **❌ DON'T:**
```bash
# Don't just restart if Dockerfile changed
git pull
docker compose restart app  # ❌ Won't pick up Dockerfile changes!
```

### **✅ DO:**
```bash
# Rebuild when Dockerfile changed
git pull
docker compose build --no-cache app
docker compose up -d  # ✅ Uses new image
```

---

### **❌ DON'T:**
```bash
# Don't forget to clear cache after code changes
git pull
docker compose restart app
# ❌ Old cached routes/configs might cause issues
```

### **✅ DO:**
```bash
# Always clear cache after updates
git pull
docker compose restart app
docker compose exec app php artisan optimize:clear  # ✅
```

---

## 📊 **Decision Matrix**

```
┌─────────────────────┬──────────┬──────┬─────────┐
│ What Changed?       │ Restart  │ Down │ Rebuild │
│                     │          │  /Up │         │
├─────────────────────┼──────────┼──────┼─────────┤
│ PHP code            │    ✅    │      │         │
│ Blade views         │    ✅    │      │         │
│ Routes              │    ✅    │      │         │
│ Config files        │    ✅    │      │         │
│ Migrations          │    ✅    │      │         │
│ .env file           │          │  ✅  │         │
│ docker-compose.yml  │          │  ✅  │         │
│ Dockerfile          │          │      │    ✅   │
│ composer.json       │          │      │    ✅   │
│ package.json        │          │      │    ✅   │
│ Caddyfile           │    ✅    │      │         │
│ Documentation       │   NONE   │      │         │
└─────────────────────┴──────────┴──────┴─────────┘
```

---

## 🎊 **Summary**

**Most Common Scenario (95% of updates):**
```bash
git pull origin main
docker compose restart app
docker compose exec app php artisan optimize:clear
```
⏱️ **10 seconds**

**When Dependencies Change (4% of updates):**
```bash
git pull origin main
docker compose down
docker compose build --no-cache app
docker compose up -d
```
⏱️ **5 minutes**

**When Only Docs Change (1% of updates):**
```bash
git pull origin main
# Done! ✅
```
⏱️ **0 seconds**

---

## 📚 **Related Documentation**

- **Production Deployment:** `PRODUCTION_FROM_MAIN.md`
- **Docker Compose:** `docker-compose.yml`
- **Update Guide:** `UPDATE_GUIDE.md`
- **Troubleshooting:** `QUICK_FIX.md`

---

**🎯 Rule of Thumb:**

```
When in doubt:
  docker compose down
  docker compose up -d

If that doesn't work:
  docker compose down
  docker compose build --no-cache
  docker compose up -d

If THAT doesn't work:
  Check logs! 😄
```

