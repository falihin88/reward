# AMYN Madrasah - Striving for the Deeds

![AMYN Madrasah Logo](public/logo.png)

**AMYN Madrasah** is a modern, Islamic Gamified Learning Management System designed to motivate students in madrasah and classroom environments through daily streaks, attendance point rewards, teacher recognition, and collectible Scholar Cards featuring Islamic historical figures and Hadith scholars.

---

## 🌟 Key Features

- 🟢 **Daily Attendance Engine (+100 Pts Reward)**:
  - 1-click batch attendance sheet for teachers.
  - Marking students **Present (+100 Points)** or **Late (+50 Points)** automatically awards points to their balance and updates learning streaks.
- 🎭 **Teacher "Login as Student" Impersonation**:
  - Solve device access constraints: Teachers can log in as any student with 1 click to help device-less learners view their card collection and unlock rewards.
- 👥 **Student CRUD Operations**:
  - Teachers and Admins can create new student accounts, update profiles/points, and delete student accounts directly from their dashboard.
- 🃏 **Scholar Card Deck & Artwork Upload**:
  - Collectible trading card deck with Common, Rare, Epic, and Legendary rarity tiers.
  - Admins can create new scholar cards and upload custom artwork/photos directly.
- 🌙 **Dark & Light Theme Switcher**:
  - Toggle between sleek dark mode and bright light mode with `localStorage` memory.
- ⚡ **Daily Streaks & Leaderboards**:
  - Students earn extra rewards for consecutive daily check-ins.

---

## 🚀 Easy Deployment with Docker Compose

To deploy AMYN Madrasah in production or staging with a single command:

```bash
# Clone repository
git clone https://github.com/your-repo/amyn-madrasah.git
cd classapp

# Build and start container background daemon
docker compose up -d --build
```

The application stack includes 3 containers:
- 📱 **AMYN Madrasah Web Application**: `http://localhost:8000`
- 🗄️ **MariaDB Database Server**: Port `3306`
- 🛠️ **phpMyAdmin Database Management**: `http://localhost:8080` (Login with user: `amyn_user`, pass: `amyn_secret_password` or root: `root_secret_password`)

Database state (`mariadb_data`), uploads (`uploads_data`), and application storage (`storage_data`) persist automatically across container restarts.

> 📘 **Coolify Deployment Guide**: For step-by-step self-hosted PaaS deployment with Coolify and automated SSL, see [COOLIFY_DEPLOYMENT.md](COOLIFY_DEPLOYMENT.md).

---

## 💻 Manual Local Installation

If running locally with PHP 8.4+ and Node.js 20+:

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Create SQLite Database & Run Migrations with Seeder
touch database/database.sqlite
php artisan migrate:fresh --seed

# 5. Build Frontend Assets
npm run build

# 6. Start Local Server
php artisan serve
```

Access the application at `http://127.0.0.1:8000`.

---

## 🔑 Default Accounts

After running `php artisan migrate:fresh --seed`, log in with any of the default accounts:

| Role | Email Address | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@amynmadrasah.com` | `password` |
| **Teacher** | `ahmad@amynmadrasah.com` | `password` |
| **Student** | `tariq@amynmadrasah.com` | `password` |
| **Student** | `fatima@amynmadrasah.com` | `password` |
| **Student** | `zayd@amynmadrasah.com` | `password` |

---

## 🛠️ Stack & Technologies

- **Backend**: Laravel 11 / PHP 8.4
- **Frontend**: Vue 3 (Options API / Composition API) + Inertia.js
- **Styling**: Tailwind CSS with custom HSL CSS variables & dark mode
- **Database**: SQLite / MySQL / PostgreSQL support
- **Containerization**: Docker & Docker Compose
