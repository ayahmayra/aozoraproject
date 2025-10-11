# 🔧 Setup Main Branch - Local Development

**Branch:** `main`  
**Environment:** Local development with Laravel Herd  
**Database:** MySQL (via Herd or local MySQL)  
**Cache/Session:** File-based (no Redis)

---

## 🚀 Quick Setup

### **Step 1: Create MySQL Database**

**Option A: Using Herd's MySQL (Recommended)**

```bash
# Check if MySQL is running
mysql --version

# Create database (no password if using Herd default)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS aozora_education;"
mysql -u root -e "SHOW DATABASES LIKE 'aozora%';"
```

**Option B: Using Local MySQL (if different)**

```bash
# If you have password
mysql -u root -p

# Then in MySQL:
CREATE DATABASE IF NOT EXISTS aozora_education;
SHOW DATABASES;
exit;
```

---

### **Step 2: Configure .env**

```bash
# Update cache/session to file-based (no Redis)
cat > .env << 'EOF'
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://aozoraproject.test

# Database - Local MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aozora_education
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting & Cache - File-based (no Redis)
BROADCAST_CONNECTION=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# No Redis needed for main branch
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@aozora.test"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
EOF
```

---

### **Step 3: Generate APP_KEY**

```bash
# Generate application key
php artisan key:generate

# Verify
grep "APP_KEY" .env
```

---

### **Step 4: Clear All Caches**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

### **Step 5: Run Migrations**

```bash
# Check connection first
php artisan db:show

# Run migrations
php artisan migrate --seed

# Verify tables
php artisan db:table users
```

---

### **Step 6: Test Application**

```bash
# Start queue worker (in background)
php artisan queue:work &

# Visit in browser
open https://aozoraproject.test

# Or test with curl
curl -I https://aozoraproject.test
```

---

## 🔍 Troubleshooting

### **Error: Access denied for user 'root'**

**Solution 1: Check MySQL password**

```bash
# Test connection
mysql -u root -e "SELECT 1;"

# If fails, try with password
mysql -u root -p -e "SELECT 1;"
```

**Solution 2: Update .env with password**

```bash
# If MySQL requires password
echo "DB_PASSWORD=your_mysql_password" >> .env
```

**Solution 3: Use Herd's MySQL socket**

```bash
# Herd uses socket connection
# Update .env
DB_HOST=/Users/hermansyah/Library/Application Support/Herd/config/mysql/mysql.sock
DB_PORT=

# Or just
DB_CONNECTION=mysql
DB_SOCKET=/Users/hermansyah/Library/Application Support/Herd/config/mysql/mysql.sock
```

---

### **Error: Redis connection failed**

**Already Fixed!** Main branch now uses file-based cache/session:

```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

No Redis needed! ✅

---

### **Error: Organization not found**

```bash
# Run seeder
php artisan db:seed --class=OrganizationSeeder

# Or full seed
php artisan db:seed
```

---

## 📋 Complete .env for Main Branch

```env
APP_NAME="Aozora Education"
APP_ENV=local
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://aozoraproject.test

VITE_APP_NAME="${APP_NAME}"

# Database - Local MySQL (Herd)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aozora_education
DB_USERNAME=root
DB_PASSWORD=

# Cache & Session - File-based (NO REDIS!)
BROADCAST_CONNECTION=log
CACHE_DRIVER=file
CACHE_PREFIX=
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Mail
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@aozora.test"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔀 Switching Between Branches

### **From `dockerversion` to `main`**

```bash
# 1. Checkout main
git checkout main

# 2. Update .env
cp .env .env.docker.backup
sed -i '' 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env
sed -i '' 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env
sed -i '' 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=database/' .env
sed -i '' 's/DB_HOST=db/DB_HOST=127.0.0.1/' .env
sed -i '' 's/DB_DATABASE=aozora_local/DB_DATABASE=aozora_education/' .env
sed -i '' 's/DB_USERNAME=aozora_user/DB_USERNAME=root/' .env
sed -i '' 's/DB_PASSWORD=aozora_password123/DB_PASSWORD=/' .env

# 3. Clear caches
php artisan config:clear
php artisan cache:clear

# 4. Test
php artisan db:show
open https://aozoraproject.test
```

### **From `main` to `dockerversion`**

```bash
# 1. Checkout dockerversion
git checkout dockerversion

# 2. Restore Docker .env
cp .env.docker.backup .env

# Or update .env for Docker
sed -i '' 's/CACHE_DRIVER=file/CACHE_DRIVER=redis/' .env
sed -i '' 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' .env
sed -i '' 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=redis/' .env
sed -i '' 's/DB_HOST=127.0.0.1/DB_HOST=db/' .env

# 3. Start Docker
docker compose up -d

# 4. Test
docker compose exec app php artisan db:show
curl -I http://localhost:8080
```

---

## 🎯 Key Differences: Main vs Dockerversion

| Feature | Main Branch | Dockerversion Branch |
|---------|-------------|---------------------|
| **Environment** | Local (Herd) | Docker containers |
| **Database** | MySQL (local) | MySQL (container `db`) |
| **Cache** | File | Redis (container) |
| **Session** | File | Redis (container) |
| **Queue** | Database | Redis (container) |
| **Port** | 80/443 (Herd) | 8080 (Docker) |
| **Setup** | Standard Laravel | FrankenPHP + Worker Mode |

---

## ✅ Verification Checklist

After setup, verify these:

- [ ] MySQL connection works: `php artisan db:show`
- [ ] Migrations ran: `php artisan migrate:status`
- [ ] Organization exists: `php artisan tinker` → `Organization::count()`
- [ ] Cache works: `php artisan cache:clear`
- [ ] Site loads: `open https://aozoraproject.test`
- [ ] Login works
- [ ] No Redis errors in logs

---

## 🚀 One-Liner Setup (After Database Created)

```bash
cd /Users/hermansyah/Herd/aozoraproject && \
git checkout main && \
sed -i '' 's/CACHE_DRIVER=redis/CACHE_DRIVER=file/' .env && \
sed -i '' 's/SESSION_DRIVER=redis/SESSION_DRIVER=file/' .env && \
sed -i '' 's/QUEUE_CONNECTION=redis/QUEUE_CONNECTION=database/' .env && \
sed -i '' 's/DB_HOST=db/DB_HOST=127.0.0.1/' .env && \
sed -i '' 's/DB_DATABASE=aozora_local/DB_DATABASE=aozora_education/' .env && \
sed -i '' 's/DB_USERNAME=aozora_user/DB_USERNAME=root/' .env && \
sed -i '' 's/DB_PASSWORD=.*/DB_PASSWORD=/' .env && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan db:show && \
echo "✅ Main branch ready!"
```

---

**🎉 Main branch configured for local development!**

