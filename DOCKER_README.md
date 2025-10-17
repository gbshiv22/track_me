# 🐳 Track Me App - Docker Setup

## 📋 Overview

This Laravel application is containerized with Docker for easy development and deployment. The setup includes:

- **PHP 8.3** with Apache
- **SQLite** database (file-based, no external server needed)
- **Redis** for caching and sessions (optional)
- **Nginx** (production only)

## 🚀 Quick Start

### Development Environment

1. **Run the setup script:**
```bash
./docker-setup.sh
```

2. **Access your application:**
- 🌐 Web App: http://localhost:8000
- 🗄️ Database: SQLite file at ./database/database.sqlite
- 🔴 Redis: localhost:6379 (optional)

### Manual Setup

1. **Build and start containers:**
```bash
docker-compose up -d
```

2. **Setup Laravel:**
```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan config:cache
```

## 📁 Docker Files Structure

```
track_me/
├── Dockerfile                    # Development Docker image
├── Dockerfile.production         # Production Docker image
├── docker-compose.yml           # Development services
├── docker-compose.production.yml # Production services
├── docker.env                   # Environment variables for Docker
├── docker-setup.sh              # Automated setup script
├── .dockerignore                # Docker ignore file
└── docker/
    ├── apache-config.conf       # Apache configuration
    └── nginx.conf               # Nginx configuration (production)
```

## 🔧 Services

### Development (`docker-compose.yml`)

| Service | Port | Description |
|---------|------|-------------|
| **app** | 8000 | Laravel application (Apache) |
| **redis** | 6379 | Redis cache/sessions (optional) |

### Production (`docker-compose.production.yml`)

| Service | Port | Description |
|---------|------|-------------|
| **app** | 80 | Laravel application (Apache) |
| **nginx** | 443 | Reverse proxy with SSL |
| **redis** | - | Redis cache/sessions (internal) |

## 🛠️ Docker Commands

### Development Commands

```bash
# Start services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Restart services
docker-compose restart

# Access app shell
docker-compose exec app bash

# Access database
docker-compose exec db mysql -u track_me_user -p track_me

# Run Laravel commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan queue:work
```

### Production Commands

```bash
# Start production services
docker-compose -f docker-compose.production.yml up -d

# View production logs
docker-compose -f docker-compose.production.yml logs -f

# Stop production services
docker-compose -f docker-compose.production.yml down
```

## 🔧 Environment Configuration

### Development Environment Variables

The `docker.env` file contains development settings:

```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=track_me
DB_USERNAME=track_me_user
DB_PASSWORD=track_me_password
SESSION_DRIVER=redis
CACHE_STORE=redis
REDIS_HOST=redis
```

### Production Environment Variables

For production, update these variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_PASSWORD=secure_password_here
MYSQL_ROOT_PASSWORD=secure_root_password
```

## 🗄️ Database

### MySQL Configuration

- **Host**: `db` (internal) / `localhost:3306` (external)
- **Database**: `track_me`
- **Username**: `track_me_user`
- **Password**: `track_me_password`

### Database Commands

```bash
# Access MySQL shell
docker-compose exec db mysql -u track_me_user -p track_me

# Backup database
docker-compose exec db mysqldump -u track_me_user -p track_me > backup.sql

# Restore database
docker-compose exec -T db mysql -u track_me_user -p track_me < backup.sql
```

## 🔴 Redis

### Redis Configuration

- **Host**: `redis` (internal) / `localhost:6379` (external)
- **Port**: 6379
- **Password**: None (development)

### Redis Commands

```bash
# Access Redis CLI
docker-compose exec redis redis-cli

# Clear all cache
docker-compose exec redis redis-cli FLUSHALL

# Monitor Redis
docker-compose exec redis redis-cli MONITOR
```

## 🚀 Deployment

### Development Deployment

1. **Local Development:**
```bash
./docker-setup.sh
```

2. **Access Application:**
- Open http://localhost:8000
- Register a new account
- Test tracking features

### Production Deployment

1. **Update Environment Variables:**
```bash
# Edit docker-compose.production.yml
# Update passwords and domain
```

2. **Deploy:**
```bash
docker-compose -f docker-compose.production.yml up -d
```

3. **Setup SSL (Optional):**
```bash
# Add SSL certificates to docker/ssl/
# Update nginx.conf if needed
```

## 🔍 Troubleshooting

### Common Issues

#### 1. Port Already in Use
```bash
# Check what's using the port
lsof -i :8000

# Stop conflicting services
sudo service apache2 stop
```

#### 2. Database Connection Error
```bash
# Check database logs
docker-compose logs db

# Restart database
docker-compose restart db
```

#### 3. Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage
docker-compose exec app chmod -R 755 storage
```

#### 4. Container Won't Start
```bash
# Check container logs
docker-compose logs app

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Debug Commands

```bash
# Check container status
docker-compose ps

# Check container resources
docker stats

# Access container shell
docker-compose exec app bash

# Check Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log
```

## 📊 Monitoring

### Health Checks

- **Application**: http://localhost:8000/health
- **Database**: `docker-compose exec db mysqladmin ping`
- **Redis**: `docker-compose exec redis redis-cli ping`

### Logs

```bash
# Application logs
docker-compose logs -f app

# Database logs
docker-compose logs -f db

# All services logs
docker-compose logs -f
```

## 🔒 Security

### Development Security

- Default passwords (change for production)
- Debug mode enabled
- No SSL encryption

### Production Security

- Strong passwords required
- SSL/TLS encryption
- Security headers enabled
- Debug mode disabled
- Firewall configuration

## 📈 Performance

### Optimization Tips

1. **Use Redis for sessions and cache**
2. **Enable OPcache in production**
3. **Use CDN for static assets**
4. **Configure database connection pooling**
5. **Monitor resource usage**

### Resource Limits

```yaml
# Add to docker-compose.yml
services:
  app:
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M
```

## 🆘 Support

If you encounter issues:

1. Check Docker logs: `docker-compose logs -f`
2. Verify environment variables
3. Check container status: `docker-compose ps`
4. Review this documentation
5. Check Laravel logs: `storage/logs/laravel.log`

## 🎯 Features Included

✅ **Laravel 11** with PHP 8.3  
✅ **MySQL 8.0** database  
✅ **Redis** caching and sessions  
✅ **Apache** web server  
✅ **Nginx** reverse proxy (production)  
✅ **SSL/TLS** support (production)  
✅ **Health checks**  
✅ **Automated setup** script  
✅ **Development and production** configurations  

Your Track Me app is now fully containerized and ready for development and deployment! 🚀
