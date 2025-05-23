#!/bin/bash
set -e
set -x

echo "Deployment started ..."

# Enter maintenance mode or return true
# if already is in maintenance mode
php artisan down || true

# Pull the latest version of the app
git pull origin main

# Install composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Clear the old cache
php artisan clear-compiled

# Recreate cache
php artisan optimize
php artisan icons:clear
php artisan icons:cache

# Compile npm assets
npm ci
npm run build

# Run database migrations
php artisan migrate --force

# Exit maintenance mode
php artisan up

echo "Deployment finished!"