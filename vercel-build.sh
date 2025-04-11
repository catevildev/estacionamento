#!/bin/bash

echo "Installing composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Generating application key..."
php artisan key:generate

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage link..."
php artisan storage:link

echo "Build completed successfully!" 