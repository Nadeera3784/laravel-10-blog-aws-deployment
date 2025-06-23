#!/bin/bash

echo "🚀 Starting Laravel Application Fix Script..."

# Navigate to the Laravel application directory
cd /var/www

# Clear all Laravel caches to ensure fresh start
echo "🧹 Clearing Laravel caches..."
php artisan config:clear || echo "Config clear failed"
php artisan route:clear || echo "Route clear failed"
php artisan view:clear || echo "View clear failed"
php artisan cache:clear || echo "Cache clear failed"

# Generate application key if it doesn't exist
echo "🔑 Checking application key..."
if ! php artisan key:generate --show > /dev/null 2>&1; then
    echo "Generating new application key..."
    php artisan key:generate --force
fi

# Wait for database to be ready
echo "🗄️ Waiting for database connection..."
for i in {1..30}; do
    if php artisan migrate:status > /dev/null 2>&1; then
        echo "Database connection successful!"
        break
    fi
    echo "Waiting for database... ($i/30)"
    sleep 2
done

# Run database migrations
echo "📊 Running database migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

# Seed the database if it's empty
echo "🌱 Checking if database needs seeding..."
if php artisan tinker --execute="echo App\Blog\Entities\Category::count();" 2>/dev/null | grep -q "0"; then
    echo "Seeding database..."
    php artisan db:seed --force || echo "Seeding failed, continuing..."
fi

# Create Elasticsearch index
echo "🔍 Setting up Elasticsearch index..."
php artisan elasticsearch:recreate-index || echo "Elasticsearch setup failed, continuing..."

# Set proper permissions
echo "🔒 Setting proper permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "✅ Laravel Application Fix Script completed!"
echo "🌐 Application should now be ready to serve requests"

# Start supervisord to run nginx, php-fpm, and workers
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 