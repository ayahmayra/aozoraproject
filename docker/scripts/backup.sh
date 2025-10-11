#!/bin/bash
set -e

# Configuration
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)
APP_NAME="aozora"

echo "========================================"
echo "Backup Aozora Education System"
echo "========================================"

# Create backup directory
mkdir -p ${BACKUP_DIR}

# Backup database
echo "Backing up database..."
docker compose exec -T db mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} | gzip > ${BACKUP_DIR}/${APP_NAME}_db_${DATE}.sql.gz
echo "Database backup completed: ${APP_NAME}_db_${DATE}.sql.gz"

# Backup storage files
echo "Backing up storage files..."
tar -czf ${BACKUP_DIR}/${APP_NAME}_storage_${DATE}.tar.gz -C ./storage .
echo "Storage backup completed: ${APP_NAME}_storage_${DATE}.tar.gz"

# Remove old backups (keep last 7 days)
echo "Cleaning old backups..."
find ${BACKUP_DIR} -name "${APP_NAME}_*.gz" -mtime +7 -delete

echo "========================================"
echo "Backup completed successfully!"
echo "========================================"
echo ""
ls -lh ${BACKUP_DIR}/${APP_NAME}_*${DATE}*

