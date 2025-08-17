@echo off
REM Docker Deployment Script for Biometric Attendance System (Windows)
REM This script automates the Docker deployment process on Windows

echo 🐳 Starting Docker Deployment for Biometric Attendance System
echo ==============================================================

REM Check if Docker is installed
docker --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Docker is not installed. Please install Docker Desktop for Windows first.
    pause
    exit /b 1
)

REM Check if Docker Compose is installed
docker-compose --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Docker Compose is not installed. Please install Docker Desktop for Windows first.
    pause
    exit /b 1
)

echo [INFO] Docker and Docker Compose check passed

REM Check if .env file exists
if not exist .env (
    echo [WARNING] .env file not found. Creating from env_docker.txt
    if exist env_docker.txt (
        copy env_docker.txt .env >nul
        echo [INFO] .env file created from env_docker.txt
    ) else (
        echo [ERROR] env_docker.txt file not found. Please create .env file manually.
        pause
        exit /b 1
    )
)

REM Create necessary directories
echo [STEP] Creating necessary directories...
if not exist nginx\ssl mkdir nginx\ssl
if not exist storage\logs mkdir storage\logs
if not exist database\init mkdir database\init

REM Generate self-signed SSL certificate for development
if not exist nginx\ssl\cert.pem (
    echo [STEP] Generating self-signed SSL certificate...
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout nginx\ssl\key.pem -out nginx\ssl\cert.pem -subj "/C=NG/ST=State/L=City/O=Organization/CN=localhost"
    echo [INFO] SSL certificate generated
)

REM Stop and remove existing containers
echo [STEP] Stopping existing containers...
docker-compose down --remove-orphans

REM Remove existing images (optional, for clean build)
set /p REBUILD="Do you want to rebuild all images? (y/N): "
if /i "%REBUILD%"=="y" (
    echo [STEP] Removing existing images...
    docker-compose down --rmi all --volumes --remove-orphans
)

REM Build and start containers
echo [STEP] Building and starting containers...
docker-compose up --build -d

REM Wait for services to be ready
echo [STEP] Waiting for services to be ready...
timeout /t 30 /nobreak >nul

REM Check container status
echo [STEP] Checking container status...
docker-compose ps

REM Run database migrations
echo [STEP] Running database migrations...
docker-compose exec app php artisan migrate --force

REM Run database seeders
echo [STEP] Running database seeders...
docker-compose exec app php artisan db:seed --force

REM Generate application key
echo [STEP] Generating application key...
docker-compose exec app php artisan key:generate --no-interaction

REM Optimize application
echo [STEP] Optimizing application for production...
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

REM Set proper permissions
echo [STEP] Setting proper permissions...
docker-compose exec app chown -R www-data:www-data storage/
docker-compose exec app chown -R www-data:www-data bootstrap/cache/
docker-compose exec app chmod -R 775 storage/
docker-compose exec app chmod -R 775 bootstrap/cache/

REM Health check
echo [STEP] Performing health check...
curl -f http://localhost/health >nul 2>&1
if %errorLevel% equ 0 (
    echo [INFO] Application is healthy and responding
) else (
    echo [WARNING] Health check failed, but containers are running
)

REM Create deployment info file
echo Docker Deployment Completed > docker_deployment_info.txt
echo ========================== >> docker_deployment_info.txt
echo Date: %date% %time% >> docker_deployment_info.txt
echo Docker Version: >> docker_deployment_info.txt
docker --version >> docker_deployment_info.txt
echo Docker Compose Version: >> docker_deployment_info.txt
docker-compose --version >> docker_deployment_info.txt
echo. >> docker_deployment_info.txt
echo Services: >> docker_deployment_info.txt
echo - App: http://localhost:8000 >> docker_deployment_info.txt
echo - Database: localhost:3306 >> docker_deployment_info.txt
echo - Redis: localhost:6379 >> docker_deployment_info.txt
echo - Nginx: http://localhost (redirects to https) >> docker_deployment_info.txt
echo - Nginx HTTPS: https://localhost >> docker_deployment_info.txt
echo. >> docker_deployment_info.txt
echo Container Names: >> docker_deployment_info.txt
echo - App: biometric-attendance-app >> docker_deployment_info.txt
echo - Database: biometric-attendance-db >> docker_deployment_info.txt
echo - Redis: biometric-attendance-redis >> docker_deployment_info.txt
echo - Nginx: biometric-attendance-nginx >> docker_deployment_info.txt
echo. >> docker_deployment_info.txt
echo Useful Commands: >> docker_deployment_info.txt
echo - View logs: docker-compose logs -f >> docker_deployment_info.txt
echo - Stop services: docker-compose down >> docker_deployment_info.txt
echo - Restart services: docker-compose restart >> docker_deployment_info.txt
echo - Access app container: docker-compose exec app bash >> docker_deployment_info.txt
echo - Access database: docker-compose exec db mysql -u attendance_user -p attendance_db >> docker_deployment_info.txt

echo [INFO] Deployment completed successfully! 🎉
echo [INFO] Check docker_deployment_info.txt for details and next steps.

echo.
echo ==============================================================
echo 🐳 Biometric Attendance System is now running in Docker!
echo ==============================================================
echo.
echo Access your application at:
echo   HTTP:  http://localhost:8000
echo   HTTPS: https://localhost
echo.
echo Useful commands:
echo   View logs: docker-compose logs -f
echo   Stop:      docker-compose down
echo   Restart:   docker-compose restart

pause 