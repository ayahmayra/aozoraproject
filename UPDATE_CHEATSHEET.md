# Update Cheatsheet - Quick Reference

## 🚀 Quick Update Commands

### Standard Update (Recommended)
```bash
./docker/scripts/update.sh
```

### Update with Maintenance Mode
```bash
./docker/scripts/update.sh yes
```

### Manual Update (Step by Step)
```bash
# 1. Backup
./docker/scripts/backup.sh

# 2. Pull
git pull origin main

# 3. Rebuild
docker compose build

# 4. Restart
docker compose up -d

# 5. Migrate
docker compose exec app php artisan migrate --force

# 6. Optimize
docker compose exec app php artisan optimize

# 7. Restart queue
docker compose restart queue
```

---

## 🔙 Rollback Commands

### Rollback to Previous Commit
```bash
./docker/scripts/rollback.sh
```

### Rollback to Specific Commit
```bash
./docker/scripts/rollback.sh abc1234
```

### Manual Rollback
```bash
# View commits
git log --oneline -10

# Reset to commit
git reset --hard COMMIT_HASH

# Rebuild
docker compose build && docker compose up -d
```

---

## 🔍 Check Before Update

### Check for Updates
```bash
git fetch origin
git log HEAD..origin/main --oneline
```

### Check What Changed
```bash
# Files changed
git diff HEAD..origin/main --name-only

# Migrations
git diff HEAD..origin/main --name-only database/migrations/

# Dependencies
git diff HEAD..origin/main composer.json package.json
```

---

## 🛠️ Common Tasks

### Clear All Caches
```bash
docker compose exec app php artisan optimize:clear
```

### Rebuild Caches
```bash
docker compose exec app php artisan optimize
```

### Run Specific Migration
```bash
docker compose exec app php artisan migrate --path=database/migrations/2024_XX_XX_XXXXX_name.php
```

### Rollback Last Migration
```bash
docker compose exec app php artisan migrate:rollback --step=1
```

### Restart Queue Workers
```bash
docker compose restart queue
```

### View Logs
```bash
# All logs
docker compose logs -f

# App only
docker compose logs -f app

# Last 100 lines
docker compose logs app --tail=100

# Errors only
docker compose logs app | grep -i error
```

---

## 🔧 Troubleshooting

### Container Won't Start
```bash
# Check status
docker compose ps

# View logs
docker compose logs app

# Force recreate
docker compose up -d --force-recreate
```

### Database Issues
```bash
# Check connection
docker compose exec app php artisan db:show

# Check migrations
docker compose exec app php artisan migrate:status

# Restart database
docker compose restart db
```

### Cache Issues
```bash
# Clear everything
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

# Rebuild
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### Permission Issues
```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

---

## 📊 Monitoring

### Check Application Health
```bash
curl http://localhost/up
```

### Check Container Health
```bash
docker compose ps
```

### Resource Usage
```bash
docker stats
```

### Disk Usage
```bash
docker system df
```

---

## 🔐 Backup & Restore

### Create Backup
```bash
./docker/scripts/backup.sh
```

### Restore from Backup
```bash
# Database
gunzip < backups/backup_file.sql.gz | \
  docker compose exec -T db mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}

# Storage
tar -xzf backups/storage_backup.tar.gz -C ./storage
```

---

## 🚨 Emergency Commands

### Enable Maintenance Mode
```bash
docker compose exec app php artisan down
```

### Disable Maintenance Mode
```bash
docker compose exec app php artisan up
```

### Complete Reset (DANGER!)
```bash
# Stop everything
docker compose down -v

# Remove images
docker compose down --rmi all

# Fresh start
docker compose up -d --build
```

### Quick Rollback
```bash
git reset --hard HEAD~1
docker compose build
docker compose up -d
```

---

## 📝 Best Practices

- ✅ Always backup before update
- ✅ Test in staging first (if available)
- ✅ Update during low traffic hours
- ✅ Monitor logs after update
- ✅ Keep backups for at least 7 days
- ✅ Document changes in commit messages
- ✅ Use maintenance mode for major updates
- ✅ Test critical features after update

---

## 🔗 Related Documentation

- `UPDATE_GUIDE.md` - Complete update guide
- `DEPLOYMENT.md` - Initial deployment guide
- `DOMAIN_SETUP.md` - Domain configuration
- `QUICK_START_PRODUCTION.md` - Quick start guide

---

**Keep this cheatsheet handy for quick reference! 📌**

