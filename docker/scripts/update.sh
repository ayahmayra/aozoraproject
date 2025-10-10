#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "🔄 Aozora Education System Updater"
echo "========================================"
echo ""

# Check if we're in maintenance mode
MAINTENANCE_MODE=${1:-"no"}

# Function to print colored output
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo "ℹ️  $1"
}

# Check if git repo
if [ ! -d .git ]; then
    print_error "Not a git repository!"
    exit 1
fi

# Check current branch
CURRENT_BRANCH=$(git branch --show-current)
print_info "Current branch: $CURRENT_BRANCH"

# Check for uncommitted changes
if ! git diff-index --quiet HEAD --; then
    print_warning "You have uncommitted changes!"
    read -p "Do you want to stash them? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git stash
        print_success "Changes stashed"
    else
        print_error "Please commit or stash your changes first"
        exit 1
    fi
fi

# Backup database
print_info "Creating database backup..."
BACKUP_FILE="backup_before_update_$(date +%Y%m%d_%H%M%S).sql.gz"
if docker compose exec -T db mysqldump -u${DB_USERNAME:-aozora_user} -p${DB_PASSWORD} ${DB_DATABASE:-aozora_production} | gzip > "backups/${BACKUP_FILE}"; then
    print_success "Database backup created: backups/${BACKUP_FILE}"
else
    print_error "Backup failed! Aborting update."
    exit 1
fi

# Fetch latest changes
print_info "Fetching latest changes..."
git fetch origin

# Check if updates available
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/$CURRENT_BRANCH)

if [ $LOCAL = $REMOTE ]; then
    print_info "Already up to date!"
    echo ""
    echo "Current commit: $(git log -1 --oneline)"
    exit 0
fi

# Show changes
echo ""
print_info "New commits available:"
git log HEAD..origin/$CURRENT_BRANCH --oneline
echo ""

# Check for migration files
MIGRATION_COUNT=$(git diff HEAD..origin/$CURRENT_BRANCH --name-only | grep -c "database/migrations/" || true)
if [ $MIGRATION_COUNT -gt 0 ]; then
    print_warning "Found $MIGRATION_COUNT new migration file(s)"
fi

# Confirm update
read -p "Do you want to proceed with the update? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_info "Update cancelled"
    exit 0
fi

# Enable maintenance mode
if [ "$MAINTENANCE_MODE" = "yes" ]; then
    print_info "Enabling maintenance mode..."
    docker compose exec -T app php artisan down --render="errors::503" || true
fi

# Pull latest code
print_info "Pulling latest code..."
if git pull origin $CURRENT_BRANCH; then
    print_success "Code updated"
else
    print_error "Git pull failed!"
    exit 1
fi

# Check if Dockerfile changed
if git diff HEAD@{1} HEAD --name-only | grep -q "Dockerfile\|composer.json\|package.json"; then
    print_warning "Dependencies changed, rebuilding containers..."
    
    # Build new images
    print_info "Building images..."
    if docker compose build; then
        print_success "Build completed"
    else
        print_error "Build failed! Rolling back..."
        git reset --hard HEAD@{1}
        exit 1
    fi
fi

# Restart containers
print_info "Restarting containers..."
if docker compose up -d; then
    print_success "Containers restarted"
else
    print_error "Failed to restart containers!"
    exit 1
fi

# Wait for containers to be healthy
print_info "Waiting for services to be ready..."
sleep 10

# Check container health
if ! docker compose ps | grep -q "healthy"; then
    print_warning "Containers may not be healthy yet, waiting..."
    sleep 10
fi

# Run migrations
if [ $MIGRATION_COUNT -gt 0 ]; then
    print_info "Running database migrations..."
    if docker compose exec -T app php artisan migrate --force; then
        print_success "Migrations completed"
    else
        print_error "Migration failed!"
        print_warning "Rolling back..."
        git reset --hard HEAD@{1}
        docker compose build
        docker compose up -d
        exit 1
    fi
fi

# Clear and rebuild cache
print_info "Clearing cache..."
docker compose exec -T app php artisan optimize:clear

print_info "Rebuilding cache..."
docker compose exec -T app php artisan optimize

# Restart queue workers
print_info "Restarting queue workers..."
docker compose restart queue

# Disable maintenance mode
if [ "$MAINTENANCE_MODE" = "yes" ]; then
    print_info "Disabling maintenance mode..."
    docker compose exec -T app php artisan up
fi

# Verify application
print_info "Verifying application..."
if curl -f http://localhost/up > /dev/null 2>&1; then
    print_success "Application is healthy"
else
    print_warning "Application may not be responding correctly"
fi

# Show current version
CURRENT_COMMIT=$(git log -1 --oneline)
NEW_COMMIT_COUNT=$(git rev-list HEAD@{1}..HEAD --count)

echo ""
echo "========================================"
print_success "Update completed successfully!"
echo "========================================"
echo ""
echo "📊 Update Summary:"
echo "  - Previous commit: $(git log -1 --oneline HEAD@{1})"
echo "  - Current commit: $CURRENT_COMMIT"
echo "  - Commits applied: $NEW_COMMIT_COUNT"
echo "  - Backup: backups/${BACKUP_FILE}"
echo ""
echo "📋 Post-Update Checklist:"
echo "  [ ] Test login functionality"
echo "  [ ] Verify database queries"
echo "  [ ] Check queue processing"
echo "  [ ] Monitor logs for errors"
echo "  [ ] Test critical features"
echo ""
echo "📝 Useful Commands:"
echo "  - View logs: docker compose logs -f app"
echo "  - Rollback: git reset --hard HEAD@{1} && docker compose build && docker compose up -d"
echo "  - Restore DB: gunzip < backups/${BACKUP_FILE} | docker compose exec -T db mysql -u\${DB_USERNAME} -p\${DB_PASSWORD} \${DB_DATABASE}"
echo ""
print_success "Happy deploying! 🚀"

