#!/bin/bash

# Track Me App - Quick Deployment Script
echo "🚀 Preparing Track Me App for Deployment..."

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found. Please run this script from the project root."
    exit 1
fi

echo "📦 Installing production dependencies..."
composer install --optimize-autoloader --no-dev

echo "🔧 Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🗄️ Checking database..."
if [ ! -f "database/database.sqlite" ]; then
    echo "⚠️  Warning: SQLite database not found. You'll need to set up MySQL for production."
fi

echo "📁 Creating deployment package..."
# Create a deployment folder
mkdir -p ../track_me_deploy
cp -r . ../track_me_deploy/
cd ../track_me_deploy

# Remove development files
rm -rf .git
rm -rf node_modules
rm -rf tests
rm -f .env.example
rm -f README.md
rm -f DEPLOYMENT_GUIDE.md
rm -f FULLSCREEN_FEATURE.md

echo "✅ Deployment package created in ../track_me_deploy/"
echo ""
echo "📋 Next Steps:"
echo "1. Upload the contents of ../track_me_deploy/ to your hosting provider"
echo "2. Set up MySQL database"
echo "3. Update .env file with production settings"
echo "4. Set file permissions (storage/ and bootstrap/cache/ should be writable)"
echo ""
echo "🌐 Recommended Platforms:"
echo "- Heroku: https://heroku.com (Free tier available)"
echo "- Railway: https://railway.app (Free credits)"
echo "- Render: https://render.com (Free tier)"
echo "- InfinityFree: https://infinityfree.net (Limited but free)"
