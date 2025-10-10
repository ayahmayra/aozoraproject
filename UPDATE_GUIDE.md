# Update Guide - Production

Panduan lengkap untuk update aplikasi di production via git pull.

## 🔄 Update Workflow

### Standard Update Process

```bash
# 1. Masuk ke direktori aplikasi
cd /path/to/aozoraproject

# 2. Backup database (PENTING!)
docker compose exec db mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} \
  | gzip > backup_before_update_$(date +%Y%m%d_%H%M%S).sql.gz

# 3. Pull latest code
git pull origin main

# 4. Rebuild container (jika ada perubahan Dockerfile)
docker compose build

# 5. Restart services
docker compose up -d

# 6. Run migrations
docker compose exec app php artisan migrate --force

# 7. Clear & rebuild cache
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize

# 8. Restart queue workers
docker compose restart queue

# 9. Verify
curl https://yourdomain.com/up
```

---

## 🎯 Update Scenarios

### Scenario 1: Update Kode Saja (No Dependencies)

**Contoh**: Update view blade, controller logic, routes

```bash
# Pull code
git pull origin main

# Clear cache
docker compose exec app php artisan view:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan config:clear

# Rebuild cache
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Done! No restart needed
```

### Scenario 2: Update dengan Dependencies

**Contoh**: composer.json berubah, package.json berubah

```bash
# Pull code
git pull origin main

# Rebuild container (akan install dependencies baru)
docker compose build --no-cache app

# Restart
docker compose up -d

# Clear cache
docker compose exec app php artisan optimize

# Restart queue
docker compose restart queue
```

### Scenario 3: Update Database Schema

**Contoh**: Ada migration baru

```bash
# WAJIB: Backup database dulu!
./docker/scripts/backup.sh

# Pull code
git pull origin main

# Rebuild (jika perlu)
docker compose build app

# Restart
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force

# Clear cache
docker compose exec app php artisan optimize

# Restart queue
docker compose restart queue
```

### Scenario 4: Update Assets (CSS/JS)

**Contoh**: resources/css atau resources/js berubah

```bash
# Pull code
git pull origin main

# Rebuild (akan compile assets baru via npm run build)
docker compose build --no-cache app

# Restart
docker compose up -d

# Done!
```

### Scenario 5: Update Environment Variables

**Contoh**: .env needs changes

```bash
# Edit .env
nano .env

# Restart untuk apply changes
docker compose down
docker compose up -d

# Clear config cache
docker compose exec app php artisan config:cache
```

---

## 🚀 Zero-Downtime Deployment

### Using Docker Compose (Recommended)

```bash
# 1. Backup
./docker/scripts/backup.sh

# 2. Pull code
git pull origin main

# 3. Build new image with different tag
docker compose build

# 4. Start new containers (old ones still running)
docker compose up -d --no-deps --build app

# Docker akan:
# - Start container baru
# - Health check passed
# - Stop container lama
# - Zero downtime! ✅
```

### Using Blue-Green Deployment

```bash
# Terminal 1 - Start new version (green)
docker compose -f docker-compose.yml -p aozora-green up -d

# Test new version
curl http://localhost:8080/up  # Assume green uses port 8080

# If OK, switch traffic (update nginx/load balancer)
# Then stop old version (blue)
docker compose -f docker-compose.yml -p aozora-blue down
```

---

## 📋 Pre-Update Checklist

- [ ] Backup database
- [ ] Backup uploaded files (storage/)
- [ ] Review changes: `git log origin/main..HEAD`
- [ ] Check migration files: `git diff origin/main..HEAD --name-only database/migrations/`
- [ ] Maintenance mode (jika update besar): `docker compose exec app php artisan down`
- [ ] Notify users (jika diperlukan)

---

## 🛡️ Rollback Strategy

### Quick Rollback (Git)

```bash
# Lihat commit history
git log --oneline -10

# Rollback ke commit sebelumnya
git reset --hard COMMIT_HASH

# Rebuild & restart
docker compose build
docker compose up -d

# Restore database (jika perlu)
gunzip < backup_file.sql.gz | docker compose exec -T db mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}
```

### Rollback dengan Git Revert (Safer)

```bash
# Revert commit terakhir (creates new commit)
git revert HEAD

# Push
git push origin main

# Update production
git pull origin main
docker compose build
docker compose up -d
```

---

## 🔍 Troubleshooting Updates

### Git Pull Conflicts

```bash
# Stash local changes
git stash

# Pull
git pull origin main

# Apply stashed changes (jika perlu)
git stash pop

# Resolve conflicts
nano conflicted_file.php

# Commit resolution
git add .
git commit -m "Resolved merge conflicts"
```

### Container Won't Start After Update

```bash
# Check logs
docker compose logs app

# Check specific service
docker compose logs db
docker compose logs redis

# Force recreate
docker compose up -d --force-recreate

# Rebuild from scratch
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Migration Failed

```bash
# Check migration status
docker compose exec app php artisan migrate:status

# Rollback last migration
docker compose exec app php artisan migrate:rollback --step=1

# Try again
docker compose exec app php artisan migrate --force

# If still fails, restore from backup
```

### Assets Not Updating

```bash
# Clear browser cache first!

# Then clear Laravel cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear

# Rebuild with no cache
docker compose build --no-cache app
docker compose up -d

# Force browser refresh: Ctrl+Shift+R (Chrome/Firefox)
```

---

## 🔧 Maintenance Mode

### Enable Maintenance Mode

```bash
# Show maintenance page
docker compose exec app php artisan down

# Custom message
docker compose exec app php artisan down --message="Updating system, back in 5 minutes"

# Allow specific IPs
docker compose exec app php artisan down --allow=123.456.789.10
```

### Update During Maintenance

```bash
# 1. Enable maintenance
docker compose exec app php artisan down

# 2. Update
git pull origin main
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize

# 3. Disable maintenance
docker compose exec app php artisan up
```

---

## 📊 Monitoring After Update

### Check Application Health

```bash
# Health endpoint
curl https://yourdomain.com/up

# Check logs
docker compose logs -f app --tail=100

# Check errors
docker compose logs app | grep -i error

# Check database connection
docker compose exec app php artisan db:show
```

### Monitor Resource Usage

```bash
# Real-time stats
docker stats

# Specific container
docker stats aozora-app

# Disk usage
df -h
du -sh /var/lib/docker/volumes/
```

### Verify Functionality

```bash
# Test login
curl -X POST https://yourdomain.com/login \
  -d "email=test@test.com&password=password"

# Test database
docker compose exec app php artisan tinker
>>> User::count()
>>> DB::connection()->getPdo()

# Test queue
docker compose exec app php artisan queue:work --once

# Test scheduled tasks
docker compose exec app php artisan schedule:run
```

---

## 🤖 Automated Update Script

Script sudah tersedia di `docker/scripts/deploy.sh`:

```bash
# Usage
./docker/scripts/deploy.sh

# Or with auto-yes
./docker/scripts/deploy.sh --yes
```

---

## 📈 Best Practices

### 1. Always Backup Before Update
```bash
# Automated backup
./docker/scripts/backup.sh
```

### 2. Test in Staging First
```bash
# Setup staging environment
cp .env .env.staging
# Update staging first, test, then production
```

### 3. Use Git Tags for Releases
```bash
# Create release tag
git tag -a v1.0.1 -m "Release version 1.0.1"
git push origin v1.0.1

# Deploy specific version
git checkout v1.0.1
docker compose build
docker compose up -d
```

### 4. Keep Update Log
```bash
# Log updates
echo "$(date): Updated to $(git rev-parse HEAD)" >> /var/log/aozora-updates.log
```

### 5. Monitor for 30 Minutes After Update
```bash
# Watch logs
docker compose logs -f app

# Watch errors
watch -n 5 'docker compose logs app --tail=50 | grep -i error'
```

---

## 🔄 Automated CI/CD (Advanced)

### GitHub Actions Example

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /path/to/aozoraproject
            git pull origin main
            docker compose build
            docker compose up -d
            docker compose exec -T app php artisan migrate --force
            docker compose exec -T app php artisan optimize
```

---

## 📞 Emergency Contacts

Update failed dan butuh bantuan cepat?

1. Check logs: `docker compose logs app`
2. Rollback: `git reset --hard PREV_COMMIT`
3. Restore backup: `./docker/scripts/restore.sh`
4. Contact: support@aozora.edu

---

## ✅ Post-Update Checklist

- [ ] Application accessible
- [ ] Login working
- [ ] Database queries working
- [ ] Queue processing
- [ ] Scheduled tasks running
- [ ] Email sending (if configured)
- [ ] File uploads working
- [ ] No errors in logs
- [ ] Performance acceptable
- [ ] SSL certificate valid
- [ ] Backup completed successfully

---

**Happy Updating! 🚀**

