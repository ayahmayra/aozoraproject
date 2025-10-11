# Flux Pro Authentication Setup

Panduan lengkap untuk setup Flux Pro credentials di Docker environment.

## 🔐 Apa itu Flux Pro?

**Flux Pro** adalah UI component library premium untuk Livewire yang digunakan dalam sistem ini.

**License:** Paid (requires authentication)
**Website:** https://fluxui.dev

---

## 📋 Current Status

Your credentials **sudah ada** di `auth.json`:

```json
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "hermansyah_@live.com",
            "password": "84c037a3-bfe7-45dd-9093-f540badf8462"
        }
    }
}
```

✅ **Credentials ini valid dan akan otomatis digunakan saat Docker build!**

---

## 🚀 Cara Kerja Authentication

### 1. **Local Development (Without Docker)**

```bash
# Composer akan otomatis baca auth.json dari project root
composer install

# Atau dari ~/.composer/auth.json
cp auth.json ~/.composer/auth.json
composer install
```

### 2. **Docker Build (Production)**

```dockerfile
# Dockerfile otomatis handle ini:

# Step 1: Copy auth.json ke container
COPY auth.json /app/auth.json

# Step 2: Setup Composer authentication
RUN mkdir -p /root/.composer && \
    cp /app/auth.json /root/.composer/auth.json

# Step 3: Composer install dengan authentication
RUN composer install
```

**Tidak perlu input manual!** Credentials dari `auth.json` otomatis digunakan. ✅

---

## 🔄 Update Credentials

### Jika License Expired atau Perlu Update:

#### **Method 1: Manual Edit**

```bash
# Edit auth.json
nano auth.json

# Update dengan credentials baru:
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "your-email@example.com",
            "password": "your-new-license-key-here"
        }
    }
}

# Save (Ctrl+X, Y, Enter)
```

#### **Method 2: Interactive Script**

```bash
# Run setup script
./setup-flux-credentials.sh

# Script akan tanya:
# - Email Flux Pro
# - License key
# - Auto-generate auth.json
```

#### **Method 3: Get from Flux Dashboard**

1. Login ke **https://fluxui.dev**
2. Go to **Account** → **Licenses**
3. Find your license
4. Copy **License Key**
5. Update `auth.json`:

```json
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "YOUR_EMAIL_HERE",
            "password": "YOUR_LICENSE_KEY_HERE"
        }
    }
}
```

---

## ✅ Verify Credentials

### Test Locally (Before Docker Build)

```bash
# Method 1: Use setup script
./setup-flux-credentials.sh

# Method 2: Manual test
composer config --global http-basic.composer.fluxui.dev YOUR_EMAIL YOUR_LICENSE_KEY
composer show livewire/flux-pro -a

# Should show package info without errors
```

### Test in Docker Build

```bash
# Build with verbose output
docker compose build 2>&1 | tee build.log

# Search for Flux Pro download
grep -i "flux-pro" build.log

# Should see:
# ✅ - Downloading livewire/flux-pro (version)
# ✅ - Installing livewire/flux-pro
```

---

## 🐛 Troubleshooting

### Error: "Could not find package livewire/flux-pro"

**Cause:** Credentials tidak valid atau tidak terbaca

**Solution:**

```bash
# 1. Verify auth.json exists
ls -la auth.json

# 2. Check content
cat auth.json

# 3. Verify format (must be valid JSON)
cat auth.json | python3 -m json.tool

# 4. Re-setup credentials
./setup-flux-credentials.sh

# 5. Rebuild
docker compose build --no-cache
```

### Error: "Invalid credentials for composer.fluxui.dev"

**Cause:** License expired atau credentials salah

**Solution:**

```bash
# 1. Check license status
# Login to https://fluxui.dev → Account → Licenses

# 2. Get new license key

# 3. Update auth.json
./setup-flux-credentials.sh

# 4. Test
composer show livewire/flux-pro -a

# 5. Rebuild
docker compose build --no-cache
```

### Error: "The 'https://composer.fluxui.dev/...' file could not be downloaded"

**Cause:** Network issue atau authentication problem

**Solution:**

```bash
# 1. Check internet connection
ping composer.fluxui.dev

# 2. Verify auth.json
cat auth.json

# 3. Try manual composer command
docker run --rm -v $(pwd):/app -w /app composer:latest composer diagnose

# 4. Check if credentials work
docker run --rm -v $(pwd):/app -w /app \
  -e COMPOSER_AUTH="$(cat auth.json | tr -d '\n')" \
  composer:latest composer show livewire/flux-pro
```

---

## 🔒 Security Notes

### For Development

```bash
# auth.json sudah di .gitignore (default Laravel)
# Tapi di sistem ini, kita include untuk Docker build

# Check .gitignore
cat .gitignore | grep auth.json
```

### For Production

**Options:**

#### **Option 1: Include auth.json (Current Setup)**

```dockerfile
# Dockerfile
COPY auth.json /app/auth.json
```

**Pros:**
- ✅ Simple
- ✅ Works everywhere

**Cons:**
- ⚠️ Credentials in image (not ideal for public images)

#### **Option 2: Use Build Secrets (Most Secure)**

```dockerfile
# Dockerfile
RUN --mount=type=secret,id=composer_auth,target=/root/.composer/auth.json \
    composer install
```

```bash
# Build with secret
docker compose build --secret id=composer_auth,src=auth.json
```

**Pros:**
- ✅ Credentials not in image
- ✅ More secure

**Cons:**
- ⚠️ Slightly more complex

#### **Option 3: Environment Variable**

```bash
# Set environment variable
export COMPOSER_AUTH=$(cat auth.json | tr -d '\n')

# Build
docker compose build
```

**For now, Option 1 (current) is fine for private deployments.**

---

## 📝 Quick Commands

### Setup New Credentials

```bash
./setup-flux-credentials.sh
```

### Verify Credentials

```bash
# Check auth.json exists
cat auth.json

# Test with Composer
composer show livewire/flux-pro
```

### Update Credentials

```bash
# Re-run setup
./setup-flux-credentials.sh

# Or manual edit
nano auth.json
```

### Build with Fresh Credentials

```bash
# After updating auth.json
docker compose build --no-cache
```

---

## 🎯 Common Scenarios

### Scenario 1: First Time Setup (No auth.json)

```bash
# Run setup script
./setup-flux-credentials.sh

# Enter your Flux Pro credentials when prompted
# Script will create auth.json

# Then build
docker compose build
```

### Scenario 2: Credentials Expired

```bash
# Get new license key from https://fluxui.dev

# Update credentials
./setup-flux-credentials.sh

# Rebuild
docker compose build --no-cache
```

### Scenario 3: Multiple Developers

**Share credentials securely:**

```bash
# Developer 1 (has license)
./setup-flux-credentials.sh
# Share auth.json securely (encrypted)

# Developer 2 (receive auth.json)
# Place auth.json in project root
docker compose build
```

### Scenario 4: CI/CD Pipeline

```bash
# GitHub Actions
# Store auth.json content as secret: COMPOSER_AUTH

# .github/workflows/deploy.yml
- name: Setup Composer auth
  run: echo '${{ secrets.COMPOSER_AUTH }}' > auth.json

- name: Build
  run: docker compose build
```

---

## 🎓 Best Practices

1. **Never commit auth.json to public repos**
   - ✅ Already in `.gitignore` (if public)
   - ℹ️ OK for private repos

2. **Keep backup of license key**
   - Save license key securely
   - Store in password manager

3. **Rotate credentials regularly**
   - Update every 6-12 months
   - Or when team members leave

4. **Use build secrets for production**
   - More secure than embedding in image
   - See Option 2 above

---

## 📞 Get Flux Pro License

### If You Don't Have License Yet

1. Visit **https://fluxui.dev**
2. Click **"Get Flux Pro"**
3. Purchase license
4. Check email for license key
5. Run `./setup-flux-credentials.sh`

### If You Have License

Your credentials are **already in auth.json** and working! ✅

---

## ✅ Summary

- **Current Status:** ✅ auth.json exists with valid credentials
- **Docker Build:** ✅ Will automatically use auth.json
- **Setup Script:** ✅ Check credentials before build
- **Update Tool:** ✅ `./setup-flux-credentials.sh` available

**You're all set!** Just build:

```bash
docker compose build
```

Credentials akan otomatis digunakan untuk download Flux Pro! 🎉

---

## 🚨 Still Having Issues?

Run build dengan verbose dan kirim output:

```bash
docker compose build --progress=plain 2>&1 | tee build.log

# Share build.log atau copy error section
```

**Happy building! 🚀**

