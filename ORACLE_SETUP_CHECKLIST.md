# Oracle Setup Checklist for Biometric Attendance System

## Pre-Deployment Checklist

### ✅ Oracle Database Installation
- [ ] Oracle Database 19c/21c installed and running
- [ ] Oracle service name configured (default: XE)
- [ ] Oracle listener running on port 1521
- [ ] Database user created with proper privileges
- [ ] Tablespace configured with sufficient space

### ✅ Oracle Instant Client
- [ ] Oracle Instant Client installed
- [ ] Environment variables set (ORACLE_HOME, LD_LIBRARY_PATH)
- [ ] Client libraries accessible to PHP

### ✅ PHP Extensions
- [ ] PHP 8.2+ installed
- [ ] OCI8 extension installed and enabled
- [ ] PDO_OCI extension installed and enabled
- [ ] Required PHP extensions: mbstring, xml, curl, gd, zip

### ✅ Web Server
- [ ] Apache/Nginx installed and configured
- [ ] PHP-FPM or mod_php configured
- [ ] Document root pointing to /public directory
- [ ] mod_rewrite enabled (Apache) or proper Nginx config

### ✅ Application Files
- [ ] Laravel application files uploaded
- [ ] Proper file permissions set (storage/, bootstrap/cache/)
- [ ] .env file configured with Oracle settings
- [ ] Composer dependencies installed

## Deployment Steps

### 1. Database Setup
```sql
-- Connect as SYSTEM user
CONNECT system/password@localhost:1521/XE

-- Create application user
CREATE USER biometric_user IDENTIFIED BY your_password;
GRANT CONNECT, RESOURCE TO biometric_user;
GRANT CREATE SESSION TO biometric_user;
GRANT CREATE TABLE TO biometric_user;
GRANT CREATE SEQUENCE TO biometric_user;
GRANT CREATE VIEW TO biometric_user;
GRANT CREATE PROCEDURE TO biometric_user;
GRANT CREATE TRIGGER TO biometric_user;
GRANT UNLIMITED TABLESPACE TO biometric_user;
GRANT SELECT ANY DICTIONARY TO biometric_user;
GRANT SELECT ANY TABLE TO biometric_user;
```

### 2. Environment Configuration
```bash
# Copy environment file
cp env_oracle_example.txt .env

# Edit .env file with your Oracle settings
nano .env
```

### 3. Install Dependencies
```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install Oracle package
composer require yajra/laravel-oci8

# Publish Oracle configuration
php artisan vendor:publish --provider="Yajra\Oci8\Oci8ServiceProvider"
```

### 4. Run Migrations
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Success';"

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force
```

### 5. Optimize Application
```bash
# Generate application key
php artisan key:generate

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## Post-Deployment Verification

### ✅ Database Verification
- [ ] All tables created successfully
- [ ] Foreign key constraints working
- [ ] Indexes created for performance
- [ ] Sample data inserted correctly

### ✅ Application Verification
- [ ] Homepage loads without errors
- [ ] Database connection working
- [ ] User authentication functional
- [ ] File uploads working
- [ ] Face recognition features working

### ✅ Performance Verification
- [ ] Page load times acceptable
- [ ] Database queries optimized
- [ ] Caching working properly
- [ ] No memory leaks

### ✅ Security Verification
- [ ] HTTPS configured (production)
- [ ] File permissions secure
- [ ] Environment variables protected
- [ ] SQL injection protection working

## Troubleshooting Common Issues

### Database Connection Issues
```bash
# Check Oracle listener
lsnrctl status

# Test connection with SQL*Plus
sqlplus username/password@//localhost:1521/XE

# Check PHP extensions
php -m | grep oci
```

### Migration Issues
```bash
# Check migration status
php artisan migrate:status

# Rollback and retry
php artisan migrate:rollback
php artisan migrate --force

# Check for Oracle-specific errors
tail -f storage/logs/laravel.log
```

### Performance Issues
```sql
-- Check table sizes
SELECT table_name, bytes/1024/1024 MB 
FROM user_segments 
WHERE segment_type = 'TABLE';

-- Check index usage
SELECT index_name, table_name, status 
FROM user_indexes;
```

## Support Resources

- [Oracle Database Documentation](https://docs.oracle.com/en/database/)
- [Laravel OCI8 Package](https://github.com/yajra/laravel-oci8)
- [PHP OCI8 Documentation](https://www.php.net/manual/en/book.oci8.php)
- [Oracle Community Forums](https://community.oracle.com/)

## Emergency Contacts

- System Administrator: [Contact Info]
- Database Administrator: [Contact Info]
- Application Developer: [Contact Info]

---

**Last Updated:** [Date]
**Deployed By:** [Name]
**Version:** [Application Version] 