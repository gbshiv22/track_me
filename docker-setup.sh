#!/bin/bash

# Track Me App - Docker Setup Script (SQLite)

echo "🐳 Setting up Track Me App with Docker (SQLite)..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    echo "Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    echo "Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

echo "✅ Docker and Docker Compose are installed"

# Create .env file for Docker if it doesn't exist
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file from docker.env..."
    cp docker.env .env
    echo "✅ .env file created"
else
    echo "⚠️  .env file already exists. Backing up to .env.backup..."
    cp .env .env.backup
    echo "📝 Creating new .env file from docker.env..."
    cp docker.env .env
    echo "✅ .env file updated"
fi

# Ensure database directory exists and has proper permissions
echo "📁 Setting up database directory..."
mkdir -p database
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄️  Creating SQLite database file..."
    touch database/database.sqlite
fi
chmod 755 database
chmod 664 database/database.sqlite

# Build and start containers
echo "🔨 Building Docker containers..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for app to be ready
echo "⏳ Waiting for application to be ready..."
sleep 10

# Run Laravel setup commands
echo "🔧 Setting up Laravel application..."

# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate --force

# Clear and cache config
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "✅ Laravel application setup complete!"

echo ""
echo "🎉 Track Me App is now running!"
echo ""
echo "📱 Access your application at:"
echo "   🌐 Web App: http://localhost:8000"
echo "   🔴 Redis: localhost:6379"
echo "   🗄️  Database: SQLite file at ./database/database.sqlite"
echo ""
echo "🔧 Useful Docker commands:"
echo "   📊 View logs: docker-compose logs -f"
echo "   🛑 Stop app: docker-compose down"
echo "   🔄 Restart app: docker-compose restart"
echo "   🐚 Access shell: docker-compose exec app bash"
echo "   🗄️  Database shell: docker-compose exec app sqlite3 /var/www/html/database/database.sqlite"
echo ""
echo "📋 Environment:"
echo "   📁 App Directory: /var/www/html"
echo "   🗄️  Database: SQLite (file-based)"
echo "   🔴 Cache/Sessions: Redis (optional)"
echo "   🌐 Web Server: Apache"
echo ""
echo "🚀 Your Track Me app is ready to use!"
echo ""
echo "💡 Benefits of SQLite:"
echo "   ✅ No external database server needed"
echo "   ✅ Easy backup (just copy the .sqlite file)"
echo "   ✅ Perfect for development and small deployments"
echo "   ✅ No database credentials to manage"