# Oracle Database Deployment Guide for Biometric Attendance System

## Prerequisites

### 1. Oracle Database Requirements
- Oracle Database 19c or 21c (XE, Standard, or Enterprise Edition)
- Oracle Instant Client (for PHP connection)
- Minimum 2GB RAM for XE, 4GB+ for Standard/Enterprise

### 2. PHP Requirements
- PHP 8.2+ with Oracle extensions
- Required PHP extensions:
  - `oci8` (Oracle Database Driver)
  - `pdo_oci` (PDO Oracle Driver)
  - `mbstring`
  - `xml`
  - `curl`
  - `gd` (for image processing)
  - `zip` (for Excel operations)

### 3. Server Requirements
- Linux/Windows Server with Oracle support
- Apache/Nginx web server
- Composer for PHP dependencies

## Installation Steps

### Step 1: Install Oracle Database

#### Option A: Oracle XE (Free for Development)
```bash
# Download Oracle XE from Oracle website
# Install following Oracle documentation
# Default credentials: system/password
# Default port: 1521
# Default service: XE
```

#### Option B: Oracle Standard/Enterprise
```bash
# Follow Oracle installation guide for your OS
# Configure service name and port
```

### Step 2: Install Oracle Instant Client

#### Ubuntu/Debian:
```bash
# Download Oracle Instant Client from Oracle website
sudo apt-get install libaio1
sudo dpkg -i oracle-instantclient-basic_*.deb
sudo dpkg -i oracle-instantclient-devel_*.deb

# Set environment variables
echo 'export ORACLE_HOME=/usr/lib/oracle/21/client64' >> ~/.bashrc
echo 'export LD_LIBRARY_PATH=$ORACLE_HOME/lib:$LD_LIBRARY_PATH' >> ~/.bashrc
echo 'export PATH=$ORACLE_HOME/bin:$PATH' >> ~/.bashrc
source ~/.bashrc
```

#### CentOS/RHEL:
```bash
# Download Oracle Instant Client from Oracle website
sudo yum install libaio
sudo rpm -ivh oracle-instantclient-basic-*.rpm
sudo rpm -ivh oracle-instantclient-devel-*.rpm

# Set environment variables
echo 'export ORACLE_HOME=/usr/lib/oracle/21/client64' >> ~/.bashrc
echo 'export LD_LIBRARY_PATH=$ORACLE_HOME/lib:$LD_LIBRARY_PATH' >> ~/.bashrc
echo 'export PATH=$ORACLE_HOME/bin:$PATH' >> ~/.bashrc
source ~/.bashrc
```

#### Windows:
```bash
# Download Oracle Instant Client from Oracle website
# Extract to C:\oracle\instantclient_21_x
# Add C:\oracle\instantclient_21_x to PATH environment variable
```

### Step 3: Install PHP Oracle Extensions

#### Ubuntu/Debian:
```bash
sudo apt-get install php8.2-oci8 php8.2-pdo-oci
sudo systemctl restart apache2
```

#### CentOS/RHEL:
```bash
sudo yum install php-oci8 php-pdo-oci
sudo systemctl restart httpd
```

#### Windows:
```bash
# Download pre-compiled extensions from PECL
# Or use XAMPP/WAMP with Oracle extensions
```

### Step 4: Configure Laravel Application

#### 1. Update .env file:
```env
DB_CONNECTION=oracle
DB_HOST=localhost
DB_PORT=1521
DB_DATABASE=XE
DB_SERVICE_NAME=XE
DB_USERNAME=your_username
DB_PASSWORD=your_password
DB_CHARSET=AL32UTF8
DB_TNS=
```

#### 2. Install Laravel Oracle package:
```bash
composer require yajra/laravel-oci8
```

#### 3. Publish configuration:
```bash
php artisan vendor:publish --provider="Yajra\Oci8\Oci8ServiceProvider"
```

### Step 5: Create Oracle Database User

```sql
-- Connect as SYSTEM user
CONNECT system/password@localhost:1521/XE

-- Create new user for the application
CREATE USER biometric_user IDENTIFIED BY your_password;

-- Grant necessary privileges
GRANT CONNECT, RESOURCE TO biometric_user;
GRANT CREATE SESSION TO biometric_user;
GRANT CREATE TABLE TO biometric_user;
GRANT CREATE SEQUENCE TO biometric_user;
GRANT CREATE VIEW TO biometric_user;
GRANT CREATE PROCEDURE TO biometric_user;
GRANT CREATE TRIGGER TO biometric_user;
GRANT UNLIMITED TABLESPACE TO biometric_user;

-- Grant additional privileges for Laravel
GRANT SELECT ANY DICTIONARY TO biometric_user;
GRANT SELECT ANY TABLE TO biometric_user;
```

### Step 6: Run Migrations

```bash
# Clear any cached configurations
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate --force

# If you encounter issues, run with verbose output
php artisan migrate --force -v
```

### Step 7: Seed Database

```bash
# Run seeders
php artisan db:seed

# Or run specific seeders
php artisan db:seed --class=SuperadminSeeder
php artisan db:seed --class=LecturerSeeder
php artisan db:seed --class=SampleDataSeeder
```

## Configuration Files

### 1. Oracle Service Configuration (tnsnames.ora)
```ora
XE =
  (DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = XE)
    )
  )
```

### 2. PHP Configuration (php.ini)
```ini
extension=oci8
extension=pdo_oci

[OCI8]
oci8.privileged_connect = Off
oci8.max_persistent = -1
oci8.persistent_timeout = -1
oci8.ping_interval = 60
oci8.connection_class = ""
oci8.events = Off
oci8.statement_cache_size = 20
oci8.default_prefetch = 100
oci8.old_oci_close_semantics = Off
```

## Troubleshooting

### Common Issues and Solutions

#### 1. OCI8 Extension Not Found
```bash
# Check if extension is loaded
php -m | grep oci

# Install extension
sudo apt-get install php8.2-oci8
```

#### 2. Connection Refused
```bash
# Check Oracle listener status
lsnrctl status

# Start listener if stopped
lsnrctl start
```

#### 3. TNS: Could not resolve service name
```bash
# Check tnsnames.ora file
# Verify service name in .env
# Test connection with SQL*Plus
sqlplus username/password@//localhost:1521/XE
```

#### 4. Migration Errors
```bash
# Check Oracle user privileges
# Verify table creation permissions
# Check for reserved words in table/column names
```

#### 5. Character Set Issues
```bash
# Set proper charset in .env
DB_CHARSET=AL32UTF8

# Check database charset
SELECT value$ FROM sys.props$ WHERE name = 'NLS_CHARACTERSET';
```

## Performance Optimization

### 1. Database Configuration
```sql
-- Set memory parameters
ALTER SYSTEM SET memory_target = 1G SCOPE = SPFILE;
ALTER SYSTEM SET sga_target = 512M SCOPE = SPFILE;
ALTER SYSTEM SET pga_aggregate_target = 256M SCOPE = SPFILE;

-- Restart database
SHUTDOWN IMMEDIATE;
STARTUP;
```

### 2. Laravel Configuration
```php
// config/database.php - Oracle connection
'options' => [
    PDO::ATTR_CASE => PDO::CASE_LOWER,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
    PDO::ATTR_STRINGIFY_FETCHES => false,
    PDO::ATTR_PERSISTENT => true, // For connection pooling
],
```

### 3. Indexing Strategy
```sql
-- Create composite indexes for common queries
CREATE INDEX idx_attendance_student_class ON attendances(student_id, classroom_id);
CREATE INDEX idx_attendance_time_status ON attendances(captured_at, status);
CREATE INDEX idx_student_level_dept ON students(academic_level, department);
```

## Security Considerations

### 1. Database Security
```sql
-- Create application-specific user with minimal privileges
-- Use strong passwords
-- Enable Oracle audit logging
-- Regular security patches
```

### 2. Application Security
```bash
# Use HTTPS in production
# Implement proper authentication
# Validate all inputs
# Use prepared statements
# Regular security updates
```

## Monitoring and Maintenance

### 1. Database Monitoring
```sql
-- Check table sizes
SELECT table_name, bytes/1024/1024 MB 
FROM user_segments 
WHERE segment_type = 'TABLE';

-- Check index usage
SELECT index_name, table_name, status 
FROM user_indexes;

-- Monitor performance
SELECT sql_id, executions, elapsed_time/1000000 seconds 
FROM v$sql 
WHERE executions > 0 
ORDER BY elapsed_time DESC;
```

### 2. Application Monitoring
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Monitor application performance
php artisan queue:work --verbose

# Check storage usage
du -sh storage/
```

## Backup and Recovery

### 1. Database Backup
```bash
# Export schema and data
expdp biometric_user/password@XE directory=DATA_PUMP_DIR dumpfile=backup.dmp

# Import if needed
impdp biometric_user/password@XE directory=DATA_PUMP_DIR dumpfile=backup.dmp
```

### 2. Application Backup
```bash
# Backup application files
tar -czf app_backup.tar.gz /path/to/laravel/app

# Backup storage
tar -czf storage_backup.tar.gz /path/to/laravel/storage
```

## Support and Resources

- [Oracle Database Documentation](https://docs.oracle.com/en/database/)
- [Laravel OCI8 Package](https://github.com/yajra/laravel-oci8)
- [PHP OCI8 Documentation](https://www.php.net/manual/en/book.oci8.php)
- [Oracle Community Forums](https://community.oracle.com/)

## Contact

For deployment support or issues, contact your system administrator or Oracle support team. 