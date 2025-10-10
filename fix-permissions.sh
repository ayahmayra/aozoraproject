#!/bin/bash

# Quick permission fix script for localhost deployment
# Run this if you encounter permission issues

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}🔧 Fixing Permissions${NC}"
echo -e "${YELLOW}========================================${NC}"
echo ""

# Get current user
CURRENT_USER=$(whoami)
CURRENT_GROUP=$(id -gn)

echo -e "${YELLOW}ℹ️  Current user: $CURRENT_USER:$CURRENT_GROUP${NC}"
echo ""

# Fix .env permissions
if [ -f .env ]; then
    echo -e "${YELLOW}ℹ️  Fixing .env permissions...${NC}"
    sudo chown $CURRENT_USER:$CURRENT_GROUP .env 2>/dev/null || true
    sudo chmod 644 .env 2>/dev/null || chmod 644 .env
    echo -e "${GREEN}✅ .env permissions fixed${NC}"
fi

# Fix storage permissions
if [ -d storage ]; then
    echo -e "${YELLOW}ℹ️  Fixing storage permissions...${NC}"
    sudo chown -R $CURRENT_USER:$CURRENT_GROUP storage 2>/dev/null || true
    sudo chmod -R 777 storage 2>/dev/null || chmod -R 777 storage
    echo -e "${GREEN}✅ storage permissions fixed${NC}"
fi

# Fix bootstrap/cache permissions
if [ -d bootstrap/cache ]; then
    echo -e "${YELLOW}ℹ️  Fixing bootstrap/cache permissions...${NC}"
    sudo chown -R $CURRENT_USER:$CURRENT_GROUP bootstrap/cache 2>/dev/null || true
    sudo chmod -R 777 bootstrap/cache 2>/dev/null || chmod -R 777 bootstrap/cache
    echo -e "${GREEN}✅ bootstrap/cache permissions fixed${NC}"
fi

# Remove public/storage if it exists
if [ -e public/storage ]; then
    echo -e "${YELLOW}ℹ️  Removing public/storage...${NC}"
    sudo rm -rf public/storage 2>/dev/null || rm -rf public/storage
    echo -e "${GREEN}✅ public/storage removed${NC}"
fi

# Fix public directory permissions
if [ -d public ]; then
    echo -e "${YELLOW}ℹ️  Fixing public directory permissions...${NC}"
    sudo chown -R $CURRENT_USER:$CURRENT_GROUP public 2>/dev/null || true
    sudo chmod -R 755 public 2>/dev/null || chmod -R 755 public
    echo -e "${GREEN}✅ public directory permissions fixed${NC}"
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✅ All Permissions Fixed!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${YELLOW}ℹ️  You can now run: ./setup-localhost.sh${NC}"
echo ""

