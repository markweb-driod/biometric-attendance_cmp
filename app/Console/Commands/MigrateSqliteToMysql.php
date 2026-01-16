<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class MigrateSqliteToMysql extends Command
{
    protected $signature = 'migrate:sqlite-to-mysql 
                            {--force : Force migration even if MySQL tables already exist}
                            {--skip-data : Skip data migration, only run schema migration}';
    
    protected $description = 'Migrate data from SQLite to MySQL database';

    private $sqliteConnection;
    private $mysqlConnection;
    private $tablesOrder = [
        'users',
        'departments',
        'academic_levels',
        'courses',
        'semesters',
        'students',
        'lecturers',
        'superadmins',
        'classrooms',
        'attendance_sessions',
        'attendances',
        'class_student',
        'system_settings',
        'sessions',
        'hods',
        'exam_eligibilities',
        'audit_logs',
        'api_keys',
        'api_key_logs',
        'lecturer_course',
        'course_department',
        'migrations',
        'cache',
        'cache_locks',
    ];

    public function handle()
    {
        $this->info('=== SQLite to MySQL Migration ===');
        $this->newLine();

        // Check if SQLite database exists
        $sqlitePath = database_path('database.sqlite');
        if (!file_exists($sqlitePath)) {
            $this->error('SQLite database file not found: ' . $sqlitePath);
            return 1;
        }

        // Setup connections
        try {
            $this->setupConnections();
        } catch (\Exception $e) {
            $this->error('Failed to setup database connections: ' . $e->getMessage());
            return 1;
        }

        // Check if MySQL database is empty (unless --force is used)
        if (!$this->option('force') && !$this->option('skip-data')) {
            if ($this->mysqlHasData()) {
                if (!$this->confirm('MySQL database already has data. Do you want to proceed?', false)) {
                    $this->info('Migration cancelled.');
                    return 0;
                }
            }
        }

        // Get list of tables from SQLite
        $tables = $this->getSqliteTables();
        
        if (empty($tables)) {
            $this->warn('No tables found in SQLite database.');
            return 0;
        }

        $this->info('Found ' . count($tables) . ' tables in SQLite database.');
        $this->newLine();

        // Migrate data if not skipping
        if (!$this->option('skip-data')) {
            $this->migrateData($tables);
        } else {
            $this->info('Skipping data migration as requested.');
        }

        // Verify migration
        $this->verifyMigration($tables);

        $this->newLine();
        $this->info('=== Migration Complete ===');
        
        return 0;
    }

    private function setupConnections()
    {
        // Setup SQLite connection
        Config::set('database.connections.sqlite_temp', [
            'driver' => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $this->sqliteConnection = DB::connection('sqlite_temp');

        // Setup MySQL connection (using default mysql connection)
        $this->mysqlConnection = DB::connection('mysql');

        // Test connections
        $this->sqliteConnection->getPdo();
        $this->mysqlConnection->getPdo();

        $this->info('✓ Database connections established');
    }

    private function getSqliteTables()
    {
        $tables = $this->sqliteConnection->select(
            "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        );

        return array_map(function($table) {
            return $table->name;
        }, $tables);
    }

    private function mysqlHasData()
    {
        try {
            $tables = $this->mysqlConnection->select('SHOW TABLES');
            return !empty($tables);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function migrateData($tables)
    {
        $this->info('Starting data migration...');
        $this->newLine();

        // Sort tables to respect foreign key dependencies
        $sortedTables = $this->sortTablesByDependency($tables);

        $progressBar = $this->output->createProgressBar(count($sortedTables));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $progressBar->start();

        $totalRecords = 0;

        foreach ($sortedTables as $table) {
            $progressBar->setMessage("Migrating table: {$table}");
            
            try {
                // Check if table exists in MySQL
                $tableExists = $this->tableExistsInMysql($table);
                
                if (!$tableExists) {
                    $progressBar->setMessage("Skipping {$table} (table not found in MySQL)");
                    $progressBar->advance();
                    continue;
                }

                // Disable foreign key checks temporarily
                $this->mysqlConnection->statement('SET FOREIGN_KEY_CHECKS=0');

                // Get data from SQLite
                $records = $this->sqliteConnection->table($table)->get();

                if ($records->isEmpty()) {
                    $progressBar->setMessage("Skipping {$table} (no data)");
                    $progressBar->advance();
                    $this->mysqlConnection->statement('SET FOREIGN_KEY_CHECKS=1');
                    continue;
                }

                // Clear existing data if force option is used
                if ($this->option('force')) {
                    $this->mysqlConnection->table($table)->truncate();
                } else {
                    // Check if data already exists
                    $existingCount = $this->mysqlConnection->table($table)->count();
                    if ($existingCount > 0) {
                        $progressBar->setMessage("Skipping {$table} (already has data)");
                        $progressBar->advance();
                        $this->mysqlConnection->statement('SET FOREIGN_KEY_CHECKS=1');
                        continue;
                    }
                }

                // Insert data in chunks to avoid memory issues
                $chunks = $records->chunk(100);
                foreach ($chunks as $chunk) {
                    $data = $this->prepareDataForInsert($chunk->toArray());
                    
                    // Handle special cases for certain tables
                    if ($table === 'migrations') {
                        // Merge migrations instead of replacing
                        $this->mergeMigrations($data);
                    } else {
                        $this->mysqlConnection->table($table)->insert($data);
                    }
                }

                $recordCount = $records->count();
                $totalRecords += $recordCount;
                $progressBar->setMessage("Migrated {$table} ({$recordCount} records)");

                // Re-enable foreign key checks
                $this->mysqlConnection->statement('SET FOREIGN_KEY_CHECKS=1');

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error migrating table {$table}: " . $e->getMessage());
                $progressBar->setMessage("Failed: {$table}");
            }

            $progressBar->advance();
        }

        $progressBar->setMessage("Total: {$totalRecords} records migrated");
        $progressBar->finish();
        $this->newLine(2);
        $this->info("✓ Migrated {$totalRecords} total records");
    }

    private function sortTablesByDependency($tables)
    {
        $sorted = [];
        $remaining = array_flip($tables);

        // Add tables in dependency order
        foreach ($this->tablesOrder as $table) {
            if (isset($remaining[$table])) {
                $sorted[] = $table;
                unset($remaining[$table]);
            }
        }

        // Add any remaining tables
        foreach (array_keys($remaining) as $table) {
            $sorted[] = $table;
        }

        return $sorted;
    }

    private function tableExistsInMysql($table)
    {
        try {
            $result = $this->mysqlConnection->select(
                "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
                [$table]
            );
            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function prepareDataForInsert($data)
    {
        $prepared = [];

        foreach ($data as $row) {
            $record = [];
            
            foreach ((array)$row as $key => $value) {
                // Handle NULL values
                if ($value === null) {
                    $record[$key] = null;
                }
                // Handle boolean values (SQLite stores as 0/1, MySQL needs proper boolean)
                elseif (is_bool($value) || in_array($key, ['is_active', 'is_public', 'face_registration_enabled'])) {
                    $record[$key] = $value ? 1 : 0;
                }
                // Handle JSON columns
                elseif (is_string($value) && (in_array($key, ['old_values', 'new_values', 'setting_value']) || 
                        (strpos($value, '{') === 0 || strpos($value, '[') === 0))) {
                    // Try to decode to verify it's JSON
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $record[$key] = $value; // Keep as JSON string
                    } else {
                        $record[$key] = $value;
                    }
                }
                // Handle timestamps
                elseif (in_array($key, ['created_at', 'updated_at', 'email_verified_at', 'captured_at', 'validated_at', 'overridden_at'])) {
                    $record[$key] = $value ?: null;
                }
                // Default: keep as is
                else {
                    $record[$key] = $value;
                }
            }

            $prepared[] = $record;
        }

        return $prepared;
    }

    private function mergeMigrations($migrationData)
    {
        foreach ($migrationData as $migration) {
            // Check if migration already exists
            $exists = $this->mysqlConnection->table('migrations')
                ->where('migration', $migration['migration'] ?? null)
                ->exists();

            if (!$exists && isset($migration['migration'])) {
                $this->mysqlConnection->table('migrations')->insert([
                    'migration' => $migration['migration'],
                    'batch' => $migration['batch'] ?? 1,
                ]);
            }
        }
    }

    private function verifyMigration($tables)
    {
        $this->newLine();
        $this->info('Verifying migration...');
        
        $verificationErrors = [];
        $totalSqliteRecords = 0;
        $totalMysqlRecords = 0;

        foreach ($tables as $table) {
            try {
                if (!$this->tableExistsInMysql($table)) {
                    continue;
                }

                $sqliteCount = $this->sqliteConnection->table($table)->count();
                $mysqlCount = $this->mysqlConnection->table($table)->count();

                $totalSqliteRecords += $sqliteCount;
                $totalMysqlRecords += $mysqlCount;

                if ($sqliteCount !== $mysqlCount && $table !== 'migrations') {
                    $verificationErrors[] = [
                        'table' => $table,
                        'sqlite' => $sqliteCount,
                        'mysql' => $mysqlCount,
                    ];
                }
            } catch (\Exception $e) {
                $verificationErrors[] = [
                    'table' => $table,
                    'error' => $e->getMessage(),
                ];
            }
        }

        if (empty($verificationErrors)) {
            $this->info("✓ Verification passed: {$totalMysqlRecords} records match SQLite ({$totalSqliteRecords})");
        } else {
            $this->warn('⚠ Some discrepancies found:');
            foreach ($verificationErrors as $error) {
                if (isset($error['error'])) {
                    $this->line("  {$error['table']}: {$error['error']}");
                } else {
                    $this->line("  {$error['table']}: SQLite={$error['sqlite']}, MySQL={$error['mysql']}");
                }
            }
        }
    }
}

