# 🎓 Aozora Education Management System

Modern school management system built with Laravel, Livewire, and FrankenPHP.

---

## 🚀 Quick Start - Localhost Deployment

### **3 Simple Steps:**

1. **Setup Flux Pro Credentials**
   ```bash
   ./setup-flux-credentials.sh
   ```

2. **Run Setup Script**
   ```bash
   ./setup-localhost.sh
   ```

3. **Access Application**
   ```
   URL: http://localhost:8080
   Admin: admin@school.com / password
   ```

**Full guide:** [`LOCALHOST_QUICK_START.md`](LOCALHOST_QUICK_START.md)

---

## 📚 Documentation

### **Deployment Guides:**
- 🚀 [`LOCALHOST_QUICK_START.md`](LOCALHOST_QUICK_START.md) - Deploy di localhost dalam 3 langkah
- 🔧 [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md) - Solutions untuk common issues
- ⚡ [`QUICK_FIX.md`](QUICK_FIX.md) - One-liner quick fixes
- 📦 [`DEPLOYMENT.md`](DEPLOYMENT.md) - Full production deployment guide
- 🌐 [`DOMAIN_SETUP.md`](DOMAIN_SETUP.md) - Domain & DNS configuration
- 🚀 [`QUICK_START_PRODUCTION.md`](QUICK_START_PRODUCTION.md) - Quick production setup

### **Performance & Updates:**
- ⚡ [`PERFORMANCE.md`](PERFORMANCE.md) - FrankenPHP Worker Mode optimization
- 🔄 [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md) - How to update application
- 📝 [`UPDATE_CHEATSHEET.md`](UPDATE_CHEATSHEET.md) - Quick update reference

### **Setup Scripts:**
- 🛠️ [`setup-localhost.sh`](setup-localhost.sh) - Automated localhost setup
- 🔐 [`setup-flux-credentials.sh`](setup-flux-credentials.sh) - Setup Flux Pro auth
- 🔧 [`fix-permissions.sh`](fix-permissions.sh) - Fix permission issues

---

## 🏗️ Technology Stack

- **Backend:** Laravel 11.x
- **Frontend:** Livewire 3.x, Flux Pro
- **UI:** Tailwind CSS
- **Server:** FrankenPHP (with Worker Mode)
- **Database:** MySQL 8.0
- **Cache:** Redis
- **Queue:** Redis
- **Deployment:** Docker + Docker Compose

---

## ✨ Features

### **Core Features:**
- 👥 User Management (Admin, Teacher, Parent, Student)
- 🎓 Student & Enrollment Management
- 📚 Subject & Class Schedule Management
- 📝 Digital Attendance System
- 💰 Invoice & Payment Management
- 📊 Dashboard & Analytics
- 🔐 Role-Based Access Control (RBAC)
- 🏫 Multi-Organization Support

### **Technical Features:**
- ⚡ FrankenPHP Worker Mode (3-5x faster)
- 🔄 Background Job Processing
- 📅 Task Scheduling
- 🗄️ Persistent Database
- 💾 Redis Caching
- 📧 Email Notifications
- 🔒 Secure Authentication

---

## 🖥️ System Requirements

### **For Development/Testing:**
- Docker Desktop
- 4GB RAM minimum
- 10GB free disk space
- macOS, Linux, or Windows (WSL2)

### **For Production:**
- Ubuntu 20.04+ or Debian 11+
- 2 CPU cores minimum
- 2GB RAM minimum (4GB recommended)
- 20GB free disk space
- Domain name with DNS access

---

## ⚡ Performance

With FrankenPHP Worker Mode enabled:
- **Response Time:** 50-200ms (vs 500-1000ms standard PHP)
- **Throughput:** 3-5x faster than traditional PHP-FPM
- **Memory:** Persistent application state
- **Concurrency:** Handles more requests per second

---

## 🔧 Common Issues & Solutions

### **Missing APP_KEY Error:**
```bash
docker compose down && docker compose up -d && sleep 15 && docker compose exec app php artisan key:generate --force && docker compose exec app php artisan optimize:clear
```

### **Permission Denied:**
```bash
./fix-permissions.sh
```

### **Port Already in Use:**
Edit `.env` and change `APP_PORT=9000`, then `docker compose restart`

**More solutions:** [`QUICK_FIX.md`](QUICK_FIX.md) | [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)

---

## 📋 Useful Commands

### **Development:**
```bash
# View logs
docker compose logs -f app

# Access container shell
docker compose exec app bash

# Run artisan commands
docker compose exec app php artisan <command>

# Reset database
docker compose exec app php artisan migrate:fresh --seed
```

### **Maintenance:**
```bash
# Restart services
docker compose restart

# Stop services
docker compose down

# Rebuild
docker compose build --no-cache

# Check status
docker compose ps
```

**Full reference:** [`QUICK_FIX.md`](QUICK_FIX.md)

---

## 🔄 Updates

When you have code updates:

```bash
git pull
docker compose down
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
```

**Detailed guide:** [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md)

---

## 🧪 Test Accounts

After running setup, use these accounts to login:

| Role    | Email                    | Password   |
|---------|--------------------------|------------|
| Admin   | admin@school.com         | password   |
| Parent  | parent@test.com          | password   |
| Teacher | teacher@test.com         | password   |
| Student | student@test.com         | password   |

---

## 🛡️ Security Notes

For **production deployment:**
- Change all default passwords
- Use strong APP_KEY
- Enable HTTPS (automatic with Caddy)
- Configure firewall
- Regular backups
- Keep Docker images updated

**Production guide:** [`DEPLOYMENT.md`](DEPLOYMENT.md)

---

## 📞 Support & Troubleshooting

1. **Check Quick Fixes:** [`QUICK_FIX.md`](QUICK_FIX.md)
2. **Read Troubleshooting:** [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md)
3. **View Logs:** `docker compose logs app --tail=100`
4. **Check Container Status:** `docker compose ps`

---

## 🤝 Contributing

This is a private educational project. For issues or questions, contact the development team.

---

## 📄 License

Proprietary - All rights reserved.

---

## 🎯 Quick Links

| Document | Purpose |
|----------|---------|
| [`LOCALHOST_QUICK_START.md`](LOCALHOST_QUICK_START.md) | ⚡ Start here for localhost |
| [`QUICK_FIX.md`](QUICK_FIX.md) | 🔧 Quick solutions |
| [`LOCALHOST_TROUBLESHOOTING.md`](LOCALHOST_TROUBLESHOOTING.md) | 🐛 Detailed troubleshooting |
| [`DEPLOYMENT.md`](DEPLOYMENT.md) | 🌐 Production deployment |
| [`PERFORMANCE.md`](PERFORMANCE.md) | ⚡ Performance tuning |
| [`UPDATE_GUIDE.md`](UPDATE_GUIDE.md) | 🔄 How to update |

---

**Built with ❤️ for Modern Education Management**

🌟 **Ready to start?** → [`LOCALHOST_QUICK_START.md`](LOCALHOST_QUICK_START.md)

