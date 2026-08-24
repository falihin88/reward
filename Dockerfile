# Stage 1: Build Frontend Assets
FROM node:20-alpine AS node_builder
ARG NODE_ENV=development
ENV NODE_ENV=${NODE_ENV}
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP & Web Server
FROM php:8.4-cli-alpine

# Set Environment Variables
ARG APP_ENV=development
ENV APP_ENV=${APP_ENV} \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_CLI_SERVER_WORKERS=4

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    sqlite-dev \
    oniguruma-dev \
    bash \
    curl \
    git \
    netcat-openbsd \
    ca-certificates \
    linux-headers \
    && docker-php-ext-install \
    intl \
    pdo_sqlite \
    pdo_mysql \
    mbstring \
    zip \
    bcmath \
    pcntl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files and built assets from node_builder
COPY . .
COPY --from=node_builder /app/public/build ./public/build

# Install PHP dependencies without running scripts (prevents package:discover failure when env/DB is absent)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs --no-scripts

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy Entrypoint Script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]


