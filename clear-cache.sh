#!/bin/bash

echo "🧹 Clearing all caches for development..."

# Clear Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan responsecache:clear
php artisan optimize:clear

# Clear Bagisto specific caches
php artisan theme:clear
php artisan product:clear
php artisan category:clear

# Clear compiled views
rm -rf storage/framework/views/*

# Clear compiled configs
rm -rf bootstrap/cache/*

echo "✅ All caches cleared!"
echo "💡 Remember to refresh your browser with Ctrl+F5" 