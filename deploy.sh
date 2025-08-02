#!/bin/bash

# Hostinger Deployment Script for Bagisto (Shared Hosting Version)
# This script will be executed on Hostinger after each GitHub push

echo "🚀 Starting Bagisto deployment..."

# Set environment
APP_ENV=production
APP_DEBUG=false

# Pull latest changes from GitHub
echo "📥 Pulling latest changes from main branch..."
git pull origin main

# Install/Update Composer dependencies (using local composer.phar if needed)
echo "📦 Installing Composer dependencies..."
if command -v composer &> /dev/null; then
    composer install --optimize-autoloader --no-dev --no-interaction
else
    echo "Using local composer.phar..."
    php composer.phar install --optimize-autoloader --no-dev --no-interaction
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public/storage

# Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan responsecache:clear

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations (if needed)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Create storage link if it doesn't exist
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Deployment completed successfully!"
echo "⚠️  Note: Frontend assets were not rebuilt. Build them locally and upload if needed." 