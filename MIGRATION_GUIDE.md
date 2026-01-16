# SQLite to MySQL Migration Guide

This guide walks you through migrating the Biometric Attendance System from SQLite to MySQL.

## Prerequisites

- PHP 7.4+ with MySQL extensions (PDO MySQL)
- MySQL Server 5.7+ or MariaDB 10.2+
- MySQL database created (or credentials to create one)
- Existing SQLite database with data at `database/database.sqlite`
- Laravel application installed with all dependencies

## Step-by-Step Migration Process

### Step 1: Backup Your SQLite Database

Before starting the migration, create a backup of your SQLite database:

```bash
# On Linux/Mac
cp database/database.sqlite database/database.sqlite.backup

# On Windows
copy database\database.sqlite database\database.sqlite.backup
```

### Step 2: Create MySQL Database

Create a MySQL database for the application:

```sql
CREATE DATABASE biometric_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or using MySQL command line:

```bash
mysql -u root -p
CREATE DATABASE biometric_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 3: Configure Environment

1. Copy the example environment file if you don't have one:
   ```bash
   cp .env.example .env
   ```

2. Update your `.env` file with MySQL credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=biometric_attendance
   DB_USERNAME=root
   DB_PASSWORD=your_password
   DB_CHARSET=utf8mb4
   DB_COLLATION=utf8mb4_unicode_ci
   ```

3. Generate application key if needed:
   ```bash
   php artisan key:generate
   ```

### Step 4: Run Migrations on MySQL

Run Laravel migrations to create the database schema in MySQL:

```bash
php artisan migrate
```

This will create all the tables in your MySQL database based on the migration files.

### Step 5: Migrate Data from SQLite to MySQL

Use the custom artisan command to migrate all data:

```bash
php artisan migrate:sqlite-to-mysql
```

**Options:**
- `--force`: Force migration even if MySQL tables already have data (will truncate tables)
- `--skip-data`: Skip data migration, only run schema migration

**Example with force:**
```bash
php artisan migrate:sqlite-to-mysql --force
```

The command will:
- Connect to both SQLite and MySQL databases
- Migrate all tables in dependency order
- Preserve foreign key relationships
- Handle data type conversions (booleans, JSON, timestamps)
- Show progress and verify the migration

### Step 6: Verify Migration

The migration command automatically verifies the migration by:
- Comparing row counts between SQLite and MySQL
- Checking for any discrepancies
- Reporting any issues found

You can also manually verify:

```bash
# Check table counts in MySQL
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('students')->count()
>>> DB::table('attendances')->count()
```

### Step 7: Test the Application

1. **Test Login**: Verify that user authentication works
2. **Test Attendance**: Create a test attendance session
3. **Test Reports**: Generate reports to ensure data integrity
4. **Check Admin Panel**: Verify all admin functions work correctly

## Troubleshooting

### Issue: Connection Refused

**Error:** `SQLSTATE[HY000] [2002] Connection refused`

**Solution:**
- Verify MySQL server is running: `mysql -u root -p`
- Check DB_HOST in `.env` (use `127.0.0.1` instead of `localhost`)
- Verify firewall settings

### Issue: Access Denied

**Error:** `SQLSTATE[HY000] [1045] Access denied`

**Solution:**
- Verify DB_USERNAME and DB_PASSWORD in `.env`
- Ensure MySQL user has privileges: `GRANT ALL PRIVILEGES ON biometric_attendance.* TO 'user'@'localhost';`

### Issue: Table Already Exists

**Error:** `Table 'table_name' already exists`

**Solution:**
- Use `--force` flag: `php artisan migrate:sqlite-to-mysql --force`
- Or manually drop tables: `php artisan migrate:fresh` (WARNING: This deletes all data)

### Issue: Foreign Key Constraint Fails

**Error:** `SQLSTATE[23000]: Integrity constraint violation`

**Solution:**
- Ensure migrations run in correct order (already handled by migration command)
- Check that parent records exist before child records
- Review migration logs for specific table issues

### Issue: Data Type Mismatch

**Error:** `Data truncated for column`

**Solution:**
- The migration command handles most conversions automatically
- Check MySQL column definitions match expected types
- Review specific column in both databases

### Issue: Partial Index Not Supported

**Note:** MySQL doesn't support partial indexes with WHERE clauses. The migration automatically converts these to regular indexes. This is handled in the migration file.

## Rollback (If Needed)

If you need to rollback to SQLite:

1. Update `.env`:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```

2. Restore SQLite backup if needed:
   ```bash
   cp database/database.sqlite.backup database/database.sqlite
   ```

## Post-Migration Tasks

### 1. Update Configuration

The default database connection has been changed to MySQL in `config/database.php`. No action needed unless you want to revert.

### 2. Clear Caches

After migration, clear all caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Optimize Database

Optimize MySQL tables for better performance:

```bash
php artisan tinker
>>> DB::statement("OPTIMIZE TABLE users");
>>> DB::statement("OPTIMIZE TABLE students");
# ... repeat for other large tables
```

Or use the admin panel's database optimization feature.

### 4. Monitor Performance

Monitor MySQL performance and adjust configuration as needed:

```bash
# Check MySQL status
mysqladmin -u root -p status

# View slow queries
mysql -u root -p
SHOW VARIABLES LIKE 'slow_query_log';
```

## Important Notes

1. **Backup First**: Always backup both databases before migration
2. **Test Environment**: Test migration in development environment first
3. **Data Integrity**: Verify critical data after migration
4. **Performance**: MySQL may perform differently than SQLite - monitor and optimize
5. **Backup Strategy**: Update backup scripts to use MySQL backup methods

## Differences Between SQLite and MySQL

### Supported Features
- ✅ Foreign keys
- ✅ Indexes (partial indexes converted to regular indexes)
- ✅ JSON columns
- ✅ Boolean types (converted automatically)
- ✅ Timestamps

### Differences
- **Partial Indexes**: MySQL doesn't support WHERE clauses in indexes - converted to regular indexes
- **Case Sensitivity**: MySQL table/column names are case-sensitive on Linux, case-insensitive on Windows
- **Concurrent Access**: MySQL handles concurrent connections better than SQLite
- **Backup**: Use `mysqldump` instead of file copy

## Support

If you encounter issues during migration:

1. Check the migration command output for specific errors
2. Review Laravel logs: `storage/logs/laravel.log`
3. Verify database connections: `php artisan migrate:status`
4. Check MySQL error logs

## Migration Command Reference

```bash
# Standard migration
php artisan migrate:sqlite-to-mysql

# Force migration (overwrites existing data)
php artisan migrate:sqlite-to-mysql --force

# Skip data migration (schema only)
php artisan migrate:sqlite-to-mysql --skip-data

# Run Laravel migrations
php artisan migrate

# Fresh migrations (WARNING: Deletes all data)
php artisan migrate:fresh

# Check migration status
php artisan migrate:status
```

---

**Migration completed successfully!** Your application is now running on MySQL.

