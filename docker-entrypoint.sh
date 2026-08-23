#!/bin/sh
set -e

# If using MySQL/MariaDB, wait for database host to be ready
if [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "mariadb" ]; then
    echo "Waiting for MariaDB database host ($DB_HOST:$DB_PORT) to be ready..."
    DB_WAIT_ATTEMPTS=0
    until nc -z -w 2 "$DB_HOST" "$DB_PORT" 2>/dev/null; do
        DB_WAIT_ATTEMPTS=$((DB_WAIT_ATTEMPTS + 1))
        if [ $((DB_WAIT_ATTEMPTS % 15)) -eq 0 ]; then
            echo "WARNING: Still cannot reach $DB_HOST:$DB_PORT after $((DB_WAIT_ATTEMPTS * 2))s."
            echo "WARNING: Ensure this resource is deployed as 'Docker Compose' in Coolify (not as a single application),"
            echo "WARNING: and that DB_HOST is set to the compose service name 'mariadb'."
        fi
        echo "MariaDB is unavailable - sleeping 2 seconds..."
        sleep 2
    done
    echo "MariaDB connection established!"
elif [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p database
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
    fi
fi

# Ensure all required storage subdirectories exist (crucial for Docker volume mounts)
mkdir -p storage/app/public \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

# Ensure storage directory permissions
chmod -R 775 storage bootstrap/cache || true

# Ensure storage link exists for public access to uploaded files
echo "Linking public storage..."
php artisan storage:link --force || true

# Package Discovery
echo "Running Package Discovery..."
php artisan package:discover --ansi || true

# Initialize .env file if missing (since .env is omitted by .dockerignore)
if [ ! -f .env ]; then
    echo "Initializing .env file..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate --force
fi

# Run Database Migrations and Seeders
echo "Checking Environment ($APP_ENV) for Database Migrations..."
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ] || [ "$APP_ENV" = "dev" ] || [ "$DB_FRESH_ON_DEPLOY" = "true" ]; then
    echo "Development environment detected. Running migrate:fresh with seeders..."
    php artisan migrate:fresh --force --seed
else
    echo "Production environment detected. Running safe database migrations (migrate --force)..."
    php artisan migrate --force
    if [ "$SEED_ON_DEPLOY" = "true" ]; then
        echo "SEED_ON_DEPLOY is enabled. Seeding database..."
        php artisan db:seed --force
    fi

    # Optimize Laravel for production environment
    echo "Optimizing application routes, views, and config for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Start Laravel server
echo "Starting Laravel server on port 8000 (workers: ${PHP_CLI_SERVER_WORKERS:-4})..."
exec php artisan serve --host=0.0.0.0 --port=8000

