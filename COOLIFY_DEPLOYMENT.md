# Coolify Deployment Guide - AMYN Madrasah

This guide provides step-by-step instructions for deploying **AMYN Madrasah** using **Coolify** (Self-hosted PaaS) and Docker Compose.

---

## 🏗️ Architecture Summary

Coolify deploys a **single** `app` service from `docker-compose.yml`:
- `app`: AMYN Madrasah Laravel / Vue 3 Application (Port `8000`).

The app connects to a **separate, standalone Coolify MariaDB instance** (created via **+ Add Resource -> MariaDB**), not to a bundled database service.

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
   - Set `Domains` for the `app` service to your custom domain **including the port**: `https://madrasah.yourdomain.com:8000`.
   - The `:8000` only tells the proxy which container port to forward to (Coolify ignores `expose:`/`ports:` in the compose file). Visitors still use normal HTTPS on port 443.

Coolify will automatically issue a Let's Encrypt SSL certificate for the domain.

---

### Step 3: Environment Variables

Verify or set the following environment variables in Coolify (App resource -> Environment Variables):

```env
APP_NAME="AMYN Madrasah"
APP_ENV=production
APP_KEY="base64:7qZ0bY1V4mK9WpX3rL8sN2tU5vY6zA1bC2dE3fG4hI5="
APP_DEBUG=false
APP_URL=https://madrasah.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=PASTE_YOUR_MARIADB_INSTANCE_UUID_HERE
DB_PORT=3306
DB_DATABASE=amyn_madrasah
DB_USERNAME=amyn_user
DB_PASSWORD=YOUR_STRONG_CUSTOM_DATABASE_PASSWORD
```

> 🔑 **How to find `DB_HOST`**: Open your standalone MariaDB resource in Coolify, then click **Connect** (or look for the **Internal connection string**). The host is a UUID like `rolq9rulhuh46kmuxx8mjrfk` — copy that value into `DB_HOST`. It is **not** `mariadb`, `localhost`, or an IP.

> 🔌 **Networking**: The app and the MariaDB instance must share a Docker network.
> 1. In the **MariaDB resource** settings, ensure it is connected to the `coolify` network (this is the default for standalone Coolify databases).
> 2. The `docker-compose.yml` already attaches the `app` service to the `coolify` network (`external: true`).
>
> If `DB_HOST` is a UUID and you still can't connect, both resources are likely on different networks.

> 💡 **Build-Time vs Runtime Variables Tip**:
> In Coolify settings, set `APP_ENV`, `APP_KEY`, and database credentials to **Runtime Only** (uncheck "Build Time"). This prevents `APP_ENV=production` from suppressing devDependencies during container image build. Our Dockerfile also enforces `ENV NODE_ENV=development` in the asset building stage to guarantee Vite and Tailwind compile properly during build.

> 🛡️ **Security**:
> - The app enforces encrypted session cookies (`SESSION_ENCRYPT=true`).
> - MariaDB is not bundled into the app compose; it runs as its own Coolify resource.

---

### Step 4: Deploy

1. Click **Deploy** in Coolify.
2. Coolify will build the Docker image, the entrypoint waits for the database host to be reachable, runs safe migrations (`php artisan migrate --force`), and launches the site.

> 🗄️ **Production Database Safety**:
> - When `APP_ENV=production`, deployments **preserve all existing data** and only execute pending migrations. It will **NEVER** wipe or re-seed the database automatically.
> - To force a initial seed without wiping data on initial setup, set `SEED_ON_DEPLOY=true` in environment variables.
> - For development (`APP_ENV=local` or `development`), `docker-entrypoint.sh` runs `migrate:fresh --seed` automatically.

---

## 🛠️ Troubleshooting: "WARNING: Still cannot reach DB_HOST" in Coolify

If your deployment logs show `WARNING: Still cannot reach <host-or-id>:3306`, it means the `app` container cannot resolve or connect to your separate MariaDB instance.

### Solution A: Connect both resources to the `coolify` Docker network (Recommended)
1. In Coolify Dashboard, go to your **MariaDB Resource** -> **Settings** -> **Network** -> Ensure it is attached to the **`coolify`** network.
2. In your **App (Docker Compose) Resource** -> **Settings** -> **Network** -> Ensure it is attached to the **`coolify`** network.
3. In App Environment Variables, set `DB_HOST` to your MariaDB internal service name/ID (e.g., `rolq9rulhuh46kmuxx8mjrfk`).

### Solution B: Connect via Host Gateway (`host.docker.internal`)
If your MariaDB is running directly on the host server or has port `3306` published to host:
1. In App Environment Variables, set `DB_HOST=host.docker.internal`.
2. Our `docker-compose.yml` already includes `extra_hosts: ["host.docker.internal:host-gateway"]` to route `host.docker.internal` directly to the host machine's network.

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
