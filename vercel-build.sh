#!/bin/bash

# Verificar se o PHP está instalado
if ! command -v php &> /dev/null; then
    echo "PHP não está instalado. Instalando..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

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