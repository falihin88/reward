# Coolify Deployment Guide - AMYN Madrasah

This guide provides step-by-step instructions for deploying **AMYN Madrasah** using **Coolify** (Self-hosted PaaS) and Docker Compose.

---

## 🏗️ Architecture Summary

Coolify will deploy 3 connected Docker services defined in `docker-compose.yml`:
1. `app`: AMYN Madrasah Laravel 11 / Vue 3 Application (Port `8000`).
2. `mariadb`: MariaDB 11 Database Server (Port `3306`).
3. `phpmyadmin`: Web Database GUI (Port `8080`).

---

## 🚀 Step-by-Step Coolify Deployment

### Step 1: Add a New Resource in Coolify
1. Log into your **Coolify Dashboard**.
2. Click **+ Add Resource** -> Select **Docker Compose**.
3. Choose **Public Repository** (or **Private Repository** if SSH key required).
4. Enter repository URL: `https://github.com/falihin88/reward.git`
5. Set branch: `main`
6. Set Base Directory: `/` (or leave default).

---

### Step 2: Configure Web Domain & Reverse Proxy

In the Coolify Configuration Panel for your resource:

1. **Main Web App Domain**:
   - Set `Domains` for the `app` service to your custom domain: `https://madrasah.yourdomain.com` (pointing to port `8000`).
2. **phpMyAdmin Domain (Optional)**:
   - Set `Domains` for `phpmyadmin` service to: `https://pma.yourdomain.com` (pointing to port `8080`).

Coolify will automatically issue Let's Encrypt SSL certificates for both domains!

---

### Step 3: Environment Variables

Verify or set the following environment variables in Coolify:

```env
APP_NAME="AMYN Madrasah"
APP_ENV=production
APP_KEY="base64:7qZ0bY1V4mK9WpX3rL8sN2tU5vY6zA1bC2dE3fG4hI5="
APP_DEBUG=false
APP_URL=https://madrasah.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=amyn_madrasah
DB_USERNAME=amyn_user
DB_PASSWORD=YOUR_STRONG_CUSTOM_DATABASE_PASSWORD
MYSQL_ROOT_PASSWORD=YOUR_STRONG_ROOT_DATABASE_PASSWORD
```

> 🛡️ **Database Security Hardening Implemented**:
> 1. **Isolated Container Network**: MariaDB is placed on an `internal: true` backend network with **no exposed public host ports** (`port 3306` is not open to public port scans).
> 2. **Restricted phpMyAdmin**: `PMA_ARBITRARY=0` is set to block attempts to connect to external servers.
> 3. **DNS Spoofing Prevention**: MariaDB `--skip-name-resolve` flag enabled for faster and safer authentication.
> 4. **Session Encryption**: Enforce encrypted session cookies (`SESSION_ENCRYPT=true`).

---

### Step 4: Deploy

1. Click **Deploy** in Coolify.
2. Coolify will build the Docker container image, wait for MariaDB healthcheck to pass, run safe database migrations (`php artisan migrate --force`), and launch the site.

> 🗄️ **Production Database Safety**:
> - When `APP_ENV=production`, deployments **preserve all existing data** and only execute pending migrations. It will **NEVER** wipe or re-seed the database automatically.
> - To force a initial seed without wiping data on initial setup, set `SEED_ON_DEPLOY=true` in environment variables.
> - For development (`APP_ENV=local` or `development`), `docker-entrypoint.sh` runs `migrate:fresh --seed` automatically.

---

## 🔑 Default Accounts After Deployment

Log in at `https://madrasah.yourdomain.com`:

| Role | Email Address | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@amynmadrasah.com` | `password` |
| **Teacher** | `ahmad@amynmadrasah.com` | `password` |
| **Student** | `tariq@amynmadrasah.com` | `password` |
| **Student** | `fatima@amynmadrasah.com` | `password` |
| **Student** | `zayd@amynmadrasah.com` | `password` |
