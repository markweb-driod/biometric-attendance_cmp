@echo off
REM Oracle Database Deployment Script for Biometric Attendance System (Windows)
REM This script automates the deployment process on Oracle for Windows

echo 🚀 Starting Oracle Database Deployment for Biometric Attendance System
echo ================================================================

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [ERROR] This script should not be run as administrator
    pause
    exit /b 1
)

REM Check PHP v
for /f "tokens=2 delims= " %%i in ('php -v 2^>nul ^| findstr "PHP"') do (
    set PHP_VERSION=%%i
    goto :php_check_done
)
echo [ERROR] PHP is not installed or not in PATH
pause
exit /b 1

:php_check_done
echo [INFO] PHP version check passed: %PHP_VERSION%

REM Check if Oracle extensions are installed
php -m | findstr "oci8" >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] OCI8 extension is not installed. Please install it first.
    echo [INFO] Download from: https://pecl.php.net/package/oci8
    pause
    exit /b 1
)

php -m | findstr "pdo_oci" >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] PDO_OCI extension is not installed. Please install it first.
    echo [INFO] Download from: https://pecl.php.net/package/pdo_oci
    pause
    exit /b 1
)
echo [INFO] Oracle extensions check passed

REM Check Composer
composer --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Composer is not installed. Please install it first.
    echo [INFO] Download from: https://getcomposer.org/
    pause
    exit /b 1
)
echo [INFO] Composer check passed

REM Check if .env file exists
if not exist .env (
    echo [WARNING] .env file not found. Creating from .env.example
    if exist .env.example (
        copy .env.example .env >nul
        echo [INFO] .env file created from .env.example
    ) else (
        echo [ERROR] .env.example file not found. Please create .env file manually.
        pause
        exit /b 1
    )
)

REM Install/Update dependencies
echo [INFO] Installing/Updating Composer dependencies...
composer install --no-dev --optimize-autoloader

REM Install Oracle package if not already installed
composer show | findstr "yajra/laravel-oci8" >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Installing Laravel OCI8 package...
    composer require yajra/laravel-oci8
)

REM Publish Oracle configuration
echo [INFO] Publishing Oracle configuration...
php artisan vendor:publish --provider="Yajra\Oci8\Oci8ServiceProvider" --force

REM Clear caches
echo [INFO] Clearing application caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Check database connection
echo [INFO] Testing database connection...
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connection successful';" >nul 2>&1
if %errorLevel% equ 0 (
    echo [INFO] Database connection successful
) else (
    echo [ERROR] Database connection failed. Please check your .env configuration.
    echo [INFO] Make sure your Oracle database is running and accessible.
    pause
    exit /b 1
)

REM Run migrations
echo [INFO] Running database migrations...
php artisan migrate --force
if %errorLevel% equ 0 (
    echo [INFO] Migrations completed successfully
) else (
    echo [ERROR] Migrations failed. Please check the error messages above.
    pause
    exit /b 1
)

REM Run seeders
echo [INFO] Running database seeders...
php artisan db:seed --force
if %errorLevel% equ 0 (
    echo [INFO] Database seeding completed successfully
) else (
    echo [WARNING] Database seeding failed. You may need to run seeders manually.
)

REM Set proper permissions (Windows equivalent)
echo [INFO] Setting storage and cache permissions...
icacls storage /grant Everyone:F /T >nul 2>&1
icacls bootstrap\cache /grant Everyone:F /T >nul 2>&1

REM Generate application key if not set
findstr "APP_KEY=base64:" .env >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Generating application key...
    php artisan key:generate
)

REM Optimize application
echo [INFO] Optimizing application for production...
php artisan config:cache
php artisan route:cache
php artisan view:cache

REM Check if web server is configured
echo [INFO] Checking web server configuration...
if exist "C:\xampp\apache\bin\httpd.exe" (
    echo [INFO] XAMPP detected. Make sure Apache is running and mod_rewrite is enabled.
    echo [INFO] Document root should point to: %CD%\public
) else if exist "C:\wamp64\bin\apache\apache2.4.9\bin\httpd.exe" (
    echo [INFO] WAMP detected. Make sure Apache is running and mod_rewrite is enabled.
    echo [INFO] Document root should point to: %CD%\public
) else (
    echo [WARNING] No web server detected. You may need to configure one manually.
    echo [INFO] Consider using XAMPP, WAMP, or IIS with PHP support.
)

REM Create deployment info file
echo Oracle Database Deployment Completed > deployment_info.txt
echo ================================== >> deployment_info.txt
echo Date: %date% %time% >> deployment_info.txt
echo PHP Version: %PHP_VERSION% >> deployment_info.txt
for /f "tokens=3 delims= " %%i in ('php artisan --version 2^>nul') do (
    echo Laravel Version: %%i >> deployment_info.txt
    goto :laravel_version_done
)
:laravel_version_done
echo Database: Oracle >> deployment_info.txt
echo Connection: Oracle >> deployment_info.txt
echo. >> deployment_info.txt
echo Next Steps: >> deployment_info.txt
echo 1. Configure your web server (Apache/Nginx/IIS) >> deployment_info.txt
echo 2. Set up SSL certificate for HTTPS >> deployment_info.txt
echo 3. Configure Windows Firewall rules >> deployment_info.txt
echo 4. Set up monitoring and logging >> deployment_info.txt
echo 5. Create database backups >> deployment_info.txt
echo 6. Test all functionality >> deployment_info.txt
echo. >> deployment_info.txt
echo For support, check the ORACLE_DEPLOYMENT_GUIDE.md file >> deployment_info.txt

echo [INFO] Deployment completed successfully! 🎉
echo [INFO] Check deployment_info.txt for details and next steps.
echo [INFO] Review ORACLE_DEPLOYMENT_GUIDE.md for comprehensive information.

echo.
echo ================================================================
echo 🚀 Biometric Attendance System is now deployed on Oracle!
echo ================================================================

pause 