#!/bin/bash

# Oracle Database Deployment Script for Biometric Attendance System
# This script automates the deployment process on Oracle

set -e

echo "🚀 Starting Oracle Database Deployment for Biometric Attendance System"
echo "================================================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root"
   exit 1
fi

# Check PHP version
PHP_VERSION=$(php -v | head -n1 | cut -d " " -f2 | cut -d "." -f1,2)
if [[ $(echo "$PHP_VERSION >= 8.2" | bc -l) -eq 0 ]]; then
    print_error "PHP 8.2+ is required. Current version: $PHP_VERSION"
    exit 1
fi
print_status "PHP version check passed: $PHP_VERSION"

# Check if Oracle extensions are installed
if ! php -m | grep -q oci8; then
    print_error "OCI8 extension is not installed. Please install it first."
    print_status "Run: sudo apt-get install php8.2-oci8 (Ubuntu/Debian)"
    print_status "Or: sudo yum install php-oci8 (CentOS/RHEL)"
    exit 1
fi

if ! php -m | grep -q pdo_oci; then
    print_error "PDO_OCI extension is not installed. Please install it first."
    print_status "Run: sudo apt-get install php8.2-pdo-oci (Ubuntu/Debian)"
    print_status "Or: sudo yum install php-pdo-oci (CentOS/RHEL)"
    exit 1
fi
print_status "Oracle extensions check passed"

# Check Composer
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install it first."
    exit 1
fi
print_status "Composer check passed"

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning ".env file not found. Creating from .env.example"
    if [ -f .env.example ]; then
        cp .env.example .env
        print_status ".env file created from .env.example"
    else
        print_error ".env.example file not found. Please create .env file manually."
        exit 1
    fi
fi

# Install/Update dependencies
print_status "Installing/Updating Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install Oracle package if not already installed
if ! composer show | grep -q "yajra/laravel-oci8"; then
    print_status "Installing Laravel OCI8 package..."
    composer require yajra/laravel-oci8
fi

# Publish Oracle configuration
print_status "Publishing Oracle configuration..."
php artisan vendor:publish --provider="Yajra\Oci8\Oci8ServiceProvider" --force

# Clear caches
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check database connection
print_status "Testing database connection..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connection successful';"; then
    print_status "Database connection successful"
else
    print_error "Database connection failed. Please check your .env configuration."
    print_status "Make sure your Oracle database is running and accessible."
    exit 1
fi

# Run migrations
print_status "Running database migrations..."
if php artisan migrate --force; then
    print_status "Migrations completed successfully"
else
    print_error "Migrations failed. Please check the error messages above."
    exit 1
fi

# Run seeders
print_status "Running database seeders..."
if php artisan db:seed --force; then
    print_status "Database seeding completed successfully"
else
    print_warning "Database seeding failed. You may need to run seeders manually."
fi

# Set proper permissions
print_status "Setting storage and cache permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating application key..."
    php artisan key:generate
fi

# Optimize application
print_status "Optimizing application for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check if web server is configured
print_status "Checking web server configuration..."
if command -v apache2 &> /dev/null; then
    print_status "Apache detected. Make sure mod_rewrite is enabled."
    print_status "Document root should point to: $(pwd)/public"
elif command -v nginx &> /dev/null; then
    print_status "Nginx detected. Make sure configuration points to: $(pwd)/public"
else
    print_warning "No web server detected. You may need to configure one manually."
fi

# Create deployment info file
cat > deployment_info.txt << EOF
Oracle Database Deployment Completed
==================================
Date: $(date)
PHP Version: $PHP_VERSION
Laravel Version: $(php artisan --version | cut -d " " -f3)
Database: Oracle
Connection: $(grep DB_CONNECTION .env | cut -d "=" -f2)
Host: $(grep DB_HOST .env | cut -d "=" -f2)
Port: $(grep DB_PORT .env | cut -d "=" -f2)
Service: $(grep DB_SERVICE_NAME .env | cut -d "=" -f2)

Next Steps:
1. Configure your web server (Apache/Nginx)
2. Set up SSL certificate for HTTPS
3. Configure firewall rules
4. Set up monitoring and logging
5. Create database backups
6. Test all functionality

For support, check the ORACLE_DEPLOYMENT_GUIDE.md file
EOF

print_status "Deployment completed successfully! 🎉"
print_status "Check deployment_info.txt for details and next steps."
print_status "Review ORACLE_DEPLOYMENT_GUIDE.md for comprehensive information."

echo ""
echo "================================================================"
echo "🚀 Biometric Attendance System is now deployed on Oracle!"
echo "================================================================" 