# 🔧 Fix Git Merge Conflict - Production Caddyfile

**Error:** `Your local changes to the following files would be overwritten by merge: Caddyfile`

**Situation:** You have local Caddyfile changes in production that conflict with GitHub updates.

---

## ✅ **Safe Solution (Recommended)**

### **Option 1: Backup, Pull, Then Compare (SAFEST)**

```bash
# Step 1: Check what changed locally
git diff Caddyfile

# Step 2: Backup your current Caddyfile
cp Caddyfile Caddyfile.production.custom

# Step 3: Reset Caddyfile to match repository
git checkout -- Caddyfile

# Step 4: Pull latest changes
git pull origin dockerversion

# Step 5: Compare and merge manually if needed
diff Caddyfile.production.custom Caddyfile

# Step 6: If you need your custom changes, edit Caddyfile manually
# Then remove backup
rm Caddyfile.production.custom
```

---

### **Option 2: Stash, Pull, Then Reapply**

```bash
# Step 1: Stash your local changes
git stash push -m "Production Caddyfile customizations"

# Step 2: Pull latest changes
git pull origin dockerversion

# Step 3: Try to reapply your changes
git stash pop

# If conflicts occur, resolve manually:
# - Open Caddyfile
# - Look for conflict markers: <<<<<<<, =======, >>>>>>>
# - Keep the version you want
# - Remove conflict markers
# - Save file

# Step 4: Clean up
git stash drop  # Only if stash pop succeeded
```

---

### **Option 3: Commit Your Changes First**

```bash
# Step 1: Review your changes
git diff Caddyfile

# Step 2: Add and commit your local changes
git add Caddyfile
git commit -m "Production Caddyfile customizations"

# Step 3: Pull with rebase (cleaner history)
git pull --rebase origin dockerversion

# If conflicts occur:
git status  # See conflicting files
# Edit Caddyfile to resolve conflicts
git add Caddyfile
git rebase --continue

# Or if you want to abort:
git rebase --abort
```

---

## 🎯 **Quick Fix (Most Common Scenario)**

**If you only changed the domain/email in Caddyfile:**

```bash
# 1. Save your domain and email
DOMAIN=$(grep -A1 "# HTTP" Caddyfile | grep "http://" | awk '{print $1}' | sed 's/http:\/\///')
EMAIL=$(grep "email" Caddyfile | awk '{print $2}')
echo "Your domain: $DOMAIN"
echo "Your email: $EMAIL"

# 2. Backup
cp Caddyfile Caddyfile.backup

# 3. Pull latest
git checkout -- Caddyfile
git pull origin dockerversion

# 4. Apply your domain/email changes
# Edit Caddyfile and update:
# - email YOUR_EMAIL
# - http://YOUR_DOMAIN
# - https://YOUR_DOMAIN

# 5. Restart containers
docker compose down
docker compose up -d
```

---

## 📋 **Complete Step-by-Step Guide**

### **Step 1: Check Current Status**

```bash
cd /var/www/aozoraproject

# See what files are changed
git status

# See exact changes in Caddyfile
git diff Caddyfile
```

**Copy the output!** You'll need to know what you changed.

---

### **Step 2: Backup Your Custom Caddyfile**

```bash
# Create backup with timestamp
cp Caddyfile Caddyfile.backup.$(date +%Y%m%d_%H%M%S)

# Verify backup
ls -la Caddyfile.backup.*
```

---

### **Step 3: Reset and Pull**

```bash
# Reset Caddyfile to match repository
git checkout -- Caddyfile

# Pull latest changes
git pull origin dockerversion
```

---

### **Step 4: Compare and Merge**

```bash
# Compare your backup with the new version
diff Caddyfile.backup.* Caddyfile

# Or use side-by-side comparison
diff -y Caddyfile.backup.* Caddyfile | less
```

**If you see differences you need:**

```bash
# Open both files
nano Caddyfile.backup.*  # See your old changes
nano Caddyfile           # Edit new version

# Manually copy any custom settings you need
# Common customizations:
# - Domain name
# - Email for SSL
# - Port numbers
# - Custom paths
```

---

### **Step 5: Test Configuration**

```bash
# Test Caddyfile syntax
docker compose exec app caddy validate --config /etc/caddy/Caddyfile

# If valid, restart
docker compose down
docker compose up -d

# Check logs
docker compose logs app -f
```

---

### **Step 6: Clean Up**

```bash
# Once everything works, remove backup
rm Caddyfile.backup.*

# Or keep it for reference
mkdir -p backups
mv Caddyfile.backup.* backups/
```

---

## 🔍 **What Changed in Latest Caddyfile**

### **Recent Updates:**

1. **Removed `/storage/*` from blocked paths**
   - Now allows uploaded files to be accessed
   - Fix for 404 on logos, images, documents

2. **Added worker mode configuration**
   - FrankenPHP worker for better performance
   - `num_threads` and `num_workers` settings

3. **Improved security headers**
   - Better default security configuration

---

## 🚨 **Common Caddyfile Customizations in Production**

### **1. Domain Name**

**Your Custom:**
```caddyfile
http://aozora.trust-idn.id {
    redir https://aozora.trust-idn.id{uri} permanent
}

https://aozora.trust-idn.id {
    # ...
}
```

**Template in Repo:**
```caddyfile
http://yourdomain.com {
    redir https://yourdomain.com{uri} permanent
}

https://yourdomain.com {
    # ...
}
```

**Action:** Update domain in new Caddyfile

---

### **2. Email for Let's Encrypt**

**Your Custom:**
```caddyfile
{
    email nothing4ll@gmail.com
}
```

**Template in Repo:**
```caddyfile
{
    email admin@yourdomain.com
}
```

**Action:** Update email in new Caddyfile

---

### **3. Port Configuration (with NPM)**

**Your Custom:**
```caddyfile
{
    auto_https off
}

:80 {
    # Only listen on port 80
}
```

**Template in Repo:**
```caddyfile
:443 {
    # Listen on both 80 and 443
}
```

**Action:** Use `Caddyfile.npm` instead if using Nginx Proxy Manager

---

## 💡 **Production-Specific Caddyfile Management**

### **Best Practice: Use Environment-Specific Caddyfiles**

**Instead of modifying `Caddyfile` directly:**

```bash
# Use the appropriate template
# Option 1: With Nginx Proxy Manager
cp Caddyfile.npm Caddyfile

# Option 2: Direct SSL (no proxy)
cp Caddyfile.production Caddyfile

# Then customize
nano Caddyfile
# Update domain, email, etc.

# Add to .gitignore to avoid conflicts
echo "Caddyfile" >> .gitignore
```

**This way:**
- ✅ Your production Caddyfile won't conflict with repo updates
- ✅ You can still pull latest changes to templates
- ✅ Easier to manage environment-specific configs

---

## 🔄 **Automated Merge Script**

**Save this as `merge-caddyfile.sh`:**

```bash
#!/bin/bash

echo "🔧 Merging Caddyfile Updates..."

# Backup current
cp Caddyfile Caddyfile.backup.$(date +%Y%m%d_%H%M%S)

# Extract current domain and email
CURRENT_DOMAIN=$(grep -oP 'https?://\K[^, ]+' Caddyfile | head -1)
CURRENT_EMAIL=$(grep -oP 'email \K.*' Caddyfile | head -1)

echo "Current domain: $CURRENT_DOMAIN"
echo "Current email: $CURRENT_EMAIL"

# Reset and pull
git checkout -- Caddyfile
git pull origin dockerversion

# Update with current values (if using Caddyfile.production)
if [ -n "$CURRENT_DOMAIN" ] && [ "$CURRENT_DOMAIN" != "yourdomain.com" ]; then
    cp Caddyfile.production Caddyfile
    sed -i "s/yourdomain\.com/$CURRENT_DOMAIN/g" Caddyfile
    sed -i "s/admin@yourdomain\.com/$CURRENT_EMAIL/g" Caddyfile
    echo "✅ Updated Caddyfile with your domain and email"
fi

# Test
docker compose exec app caddy validate --config /etc/caddy/Caddyfile && \
    echo "✅ Caddyfile is valid" || \
    echo "❌ Caddyfile has errors, check syntax"

echo "
Next steps:
1. Review Caddyfile: nano Caddyfile
2. Restart: docker compose down && docker compose up -d
3. Test: curl -I http://localhost:8080
"
```

**Usage:**

```bash
chmod +x merge-caddyfile.sh
./merge-caddyfile.sh
```

---

## 🎯 **Which Caddyfile Should You Use?**

### **Scenario 1: Using Nginx Proxy Manager (NPM)**

**Your setup:**
- NPM handles SSL and external routing
- Docker container only listens internally
- Port 8080 mapped

**Use:**
```bash
cp Caddyfile.npm Caddyfile
# Edit domain/email if needed
docker compose down && docker compose build --no-cache app && docker compose up -d
```

**Key features:**
- `auto_https off` - NPM handles SSL
- Only listens on `:80` internally
- No redirect rules

---

### **Scenario 2: Direct SSL (No Proxy)**

**Your setup:**
- Docker container directly exposed to internet
- Caddy handles SSL with Let's Encrypt
- Ports 80 and 443 mapped

**Use:**
```bash
cp Caddyfile.production Caddyfile
# Update yourdomain.com to your actual domain
# Update email
docker compose down && docker compose up -d
```

**Key features:**
- Auto HTTPS with Let's Encrypt
- HTTP to HTTPS redirect
- Direct SSL termination

---

### **Scenario 3: Localhost/Development**

**Your setup:**
- Testing on local machine
- No SSL needed

**Use:**
```bash
cp Caddyfile.standard Caddyfile
docker compose down && docker compose up -d
```

**Key features:**
- Simple HTTP only
- No SSL configuration
- Listens on `:80` and `:443` (self-signed)

---

## 📚 **Documentation References**

- **Storage Fix:** [`STORAGE_FIX_NOTE.md`](STORAGE_FIX_NOTE.md) - Why `/storage/*` was removed
- **NPM Setup:** [`NPM_QUICK_SETUP.md`](NPM_QUICK_SETUP.md) - Using Nginx Proxy Manager
- **Production Guide:** [`PRODUCTION_INSTALLATION_SUMMARY.md`](PRODUCTION_INSTALLATION_SUMMARY.md)

---

## ✅ **After Merge Checklist**

- [ ] Caddyfile has correct domain
- [ ] Caddyfile has correct email
- [ ] Caddyfile does NOT block `/storage/*`
- [ ] Container rebuilt: `docker compose build --no-cache app`
- [ ] Containers restarted: `docker compose up -d`
- [ ] Site accessible via HTTP
- [ ] Site accessible via HTTPS (if applicable)
- [ ] Uploaded images visible
- [ ] No errors in logs: `docker compose logs app -f`

---

**🎯 Most Common Solution:**

```bash
# Quick one-liner for most cases
cp Caddyfile Caddyfile.backup && git checkout -- Caddyfile && git pull origin dockerversion && nano Caddyfile && docker compose down && docker compose up -d
```

Then manually update your domain/email in the Caddyfile.

---

**Need help? Check what you changed:**
```bash
git diff HEAD -- Caddyfile
```

