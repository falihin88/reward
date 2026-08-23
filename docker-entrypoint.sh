#!/bin/sh
set -e

# If using MySQL/MariaDB, wait for database host to be ready
if [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "mariadb" ]; then
    echo "Waiting for MariaDB database host ($DB_HOST:$DB_PORT) to be ready..."
    until nc -z -v -w30 "$DB_HOST" "$DB_PORT" 2>/dev/null; do
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

# Ensure storage directory permissions
chmod -R 775 storage bootstrap/cache || true

# Package Discovery
echo "Running Package Discovery..."
php artisan package:discover --ansi || true

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate --force
fi

# Run Database Migrations and Seeders
echo "Running Database Migrations and Seeders..."
php artisan migrate:fresh --force --seed

# Start Laravel server
echo "Starting Laravel server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
