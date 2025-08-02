#!/bin/bash

echo "🚫 Disabling all caching for development..."

# Clear all Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan responsecache:clear
php artisan optimize:clear

# Clear compiled views
rm -rf storage/framework/views/*

# Clear compiled configs
rm -rf bootstrap/cache/*

echo "✅ All caches disabled!"
echo "💡 Remember to refresh your browser with Ctrl+F5" 