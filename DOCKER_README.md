# 🐳 Docker Deployment Guide for Biometric Attendance System

## 📋 **Prerequisites**

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git
- At least 4GB RAM available
- 10GB free disk space

## 🚀 **Quick Start**

### **1. Clone and Setup**
```bash
git clone <your-repo-url>
cd biometric-attendance_cmp
```

### **2. Deploy with Script**
```bash
# Make script executable
chmod +x docker-deploy.sh

# Run deployment
./docker-deploy.sh
```

### **3. Manual Deployment**
```bash
# Create environment file
cp env_docker.txt .env

# Create directories
mkdir -p nginx/ssl storage/logs database/init

# Generate SSL certificate
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout nginx/ssl/key.pem \
    -out nginx/ssl/cert.pem \
    -subj "/C=NG/ST=State/L=City/O=Organization/CN=localhost"

# Start services
docker-compose up --build -d

# Run migrations and seeders
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
docker-compose exec app php artisan key:generate --no-interaction
```

## 🌐 **Access Points**

- **Application**: http://localhost:8000
- **HTTPS**: https://localhost
- **Database**: localhost:3306
- **Redis**: localhost:6379

## 📁 **Project Structure**

```
biometric-attendance_cmp/
├── app/                    # Laravel application
├── database/              # Database files
├── nginx/                 # Nginx configuration
│   ├── nginx.conf        # Main Nginx config
│   └── ssl/              # SSL certificates
├── storage/               # Laravel storage
├── .env                   # Environment variables
├── .env.docker           # Docker environment template
├── docker-compose.yml    # Docker services
├── Dockerfile            # Application container
├── .dockerignore         # Docker ignore file
└── docker-deploy.sh      # Deployment script
```

## 🔧 **Configuration**

### **Environment Variables**
```env
# Database
DB_HOST=db
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=attendance_user
DB_PASSWORD=your_password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Application
APP_URL=https://your-domain.com
APP_ENV=production
```

### **SSL Configuration**
- **Development**: Self-signed certificates in `nginx/ssl/`
- **Production**: Replace with real certificates
- **Auto-renewal**: Configure Let's Encrypt if needed

## 🐳 **Docker Commands**

### **Service Management**
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart services
docker-compose restart

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f nginx
```

### **Container Access**
```bash
# Access app container
docker-compose exec app bash

# Access database
docker-compose exec db mysql -u attendance_user -p attendance_db

# Access Redis
docker-compose exec redis redis-cli

# Access Nginx
docker-compose exec nginx sh
```

### **Database Operations**
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Reset database
docker-compose exec app php artisan migrate:fresh --seed

# Create backup
docker-compose exec db mysqldump -u attendance_user -p attendance_db > backup.sql
```

### **Application Commands**
```bash
# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Optimize for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Generate key
docker-compose exec app php artisan key:generate
```

## 🔒 **Security Features**

- **Rate Limiting**: API and login endpoints
- **Security Headers**: XSS protection, CSRF, etc.
- **SSL/TLS**: HTTPS enforcement
- **File Access Control**: Sensitive directories protected
- **Input Validation**: Laravel validation rules

## 📊 **Monitoring & Logs**

### **Application Logs**
```bash
# View Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log

# View Nginx logs
docker-compose exec nginx tail -f /var/log/nginx/access.log
docker-compose exec nginx tail -f /var/log/nginx/error.log
```

### **Container Monitoring**
```bash
# Container status
docker-compose ps

# Resource usage
docker stats

# Health check
curl -f http://localhost/health
```

## 🚨 **Troubleshooting**

### **Common Issues**

#### **1. Port Conflicts**
```bash
# Check what's using port 80/443
sudo netstat -tlnp | grep :80
sudo netstat -tlnp | grep :443

# Change ports in docker-compose.yml if needed
```

#### **2. Permission Issues**
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage/
docker-compose exec app chmod -R 775 storage/
```

#### **3. Database Connection**
```bash
# Check database status
docker-compose exec db mysqladmin ping -u attendance_user -p

# Test connection from app
docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo();"
```

#### **4. SSL Issues**
```bash
# Regenerate SSL certificate
rm nginx/ssl/*
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout nginx/ssl/key.pem \
    -out nginx/ssl/cert.pem \
    -subj "/C=NG/ST=State/L=City/O=Organization/CN=localhost"
```

### **Reset Everything**
```bash
# Complete reset
docker-compose down --volumes --remove-orphans
docker system prune -a
./docker-deploy.sh
```

## 🔄 **Updates & Maintenance**

### **Update Application**
```bash
# Pull latest code
git pull origin main

# Rebuild and restart
docker-compose down
docker-compose up --build -d

# Run migrations
docker-compose exec app php artisan migrate
```

### **Backup & Restore**
```bash
# Backup database
docker-compose exec db mysqldump -u attendance_user -p attendance_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database
docker-compose exec -T db mysql -u attendance_user -p attendance_db < backup_file.sql
```

## 📈 **Production Deployment**

### **1. Update Environment**
```bash
# Edit .env file
nano .env

# Set production values
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

### **2. SSL Certificate**
```bash
# Replace self-signed with real certificate
cp /path/to/your/cert.pem nginx/ssl/cert.pem
cp /path/to/your/key.pem nginx/ssl/key.pem
```

### **3. Security Hardening**
```bash
# Update passwords
# Configure firewall
# Set up monitoring
# Configure backups
```

## 🆘 **Support**

- **Documentation**: Check this README
- **Logs**: `docker-compose logs -f`
- **Status**: `docker-compose ps`
- **Health**: `curl http://localhost/health`

## 📝 **Changelog**

- **v1.0.0**: Initial Docker setup
- **v1.1.0**: Added Nginx reverse proxy
- **v1.2.0**: Added Redis caching
- **v1.3.0**: Enhanced security features

---

**Last Updated**: August 2025
**Version**: 1.3.0 