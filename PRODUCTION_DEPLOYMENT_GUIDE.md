# Production Server Deployment Guide
## Biometric Attendance System

This guide outlines the steps to deploy the Biometric Attendance System to a production server (Ubuntu 22.04 LTS / Windows Server).

### 1. Server Prerequisites
Ensure your production server meets these requirements:
- **OS**: Ubuntu 22.04 LTS or Windows Server 2019+
- **Web Server**: Nginx (Recommended) or Apache
- **PHP**: Version 8.2+
- **Database**: Oracle Database 19c/21c (See `ORACLE_DEPLOYMENT_GUIDE.md`)
- **Git**: Installed
- **Composer**: Installed

### 2. Database Setup (Critical)

#### A. Required Environment Variables
Add these to your `.env` file for the Oracle connection:

```env
DB_CONNECTION=oracle
DB_HOST=127.0.0.1 (or your Oracle server IP)
DB_PORT=1521
DB_DATABASE=XE
DB_SERVICE_NAME=XE
DB_USERNAME=biometric_user
DB_PASSWORD=strong_password_here
DB_CHARSET=AL32UTF8
```

#### B. Create Database User (Oracle)
Run these SQL commands as the `SYSTEM` or `SYS` user using SQL*Plus or SQL Developer to create the application schema:

```sql
-- 1. Create User
CREATE USER biometric_user IDENTIFIED BY "strong_password_here";

-- 2. Grant Privileges
GRANT CONNECT, RESOURCE TO biometric_user;
GRANT CREATE SESSION TO biometric_user;
GRANT CREATE TABLE TO biometric_user;
GRANT CREATE VIEW TO biometric_user;
GRANT CREATE SEQUENCE TO biometric_user;
GRANT CREATE PROCEDURE TO biometric_user;
GRANT UNLIMITED TABLESPACE TO biometric_user;

-- 3. Additional Perms for Framework
GRANT SELECT ANY DICTIONARY TO biometric_user;
```

For platform-specific installation (installing OCI8 drivers on Linux/Windows), please refer to **[ORACLE_DEPLOYMENT_GUIDE.md](ORACLE_DEPLOYMENT_GUIDE.md)**.

### 3. Application Deployment

#### A. Clone Repository
```bash
cd /var/www/html
git clone https://github.com/your-repo/biometric-attendance.git
cd biometric-attendance
```

#### B. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

#### C. Environment Configuration
```bash
cp .env.example .env
nano .env
```
Update the following settings in `.env`:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- **Database**: (See Oracle Guide for credentials)

#### D. Key Generation & Storage
```bash
php artisan key:generate
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/html/biometric-attendance
```

### 4. Web Server Configuration (Nginx Example)

Create `/etc/nginx/sites-available/biometric-attendance`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/biometric-attendance/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
ln -s /etc/nginx/sites-available/biometric-attendance /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

### 5. Finalize Installation
Run migrations and optimizations:
```bash
php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

### 6. Queue Worker (Supervisor)
Install Supervisor to keep the queue worker running:
```bash
apt-get install supervisor
nano /etc/supervisor/conf.d/biometric-worker.conf
```
Content:
```ini
[program:biometric-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/biometric-attendance/artisan queue:work sqs --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/biometric-attendance/storage/logs/worker.log
```
Start supervisor:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start biometric-worker:*
```
## 6. Troubleshooting

### Assets Loading from Localhost (Connection Refused)
If your app tries to load styles/scripts from `localhost:5174` on the production server, it means a `hot` file exists.
**Fix:** Run this on the server:
```bash
rm public/hot
```

### Geolocation Errors (Secure Origin)
If you see `GeolocationPositionError: Only secure origins are allowed`:
**Cause:** The browser blocks Geolocation API on insecure HTTP connections (except localhost).
**Fix:** You **MUST** serve your application over **HTTPS**.
- Using **IP Address**: You cannot easily get a valid SSL certificate for a raw IP.
- Using **Domain**: Use Let's Encrypt (Certbot) to enable HTTPS.
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```
