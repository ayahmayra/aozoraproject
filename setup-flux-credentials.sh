#!/bin/bash

# Flux Pro Credentials Setup Script

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }

echo "========================================"
echo "🔐 Flux Pro Credentials Setup"
echo "========================================"
echo ""

# Check if auth.json exists
if [ -f "auth.json" ]; then
    print_warning "auth.json already exists!"
    echo ""
    cat auth.json
    echo ""
    read -p "Do you want to update it? (y/n) " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "Keeping existing auth.json"
        exit 0
    fi
fi

echo ""
print_info "To get your Flux Pro credentials:"
echo "  1. Go to https://fluxui.dev"
echo "  2. Login to your account"
echo "  3. Navigate to Account → Licenses"
echo "  4. Copy your license key"
echo ""

# Get credentials
read -p "Enter your Flux Pro email: " FLUX_EMAIL
read -p "Enter your Flux Pro license key: " FLUX_KEY

# Validate input
if [ -z "$FLUX_EMAIL" ] || [ -z "$FLUX_KEY" ]; then
    print_warning "Email or license key is empty!"
    exit 1
fi

# Create auth.json with permission handling
AUTH_JSON_CONTENT=$(cat << EOF
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "$FLUX_EMAIL",
            "password": "$FLUX_KEY"
        }
    }
}
EOF
)

# Try to write auth.json
if ! echo "$AUTH_JSON_CONTENT" > auth.json 2>/dev/null; then
    print_warning "Permission denied, trying with sudo..."
    
    # Remove existing file if owned by root
    if [ -f "auth.json" ]; then
        sudo rm -f auth.json
    fi
    
    # Write with sudo
    echo "$AUTH_JSON_CONTENT" | sudo tee auth.json > /dev/null
    
    # Fix ownership
    sudo chown $(whoami):$(id -gn) auth.json
    sudo chmod 644 auth.json
    
    print_success "auth.json created successfully (with sudo)!"
else
    chmod 644 auth.json
    print_success "auth.json created successfully!"
fi
echo ""
cat auth.json
echo ""

# Test credentials (optional)
print_info "Testing credentials..."

if command -v composer &> /dev/null; then
    # Create temp directory for test
    TEMP_DIR=$(mktemp -d)
    cd $TEMP_DIR
    
    # Copy auth.json to composer home
    mkdir -p ~/.composer
    cp $OLDPWD/auth.json ~/.composer/auth.json
    
    # Try to get package info
    if composer show livewire/flux-pro -a 2>/dev/null | grep -q "name"; then
        print_success "Credentials are valid! ✨"
    else
        print_warning "Could not verify credentials (might still be valid)"
    fi
    
    cd $OLDPWD
    rm -rf $TEMP_DIR
else
    print_info "Composer not found locally, will verify during Docker build"
fi

echo ""
print_success "Setup complete!"
echo ""
print_info "Next steps:"
echo "  1. Run: docker compose build"
echo "  2. Or: ./setup-localhost.sh"
echo ""

