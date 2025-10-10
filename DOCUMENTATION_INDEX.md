# 📚 Documentation Index

Complete guide to all documentation files in this project.

---

## 🎯 Start Here

**New to the project?** Start with these:

1. 📖 **[README.md](README.md)** - Project overview & quick links
2. 🚀 **[LOCALHOST_QUICK_START.md](LOCALHOST_QUICK_START.md)** - Deploy in 3 steps

---

## 📂 Documentation Structure

```
📁 Aozora Education System
│
├── 🏠 Getting Started
│   ├── README.md ............................ Project overview & quick links
│   ├── LOCALHOST_QUICK_START.md ............. 3-step localhost deployment
│   └── QUICK_FIX.md ......................... One-liner solutions
│
├── 🐛 Troubleshooting
│   ├── LOCALHOST_TROUBLESHOOTING.md ......... Detailed problem solutions
│   ├── QUICK_FIX.md ......................... Quick reference card
│   └── fix-permissions.sh ................... Permission fix utility
│
├── 🚀 Production Deployment
│   ├── DEPLOYMENT.md ........................ Full deployment guide
│   ├── DOMAIN_SETUP.md ...................... Domain & DNS configuration
│   ├── QUICK_START_PRODUCTION.md ............ Quick production setup
│   └── README_DEPLOYMENT.md ................. Deployment overview
│
├── ⚡ Performance & Optimization
│   └── PERFORMANCE.md ....................... FrankenPHP Worker Mode guide
│
├── 🔄 Updates & Maintenance
│   ├── UPDATE_GUIDE.md ...................... Detailed update guide
│   ├── UPDATE_CHEATSHEET.md ................. Quick update commands
│   └── docker/scripts/update.sh ............. Automated update script
│
├── 🔐 Security & Configuration
│   ├── setup-flux-credentials.sh ............ Setup Flux Pro authentication
│   ├── FLUX_PRO_SETUP.md .................... Flux Pro setup guide
│   └── .env.example ......................... Environment configuration
│
└── 🛠️ Setup Scripts
    ├── setup-localhost.sh ................... Automated localhost setup
    ├── fix-permissions.sh ................... Fix permission issues
    ├── docker/scripts/deploy.sh ............. Production deployment
    ├── docker/scripts/backup.sh ............. Database backup
    └── docker/scripts/optimize.sh ........... Production optimization
```

---

## 📖 Documentation by Purpose

### **I want to...**

#### **🏁 Get Started**
- Deploy on localhost → [`LOCALHOST_QUICK_START.md`](LOCALHOST_QUICK_START.md)
- Deploy to production → [`QUICK_START_PRODUCTION.md`](QUICK_START_PRODUCTION.md)
- Understand the project → [`README.md`](README.md)

#### **🐛 Fix Issues**
- Quick one-liner fix → [`QUICK_FIX.md`](QUICK_FIX.md)
- Detailed troubleshooting → [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
- Permission problems → Run `./fix-permissions.sh`

#### **🚀 Deploy to Production**
- Complete deployment guide → [`DEPLOYMENT.md`](DEPLOYMENT.md)
- Setup domain & SSL → [`DOMAIN_SETUP.md`](DOMAIN_SETUP.md)
- Quick production start → [`QUICK_START_PRODUCTION.md`](QUICK_START_PRODUCTION.md)
- Deployment overview → [`README_DEPLOYMENT.md`](README_DEPLOYMENT.md)

#### **⚡ Optimize Performance**
- Enable Worker Mode → [`PERFORMANCE.md`](PERFORMANCE.md)
- Production optimization → [`docker/scripts/optimize.sh`](docker/scripts/optimize.sh)

#### **🔄 Update Application**
- Detailed update steps → [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)
- Quick commands → [`UPDATE_CHEATSHEET.md`](UPDATE_CHEATSHEET.md)
- Automated update → Run `./docker/scripts/update.sh`

#### **🔐 Setup Authentication**
- Flux Pro setup → [`FLUX_PRO_SETUP.md`](FLUX_PRO_SETUP.md)
- Run credential setup → `./setup-flux-credentials.sh`

#### **💾 Backup & Restore**
- Backup database → Run `./docker/scripts/backup.sh`
- Manual backup → See [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)

---

## 🔍 Quick Reference by Error Type

| Error | Document | Quick Fix |
|-------|----------|-----------|
| Missing APP_KEY | [`LOCALHOST_TROUBLESHOOTING.md#missing-app-key`](LOCALHOST_TROUBLESHOOTING.md#-error-missing-app-key) | [`QUICK_FIX.md#missing-app_key`](QUICK_FIX.md#-missing-app_key) |
| Permission Denied | [`LOCALHOST_TROUBLESHOOTING.md#permission-denied`](LOCALHOST_TROUBLESHOOTING.md#-error-permission-denied) | Run `./fix-permissions.sh` |
| Port Already in Use | [`LOCALHOST_TROUBLESHOOTING.md#port-already-in-use`](LOCALHOST_TROUBLESHOOTING.md#-error-port-already-in-use) | Change `APP_PORT` in `.env` |
| Container Restarting | [`LOCALHOST_TROUBLESHOOTING.md#container-keeps-restarting`](LOCALHOST_TROUBLESHOOTING.md#-error-container-keeps-restarting) | Check logs: `docker compose logs app` |
| Composer Failed | [`LOCALHOST_TROUBLESHOOTING.md#docker-build-failed`](LOCALHOST_TROUBLESHOOTING.md#-error-docker-build-failed-composer) | Run `./setup-flux-credentials.sh` |
| Storage Permission | [`LOCALHOST_TROUBLESHOOTING.md#storage-permission-denied`](LOCALHOST_TROUBLESHOOTING.md#-error-storage-permission-denied) | `chmod -R 777 storage bootstrap/cache` |

---

## 📊 Documentation Coverage

### **Localhost Development:** ✅ Complete
- Quick start guide
- Troubleshooting guide
- Quick fix reference
- Setup automation scripts

### **Production Deployment:** ✅ Complete
- Full deployment guide
- Domain setup guide
- Quick start guide
- Deployment overview
- Update procedures

### **Performance:** ✅ Complete
- Worker mode configuration
- Optimization guide
- Performance tuning

### **Maintenance:** ✅ Complete
- Update guide
- Backup procedures
- Rollback procedures
- Cheat sheets

---

## 🎓 Learning Path

### **For Developers:**
1. Start: [`README.md`](README.md)
2. Setup: [`LOCALHOST_QUICK_START.md`](LOCALHOST_QUICK_START.md)
3. Troubleshoot: [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
4. Optimize: [`PERFORMANCE.md`](PERFORMANCE.md)

### **For DevOps:**
1. Overview: [`README_DEPLOYMENT.md`](README_DEPLOYMENT.md)
2. Deploy: [`DEPLOYMENT.md`](DEPLOYMENT.md)
3. Configure: [`DOMAIN_SETUP.md`](DOMAIN_SETUP.md)
4. Maintain: [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)

### **For Quick Fixes:**
1. Common issues: [`QUICK_FIX.md`](QUICK_FIX.md)
2. Detailed solutions: [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
3. Run scripts: `./fix-permissions.sh`

---

## 🔗 External Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Livewire Documentation:** https://livewire.laravel.com
- **FrankenPHP Documentation:** https://frankenphp.dev
- **Docker Documentation:** https://docs.docker.com
- **Tailwind CSS:** https://tailwindcss.com

---

## 📝 Documentation Standards

All documentation in this project follows these principles:

- ✅ **Clear & Concise** - Easy to understand
- ✅ **Step-by-Step** - Detailed instructions
- ✅ **Code Examples** - Real, working code
- ✅ **Quick Reference** - One-liner solutions
- ✅ **Troubleshooting** - Common issues covered
- ✅ **Up-to-date** - Reflects current codebase

---

## 🆘 Need Help?

**Priority order:**
1. Check [`QUICK_FIX.md`](QUICK_FIX.md) for one-liner solutions
2. Read [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md) for detailed help
3. View logs: `docker compose logs app --tail=100`
4. Check specific guide for your task (see structure above)

---

## 📅 Last Updated

This documentation index was last updated: **October 10, 2025**

---

**🌟 Pro Tip:** Bookmark this page for quick access to all documentation!

