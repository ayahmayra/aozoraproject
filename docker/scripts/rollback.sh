#!/bin/bash
set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_error() { echo -e "${RED}❌ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
print_info() { echo "ℹ️  $1"; }

echo "========================================"
echo "🔙 Aozora Education System Rollback"
echo "========================================"
echo ""

# Check if git repo
if [ ! -d .git ]; then
    print_error "Not a git repository!"
    exit 1
fi

# Show recent commits
print_info "Recent commits:"
git log --oneline -10
echo ""

# Get commit to rollback to
if [ -z "$1" ]; then
    print_warning "No commit specified. Rolling back to previous commit..."
    ROLLBACK_TO="HEAD~1"
else
    ROLLBACK_TO="$1"
fi

# Verify commit exists
if ! git rev-parse --verify $ROLLBACK_TO > /dev/null 2>&1; then
    print_error "Invalid commit: $ROLLBACK_TO"
    exit 1
fi

print_info "Will rollback to: $(git log -1 --oneline $ROLLBACK_TO)"
print_warning "This will reset your code to the specified commit"
echo ""

# Confirm rollback
read -p "Are you sure you want to rollback? (yes/no) " -r
echo
if [[ ! $REPLY =~ ^[Yy][Ee][Ss]$ ]]; then
    print_info "Rollback cancelled"
    exit 0
fi

# Backup current state
CURRENT_COMMIT=$(git rev-parse HEAD)
print_info "Saving current state: $CURRENT_COMMIT"

# Enable maintenance mode
print_info "Enabling maintenance mode..."
docker compose exec -T app php artisan down --render="errors::503" || true

# Backup database before rollback
print_info "Creating safety backup..."
BACKUP_FILE="backup_before_rollback_$(date +%Y%m%d_%H%M%S).sql.gz"
docker compose exec -T db mysqldump -u${DB_USERNAME:-aozora_user} -p${DB_PASSWORD} ${DB_DATABASE:-aozora_production} \
  | gzip > "backups/${BACKUP_FILE}"
print_success "Database backup: backups/${BACKUP_FILE}"

# Perform rollback
print_info "Rolling back code..."
git reset --hard $ROLLBACK_TO
print_success "Code rolled back to: $(git log -1 --oneline)"

# Rebuild containers
print_info "Rebuilding containers..."
docker compose build

# Restart services
print_info "Restarting services..."
docker compose up -d

# Wait for services
print_info "Waiting for services..."
sleep 10

# Rollback migrations if needed
print_warning "Check if migrations need to be rolled back"
read -p "Do you want to rollback database migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "How many steps to rollback? " STEPS
    docker compose exec -T app php artisan migrate:rollback --step=$STEPS
    print_success "Migrations rolled back $STEPS step(s)"
fi

# Clear cache
print_info "Clearing cache..."
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan optimize

# Restart queue
print_info "Restarting queue workers..."
docker compose restart queue

# Disable maintenance mode
print_info "Disabling maintenance mode..."
docker compose exec -T app php artisan up

# Verify
print_info "Verifying application..."
if curl -f http://localhost/up > /dev/null 2>&1; then
    print_success "Application is healthy"
else
    print_warning "Application may not be responding"
fi

echo ""
echo "========================================"
print_success "Rollback completed!"
echo "========================================"
echo ""
echo "📊 Rollback Summary:"
echo "  - Previous: $CURRENT_COMMIT"
echo "  - Current: $(git rev-parse HEAD)"
echo "  - Backup: backups/${BACKUP_FILE}"
echo ""
echo "⚠️  To restore the previous version:"
echo "  git reset --hard $CURRENT_COMMIT"
echo "  docker compose build && docker compose up -d"
echo ""

