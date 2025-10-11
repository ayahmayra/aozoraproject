# 🔀 Merge Strategy Analysis: dockerversion → main

**Goal:** Menggabungkan dockerversion ke main branch sehingga system bisa berjalan **BAIK dengan Docker MAUPUN tanpa Docker**

---

## ✅ **Kesimpulan Analisa**

### **BISA!** 🎉

Setelah merge, system **AKAN BISA** berjalan dengan:
- ✅ **Docker** (production deployment)
- ✅ **Tanpa Docker** (local development dengan Herd)

**Syarat:**
1. Environment detection yang proper
2. Template .env yang berbeda untuk setiap mode
3. Setup script untuk switch environment
4. Dokumentasi yang jelas

---

## 📊 **Perbedaan Kunci: Main vs Dockerversion**

### **Files Yang Hanya di Dockerversion:**

| File/Folder | Purpose | Impact ke Local Dev |
|-------------|---------|---------------------|
| `Dockerfile` | Docker image build | ❌ Tidak mengganggu local |
| `docker-compose.yml` | Container orchestration | ❌ Tidak mengganggu local |
| `docker/` | Docker configs | ❌ Tidak mengganggu local |
| `Caddyfile*` | FrankenPHP routing | ❌ Tidak mengganggu local |
| `public/frankenphp-worker.php` | Worker mode | ❌ Tidak mengganggu local |
| Setup scripts | Automation | ❌ Tidak mengganggu local |
| Documentation | Guides | ✅ Helpful untuk semua |

### **Configuration Differences:**

| Config | Main Branch | Dockerversion | Solution |
|--------|-------------|---------------|----------|
| **DB_HOST** | `127.0.0.1` | `db` (container) | Environment-specific .env |
| **CACHE_DRIVER** | `file` | `redis` | Environment-specific .env |
| **SESSION_DRIVER** | `file` | `redis` | Environment-specific .env |
| **QUEUE_CONNECTION** | `database` | `redis` | Environment-specific .env |
| **APP_URL** | `https://aozoraproject.test` | `http://localhost:8080` | Environment-specific .env |

---

## 🎯 **Strategy: Multi-Environment Support**

### **Approach:**

1. **Keep Docker files** (tidak mengganggu local development)
2. **Create .env templates** untuk setiap environment
3. **Setup script** untuk switch environment otomatis
4. **Update .gitignore** untuk protect .env
5. **Comprehensive documentation**

---

## 📝 **Implementation Plan**

### **Phase 1: Pre-Merge Preparation**

#### **1.1 Create Environment Templates**

**.env.local** (untuk Herd/local development):
```env
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://aozoraproject.test

# Local MySQL (Herd)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aozoraproject
DB_USERNAME=root
DB_PASSWORD=

# File-based (no Redis)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=log
```

**.env.docker** (untuk Docker deployment):
```env
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8080

# Docker MySQL
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_local
DB_USERNAME=aozora_user
DB_PASSWORD=aozora_password123
DB_ROOT_PASSWORD=root_password123

# Redis (Docker)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PASSWORD=redis_password123
REDIS_PORT=6379

# FrankenPHP Worker Mode
FRANKENPHP_NUM_THREADS=4
FRANKENPHP_NUM_WORKERS=2

MAIL_MAILER=log
```

**.env.production** (untuk production server):
```env
APP_NAME="Aozora Education"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://aozora.trust-idn.id

# Docker MySQL (Production)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=aozora_production
DB_USERNAME=aozora_user
DB_PASSWORD=STRONG_PASSWORD_HERE
DB_ROOT_PASSWORD=STRONG_ROOT_PASSWORD_HERE

# Redis (Production)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PASSWORD=STRONG_REDIS_PASSWORD_HERE
REDIS_PORT=6379

# FrankenPHP Worker Mode
FRANKENPHP_NUM_THREADS=8
FRANKENPHP_NUM_WORKERS=4

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

---

#### **1.2 Create Switch Script**

**switch-env.sh:**
```bash
#!/bin/bash

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo "🔄 Environment Switcher"
echo ""
echo "Select environment:"
echo "  1) Local Development (Herd)"
echo "  2) Docker (localhost)"
echo "  3) Production (Docker + NPM)"
echo ""
read -p "Enter choice [1-3]: " choice

case $choice in
    1)
        echo -e "${YELLOW}Switching to Local Development...${NC}"
        
        if [ ! -f .env.local ]; then
            echo -e "${RED}Error: .env.local not found!${NC}"
            exit 1
        fi
        
        # Backup current .env
        if [ -f .env ]; then
            cp .env .env.backup
        fi
        
        # Copy local template
        cp .env.local .env
        
        # Stop Docker if running
        if [ -f docker-compose.yml ]; then
            docker compose down 2>/dev/null
        fi
        
        # Clear Laravel cache
        php artisan config:clear
        php artisan cache:clear
        
        echo -e "${GREEN}✅ Switched to Local Development${NC}"
        echo ""
        echo "Configuration:"
        echo "  - Database: MySQL (127.0.0.1:3306)"
        echo "  - Cache: File"
        echo "  - Session: File"
        echo "  - Queue: Database"
        echo ""
        echo "Access: https://aozoraproject.test"
        ;;
        
    2)
        echo -e "${YELLOW}Switching to Docker (localhost)...${NC}"
        
        if [ ! -f .env.docker ]; then
            echo -e "${RED}Error: .env.docker not found!${NC}"
            exit 1
        fi
        
        # Backup current .env
        if [ -f .env ]; then
            cp .env .env.backup
        fi
        
        # Copy Docker template
        cp .env.docker .env
        
        # Generate APP_KEY if not set
        if ! grep -q "APP_KEY=base64:" .env; then
            php artisan key:generate
        fi
        
        # Start Docker
        docker compose up -d
        
        # Wait for containers
        echo "Waiting for containers..."
        sleep 10
        
        # Run migrations
        docker compose exec app php artisan migrate --force
        
        echo -e "${GREEN}✅ Switched to Docker (localhost)${NC}"
        echo ""
        echo "Configuration:"
        echo "  - Database: MySQL (Docker container 'db')"
        echo "  - Cache: Redis (Docker container 'redis')"
        echo "  - Session: Redis"
        echo "  - Queue: Redis"
        echo ""
        echo "Access: http://localhost:8080"
        ;;
        
    3)
        echo -e "${YELLOW}Switching to Production...${NC}"
        
        if [ ! -f .env.production ]; then
            echo -e "${RED}Error: .env.production not found!${NC}"
            exit 1
        fi
        
        # Backup current .env
        if [ -f .env ]; then
            cp .env .env.backup
        fi
        
        # Copy production template
        cp .env.production .env
        
        echo -e "${YELLOW}⚠️  Please update these in .env:${NC}"
        echo "  - APP_KEY (run: php artisan key:generate)"
        echo "  - DB_PASSWORD"
        echo "  - DB_ROOT_PASSWORD"
        echo "  - REDIS_PASSWORD"
        echo "  - MAIL_* settings"
        echo ""
        
        read -p "Continue with Docker deployment? [y/N]: " confirm
        
        if [[ $confirm =~ ^[Yy]$ ]]; then
            # Start Docker
            docker compose down
            docker compose build --no-cache
            docker compose up -d
            
            # Wait for containers
            echo "Waiting for containers..."
            sleep 15
            
            # Run migrations
            docker compose exec app php artisan migrate --force
            docker compose exec app php artisan db:seed --class=OrganizationSeeder --force
            
            echo -e "${GREEN}✅ Production environment started${NC}"
        else
            echo "Deployment cancelled. .env updated but containers not started."
        fi
        ;;
        
    *)
        echo -e "${RED}Invalid choice!${NC}"
        exit 1
        ;;
esac

echo ""
echo "Backup saved to: .env.backup"
```

---

### **Phase 2: Merge Execution**

#### **2.1 Backup Current Main Branch**

```bash
# Create backup branch
git checkout main
git branch main-backup
git push origin main-backup

# Backup .env
cp .env .env.main.backup
```

#### **2.2 Perform Merge**

```bash
# Merge dockerversion into main
git checkout main
git merge dockerversion

# Resolve conflicts if any
# Most conflicts will be in:
# - .env.example (keep both configs commented)
# - README.md (merge both content)
# - .gitignore (merge both)
```

#### **2.3 Post-Merge Setup**

```bash
# Create environment templates
touch .env.local .env.docker .env.production

# Update .gitignore
echo "
# Environment templates (keep in repo)
# .env.local
# .env.docker
# .env.production

# Active environment (DO NOT commit)
.env
.env.backup
.env.*.backup
" >> .gitignore

# Make switch script executable
chmod +x switch-env.sh

# Update .env.example to be generic
cat > .env.example << 'EOF'
# Copy this file to .env and configure for your environment
# Or use one of the templates:
#   - .env.local (for local development with Herd)
#   - .env.docker (for Docker localhost)
#   - .env.production (for production deployment)

APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost

# Database - Configure based on environment
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aozoraproject
DB_USERNAME=root
DB_PASSWORD=

# Cache/Session - Use 'file' for local, 'redis' for Docker
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Redis (only needed for Docker)
# REDIS_HOST=redis
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF
```

---

### **Phase 3: Testing**

#### **3.1 Test Local Development (No Docker)**

```bash
# Switch to local
./switch-env.sh
# Select: 1 (Local Development)

# Verify .env
grep "DB_HOST" .env
# Should show: DB_HOST=127.0.0.1

grep "CACHE_DRIVER" .env
# Should show: CACHE_DRIVER=file

# Test database connection
php artisan db:show

# Test application
open https://aozoraproject.test

# Test features:
# - Login
# - Dashboard
# - Organization settings
# - Upload logo/favicon
# - View uploaded images

# Check no Docker containers running
docker compose ps
# Should show: no containers
```

#### **3.2 Test Docker Development (Localhost)**

```bash
# Switch to Docker
./switch-env.sh
# Select: 2 (Docker localhost)

# Verify .env
grep "DB_HOST" .env
# Should show: DB_HOST=db

grep "CACHE_DRIVER" .env
# Should show: CACHE_DRIVER=redis

# Verify Docker containers
docker compose ps
# Should show: app, db, redis, queue, scheduler (all running)

# Test database connection
docker compose exec app php artisan db:show

# Test application
curl -I http://localhost:8080
# Should return: HTTP/1.1 200 OK

# Test features:
# - Login via http://localhost:8080
# - Dashboard
# - Organization settings
# - Upload logo/favicon
# - View uploaded images
# - Check Redis cache: docker compose exec redis redis-cli KEYS "*"

# Test worker mode
docker compose logs app | grep -i "worker"
# Should show FrankenPHP worker mode active
```

#### **3.3 Test Production Deployment (Docker + NPM)**

```bash
# On production server

# Switch to production
./switch-env.sh
# Select: 3 (Production)

# Update sensitive values in .env
nano .env
# Update: APP_KEY, DB_PASSWORD, REDIS_PASSWORD, etc.

# Verify .env
grep "APP_ENV" .env
# Should show: APP_ENV=production

grep "APP_DEBUG" .env
# Should show: APP_DEBUG=false

# Start Docker
docker compose down
docker compose build --no-cache
docker compose up -d

# Verify containers
docker compose ps

# Test internal access
curl -I http://localhost:8080

# Test external access via NPM
curl -I https://aozora.trust-idn.id

# Test features:
# - Login
# - Dashboard
# - Upload files
# - Performance (worker mode)
```

---

## 🔍 **Potential Issues & Solutions**

### **Issue 1: Conflicts During Merge**

**Files likely to have conflicts:**
- `.env.example`
- `README.md`
- `.gitignore`

**Solution:**
```bash
# For .env.example - create new generic version
# For README.md - merge both content manually
# For .gitignore - merge both, keep all exclusions

# After resolving
git add .
git commit -m "Merge dockerversion into main"
```

---

### **Issue 2: Docker Files in Local Dev**

**Non-issue:** Docker files (Dockerfile, docker-compose.yml) **tidak mengganggu** local development sama sekali. They are only used when you run `docker compose` commands.

---

### **Issue 3: Caddyfile Not Needed Locally**

**Non-issue:** Caddyfile hanya digunakan oleh FrankenPHP di dalam Docker container. Local development dengan Herd menggunakan Nginx, tidak terpengaruh Caddyfile.

---

### **Issue 4: Missing Redis in Local Dev**

**Already handled:** Local .env uses `CACHE_DRIVER=file`, so no Redis needed.

If accidentally set to redis:
```bash
# Fix it
sed -i '' 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env
sed -i '' 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env
php artisan config:clear
```

---

### **Issue 5: Wrong Database Host**

**Symptom:**
- Local dev trying to connect to `db` (Docker container)
- Docker trying to connect to `127.0.0.1` (local)

**Solution:**
```bash
# Use switch-env.sh to set correct environment
./switch-env.sh

# Or manually check
grep "DB_HOST" .env
# Local should be: 127.0.0.1
# Docker should be: db
```

---

## 📋 **Complete Testing Checklist**

### **After Merge:**

#### **Local Development (Herd):**
- [ ] `.env` configured for local (DB_HOST=127.0.0.1, CACHE_DRIVER=file)
- [ ] `php artisan db:show` works
- [ ] Site loads: `https://aozoraproject.test`
- [ ] Login works
- [ ] Dashboard loads
- [ ] Organization settings accessible
- [ ] Can upload logo/favicon
- [ ] Uploaded images display
- [ ] No Redis connection errors
- [ ] No Docker containers running

#### **Docker Development (Localhost):**
- [ ] `.env` configured for Docker (DB_HOST=db, CACHE_DRIVER=redis)
- [ ] `docker compose up -d` successful
- [ ] All containers healthy: `docker compose ps`
- [ ] `docker compose exec app php artisan db:show` works
- [ ] Site loads: `http://localhost:8080`
- [ ] Login works
- [ ] Dashboard loads
- [ ] Organization settings accessible
- [ ] Can upload logo/favicon
- [ ] Uploaded images display
- [ ] Redis working: `docker compose exec redis redis-cli KEYS "*"`
- [ ] Worker mode active: `docker compose logs app | grep worker`

#### **Switching Between Modes:**
- [ ] `./switch-env.sh` (option 1) switches to local successfully
- [ ] `./switch-env.sh` (option 2) switches to Docker successfully
- [ ] `.env.backup` created when switching
- [ ] No data loss when switching
- [ ] Can switch back and forth multiple times

---

## 🎯 **Expected Outcome After Merge**

### **Repository Structure:**

```
aozoraproject/
├── .env.example          # Generic template
├── .env.local            # Local dev template
├── .env.docker           # Docker localhost template
├── .env.production       # Production template
├── .env                  # Active config (gitignored)
├── switch-env.sh         # Environment switcher
├── Dockerfile            # Docker image
├── docker-compose.yml    # Docker orchestration
├── Caddyfile*            # FrankenPHP configs
├── docker/               # Docker scripts & configs
├── app/                  # Laravel application
├── resources/
├── public/
│   └── frankenphp-worker.php
└── [docs]                # All documentation
```

### **Usage Scenarios:**

**Scenario 1: Developer Clone Repository**
```bash
git clone https://github.com/ayahmayra/aozoraproject.git
cd aozoraproject

# For local development
./switch-env.sh  # Select: 1 (Local)
php artisan migrate
open https://aozoraproject.test

# For Docker testing
./switch-env.sh  # Select: 2 (Docker)
# Docker automatically starts
open http://localhost:8080
```

**Scenario 2: Production Deployment**
```bash
git clone https://github.com/ayahmayra/aozoraproject.git
cd aozoraproject

./switch-env.sh  # Select: 3 (Production)
# Edit .env for production settings
nano .env

# Deploy
docker compose up -d
```

**Scenario 3: Existing Developer After Merge**
```bash
git pull origin main

# If you were using local dev
./switch-env.sh  # Select: 1 (Local)

# Continue working as before
# Nothing breaks!
```

---

## 📚 **Documentation Updates Needed**

After merge, update:

1. **README.md** - Add section on multi-environment support
2. **SETUP_MAIN_BRANCH.md** - Update for merged structure
3. Create **ENVIRONMENT_GUIDE.md** - Comprehensive environment guide
4. Update all Docker-specific docs to mention they're optional

---

## ✅ **Recommendation: PROCEED WITH MERGE**

### **Why it's safe:**

1. ✅ Docker files **don't interfere** with local development
2. ✅ Environment-specific `.env` templates solve config conflicts
3. ✅ Switch script makes it easy to toggle environments
4. ✅ Comprehensive testing plan covers all scenarios
5. ✅ Rollback available via `main-backup` branch
6. ✅ Benefits both local dev and production deployment

### **Timeline:**

- **Preparation:** 30 minutes (create templates, script)
- **Merge:** 15 minutes (merge + conflict resolution)
- **Testing:** 1-2 hours (test all scenarios)
- **Documentation:** 30 minutes (update docs)
- **Total:** ~3 hours

### **Risk Level:** 🟢 **LOW**

With proper preparation and testing, this merge is very safe.

---

## 🚀 **Next Steps**

**Ready to proceed?**

1. Review this analysis
2. Create backup: `git branch main-backup`
3. Create environment templates
4. Create switch script
5. Perform merge
6. Test all scenarios
7. Update documentation
8. Push to GitHub

**Command to start:**
```bash
# I can help you execute this step-by-step!
echo "Ready to merge? Let's do this! 🚀"
```

---

**📌 Summary:** Merge dockerversion → main is **SAFE and RECOMMENDED**. System akan berjalan baik dengan Docker maupun tanpa Docker dengan proper environment configuration.

