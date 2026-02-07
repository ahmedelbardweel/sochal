#!/bin/sh
echo "Clearing configuration cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Running migrations..."
php artisan migrate --force

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
