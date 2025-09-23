#!/bin/bash

# Japan24 Production Deployment Script
# This script helps deploy the application to production environment

set -e

echo "🚀 Starting Japan24 Production Deployment..."

# Check if .env.production exists
if [ ! -f ".env.production" ]; then
    echo "❌ .env.production file not found!"
    echo "Please copy .env.production template and configure it first."
    exit 1
fi

# Generate new application key if needed
echo "🔐 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env.production; then
    echo "Generating new application key..."
    php artisan key:generate --show --env=production
fi

# Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p bootstrap/cache
mkdir -p docker/ssl

# Set permissions
echo "🔧 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Install production dependencies
echo "📦 Installing production dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and cache Laravel configurations
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "💾 Running database migrations..."
php artisan migrate --force

# Seed database if needed
read -p "Do you want to seed the database? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
fi

# Build and start production containers
echo "🐳 Building production Docker containers..."
docker compose -f docker-compose.prod.yml build --no-cache

echo "🚀 Starting production containers..."
docker compose -f docker-compose.prod.yml up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Run health check
echo "🏥 Running health check..."
if curl -f http://localhost/api/public/status > /dev/null 2>&1; then
    echo "✅ Application is running successfully!"
else
    echo "❌ Health check failed. Please check the logs:"
    echo "docker compose -f docker-compose.prod.yml logs"
    exit 1
fi

echo ""
echo "🎉 Deployment completed successfully!"
echo ""
echo "📊 Container status:"
docker compose -f docker-compose.prod.yml ps
echo ""
echo "📝 To view logs:"
echo "docker compose -f docker-compose.prod.yml logs -f"
echo ""
echo "🔧 To access the application container:"
echo "docker compose -f docker-compose.prod.yml exec app sh"
echo ""
echo "🌐 Your application should be available at:"
echo "http://localhost (or your configured domain)"