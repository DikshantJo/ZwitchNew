#!/bin/bash

# Hostinger Deployment Script for Bagisto
# This script will be executed on Hostinger after each GitHub push

echo "🚀 Starting Bagisto deployment..."

# Set environment
APP_ENV=production
APP_DEBUG=false

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Install/Update NPM dependencies and build assets
echo "🎨 Building frontend assets..."
npm install --production
npm run build

# Build theme assets
echo "🎨 Building theme assets..."
cd packages/Webkul/Shop && npm install --production && npm run build && cd ../../..
cd packages/Webkul/Admin && npm install --production && npm run build && cd ../../..

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