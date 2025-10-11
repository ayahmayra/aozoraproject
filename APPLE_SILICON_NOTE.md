# 🍎 Apple Silicon (ARM64) Compatibility Note

**For Mac with Apple Silicon (M1/M2/M3)**

---

## ⚠️ MySQL 5.7 Compatibility Issue

MySQL 5.7 has limited ARM64 support and may crash on Apple Silicon Macs when running via Docker.

### **Solution Options:**

#### **Option 1: Use MariaDB (Recommended for Apple Silicon)**

Update `docker-compose.yml`:
```yaml
  db:
    image: mariadb:10.6  # ARM64 native support
    container_name: aozora-db
    restart: unless-stopped
    ports:
      - "${DB_PORT:-3306}:3306"
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db-data:/var/lib/mysql
    networks:
      - aozora-network
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 10s
      timeout: 5s
      retries: 3
```

#### **Option 2: Use MySQL 8.0 (ARM64 supported)**

Update `docker-compose.yml`:
```yaml
  db:
    image: mysql:8.0
    # ... rest same as above
```

**Note:** Your production server may still use MySQL 5.7 without issues (Intel/AMD64).

#### **Option 3: Force AMD64 Platform**

Add platform specification (slower due to emulation):
```yaml
  db:
    platform: linux/amd64
    image: mysql:5.7
    # ... rest same
```

---

## 🎯 **Recommendation**

**For Local Development on Apple Silicon:**
- ✅ **Use Local Mode** (Herd with native MySQL) - Best performance
- ✅ **Use MariaDB 10.6** for Docker mode - ARM64 native
- ⚠️  **Use MySQL 8.0** - ARM64 supported but larger
- ❌ **Avoid MySQL 5.7** - Poor ARM64 support

**For Production (Intel/AMD servers):**
- ✅ **MySQL 5.7** works perfectly
- ✅ **MySQL 8.0** recommended for new deployments

---

## 🔄 **Quick Fix for Apple Silicon**

```bash
# Option 1: Use Local Mode (No Docker needed!)
./switch-env.sh
# Select: 1 (Local Development)

# Option 2: Update to MariaDB
nano docker-compose.yml
# Change: image: mysql:5.7
# To:     image: mariadb:10.6

# Then:
docker compose down
docker volume rm aozoraproject_db-data
docker compose up -d
```

---

## ✅ **Current Status**

After merge testing:
- ✅ **Local Mode:** Works perfectly on Apple Silicon
- ⚠️  **Docker Mode:** Requires MariaDB or MySQL 8.0
- ✅ **Production:** MySQL 5.7 works on Intel/AMD servers

---

## 📚 **Related Documentation**

- **Environment Switcher:** `./switch-env.sh`
- **Setup Guide:** `SETUP_MAIN_BRANCH.md`
- **Merge Strategy:** `MERGE_STRATEGY_ANALYSIS.md`

