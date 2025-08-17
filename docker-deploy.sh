#!/bin/bash

# Docker Deployment Script for Biometric Attendance System
# This script automates the Docker deployment process

set -e

echo "🐳 Starting Docker Deployment for Biometric Attendance System"
echo "=============================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

print_status "Docker and Docker Compose check passed"

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning ".env file not found. Creating from env_docker.txt"
    if [ -f env_docker.txt ]; then
        cp env_docker.txt .env
        print_status ".env file created from env_docker.txt"
    else
        print_error "env_docker.txt file not found. Please create .env file manually."
        exit 1
    fi
fi

# Create necessary directories
print_step "Creating necessary directories..."
mkdir -p nginx/ssl
mkdir -p storage/logs
mkdir -p database/init

# Generate self-signed SSL certificate for development
if [ ! -f nginx/ssl/cert.pem ] || [ ! -f nginx/ssl/key.pem ]; then
    print_step "Generating self-signed SSL certificate..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout nginx/ssl/key.pem \
        -out nginx/ssl/cert.pem \
        -subj "/C=NG/ST=State/L=City/O=Organization/CN=localhost"
    print_status "SSL certificate generated"
fi

# Stop and remove existing containers
print_step "Stopping existing containers..."
docker-compose down --remove-orphans

# Remove existing images (optional, for clean build)
read -p "Do you want to rebuild all images? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_step "Removing existing images..."
    docker-compose down --rmi all --volumes --remove-orphans
fi

# Build and start containers
print_step "Building and starting containers..."
docker-compose up --build -d

# Wait for services to be ready
print_step "Waiting for services to be ready..."
sleep 30

# Check container status
print_step "Checking container status..."
docker-compose ps

# Run database migrations
print_step "Running database migrations..."
docker-compose exec app php artisan migrate --force

# Run database seeders
print_step "Running database seeders..."
docker-compose exec app php artisan db:seed --force

# Generate application key
print_step "Generating application key..."
docker-compose exec app php artisan key:generate --no-interaction

# Optimize application
print_step "Optimizing application for production..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set proper permissions
print_step "Setting proper permissions..."
docker-compose exec app chown -R www-data:www-data storage/
docker-compose exec app chown -R www-data:www-data bootstrap/cache/
docker-compose exec app chmod -R 775 storage/
docker-compose exec app chmod -R 775 bootstrap/cache/

# Health check
print_step "Performing health check..."
if curl -f http://localhost/health > /dev/null 2>&1; then
    print_status "Application is healthy and responding"
else
    print_warning "Health check failed, but containers are running"
fi

# Create deployment info file
cat > docker_deployment_info.txt << EOF
Docker Deployment Completed
==========================
Date: $(date)
Docker Version: $(docker --version)
Docker Compose Version: $(docker-compose --version)

Services:
- App: http://localhost:8000
- Database: localhost:3306
- Redis: localhost:6379
- Nginx: http://localhost (redirects to https)
- Nginx HTTPS: https://localhost

Container Names:
- App: biometric-attendance-app
- Database: biometric-attendance-db
- Redis: biometric-attendance-redis
- Nginx: biometric-attendance-nginx

Useful Commands:
- View logs: docker-compose logs -f
- Stop services: docker-compose down
- Restart services: docker-compose restart
- Access app container: docker-compose exec app bash
- Access database: docker-compose exec db mysql -u attendance_user -p attendance_db

Next Steps:
1. Update your domain in .env file
2. Replace self-signed SSL with real certificate
3. Configure your reverse proxy if needed
4. Set up monitoring and logging
5. Configure backups

For support, check the documentation or contact your system administrator
EOF

print_status "Deployment completed successfully! 🎉"
print_status "Check docker_deployment_info.txt for details and next steps."

echo ""
echo "=============================================================="
echo "🐳 Biometric Attendance System is now running in Docker!"
echo "=============================================================="
echo ""
echo "Access your application at:"
echo "  HTTP:  http://localhost:8000"
echo "  HTTPS: https://localhost"
echo ""
echo "Useful commands:"
echo "  View logs: docker-compose logs -f"
echo "  Stop:      docker-compose down"
echo "  Restart:   docker-compose restart" 