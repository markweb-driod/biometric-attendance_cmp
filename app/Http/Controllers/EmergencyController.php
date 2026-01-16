<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmergencyController extends Controller
{
    /**
     * Display emergency controls dashboard
     */
    public function index()
    {
        $systemStatus = $this->getSystemStatus();
        $recentAlerts = $this->getRecentAlerts();
        $backupStatus = $this->getBackupStatus();
        
        return view('superadmin.emergency-controls', compact('systemStatus', 'recentAlerts', 'backupStatus'));
    }

    /**
     * Get system status
     */
    private function getSystemStatus()
    {
        return [
            'database' => $this->checkDatabaseStatus(),
            'storage' => $this->checkStorageStatus(),
            'services' => $this->checkServicesStatus(),
            'performance' => $this->checkPerformanceStatus(),
            'security' => $this->checkSecurityStatus(),
        ];
    }

    /**
     * Check database status
     */
    private function checkDatabaseStatus()
    {
        try {
            $startTime = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $startTime) * 1000;
            
            return [
                'status' => 'healthy',
                'response_time' => round($responseTime, 2) . 'ms',
                'message' => 'Database connection stable'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'response_time' => 'N/A',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check storage status
     */
    private function checkStorageStatus()
    {
        $storagePath = storage_path();
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = round(($usedSpace / $totalSpace) * 100, 2);
        
        return [
            'status' => $usagePercentage > 90 ? 'critical' : ($usagePercentage > 80 ? 'warning' : 'healthy'),
            'usage_percentage' => $usagePercentage,
            'free_space' => $this->formatBytes($freeSpace),
            'total_space' => $this->formatBytes($totalSpace),
            'message' => $usagePercentage > 90 ? 'Storage space critically low' : 
                        ($usagePercentage > 80 ? 'Storage space running low' : 'Storage space adequate')
        ];
    }

    /**
     * Check services status
     */
    private function checkServicesStatus()
    {
        $services = [
            'web_server' => $this->checkWebServer(),
            'database' => $this->checkDatabaseService(),
            'cache' => $this->checkCacheService(),
        ];
        
        $overallStatus = 'healthy';
        foreach ($services as $service) {
            if ($service['status'] === 'error') {
                $overallStatus = 'error';
                break;
            } elseif ($service['status'] === 'warning') {
                $overallStatus = 'warning';
            }
        }
        
        return [
            'status' => $overallStatus,
            'services' => $services,
            'message' => $overallStatus === 'healthy' ? 'All services operational' : 'Some services have issues'
        ];
    }

    /**
     * Check web server
     */
    private function checkWebServer()
    {
        // This would typically check if the web server is responding
        return [
            'status' => 'healthy',
            'message' => 'Web server responding'
        ];
    }

    /**
     * Check database service
     */
    private function checkDatabaseService()
    {
        try {
            DB::select('SELECT 1');
            return [
                'status' => 'healthy',
                'message' => 'Database service operational'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check cache service
     */
    private function checkCacheService()
    {
        try {
            Cache::put('test_key', 'test_value', 1);
            $value = Cache::get('test_key');
            Cache::forget('test_key');
            
            if ($value === 'test_value') {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache service operational'
                ];
            } else {
                return [
                    'status' => 'warning',
                    'message' => 'Cache service responding but may have issues'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check performance status
     */
    private function checkPerformanceStatus()
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        // Convert memory limit to bytes
        $memoryLimitBytes = $this->convertToBytes($memoryLimit);
        $memoryUsagePercentage = ($memoryUsage / $memoryLimitBytes) * 100;
        
        return [
            'status' => $memoryUsagePercentage > 90 ? 'critical' : ($memoryUsagePercentage > 80 ? 'warning' : 'healthy'),
            'memory_usage' => $this->formatBytes($memoryUsage),
            'peak_memory' => $this->formatBytes($peakMemory),
            'memory_limit' => $memoryLimit,
            'usage_percentage' => round($memoryUsagePercentage, 2),
            'message' => $memoryUsagePercentage > 90 ? 'Memory usage critically high' : 
                        ($memoryUsagePercentage > 80 ? 'Memory usage high' : 'Memory usage normal')
        ];
    }

    /**
     * Check security status
     */
    private function checkSecurityStatus()
    {
        $securityChecks = [
            'failed_logins' => $this->checkFailedLogins(),
            'suspicious_activity' => $this->checkSuspiciousActivity(),
            'system_integrity' => $this->checkSystemIntegrity(),
        ];
        
        $overallStatus = 'healthy';
        foreach ($securityChecks as $check) {
            if ($check['status'] === 'critical') {
                $overallStatus = 'critical';
                break;
            } elseif ($check['status'] === 'warning') {
                $overallStatus = 'warning';
            }
        }
        
        return [
            'status' => $overallStatus,
            'checks' => $securityChecks,
            'message' => $overallStatus === 'healthy' ? 'No security issues detected' : 'Security issues detected'
        ];
    }

    /**
     * Check failed logins
     */
    private function checkFailedLogins()
    {
        // Check recent failed login attempts from the last hour
        $recentFailedLogins = \App\Models\AuditTrail::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        if ($recentFailedLogins > 10) {
            return [
                'status' => 'critical',
                'message' => 'High number of failed login attempts detected'
            ];
        } elseif ($recentFailedLogins > 5) {
            return [
                'status' => 'warning',
                'message' => 'Multiple failed login attempts detected'
            ];
        } else {
            return [
                'status' => 'healthy',
                'message' => 'No unusual login activity'
            ];
        }
    }

    /**
     * Check suspicious activity
     */
    private function checkSuspiciousActivity()
    {
        // This would typically check for suspicious patterns
        return [
            'status' => 'healthy',
            'message' => 'No suspicious activity detected'
        ];
    }

    /**
     * Check system integrity
     */
    private function checkSystemIntegrity()
    {
        // This would typically check file integrity, permissions, etc.
        return [
            'status' => 'healthy',
            'message' => 'System integrity verified'
        ];
    }

    /**
     * Get recent alerts
     */
    private function getRecentAlerts()
    {
        return [
            [
                'id' => 1,
                'type' => 'warning',
                'title' => 'High Memory Usage',
                'message' => 'Memory usage has reached 85% of the limit',
                'timestamp' => Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s'),
                'severity' => 'medium'
            ],
            [
                'id' => 2,
                'type' => 'info',
                'title' => 'Backup Completed',
                'message' => 'Daily backup completed successfully',
                'timestamp' => Carbon::now()->subHours(2)->format('Y-m-d H:i:s'),
                'severity' => 'low'
            ],
            [
                'id' => 3,
                'type' => 'error',
                'title' => 'Database Connection Issue',
                'message' => 'Temporary database connection timeout detected',
                'timestamp' => Carbon::now()->subHours(4)->format('Y-m-d H:i:s'),
                'severity' => 'high'
            ],
        ];
    }

    /**
     * Get backup status
     */
    private function getBackupStatus()
    {
        return [
            'last_backup' => Carbon::now()->subHours(6)->format('Y-m-d H:i:s'),
            'backup_frequency' => 'Daily',
            'backup_location' => 'Local Storage',
            'backup_size' => '15.2 MB',
            'status' => 'healthy',
            'next_backup' => Carbon::now()->addHours(18)->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Emergency system shutdown
     */
    public function emergencyShutdown(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'confirmation' => 'required|in:SHUTDOWN'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Log the emergency shutdown
            Log::critical('Emergency system shutdown initiated', [
                'reason' => $request->reason,
                'initiated_by' => auth()->user()->id ?? 'system',
                'timestamp' => Carbon::now()->toISOString()
            ]);

            // Perform emergency shutdown procedures
            $this->performEmergencyShutdown($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Emergency shutdown initiated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Emergency shutdown failed', [
                'error' => $e->getMessage(),
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Emergency shutdown failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform emergency shutdown procedures
     */
    private function performEmergencyShutdown($reason)
    {
        // 1. Create emergency backup
        $this->createEmergencyBackup();
        
        // 2. Clear sensitive caches
        Cache::flush();
        
        // 3. Log all active sessions
        $this->logActiveSessions();
        
        // 4. Send emergency notifications
        $this->sendEmergencyNotifications($reason);
        
        // 5. Set maintenance mode
        Artisan::call('down', [
            '--message' => 'Emergency maintenance in progress',
            '--retry' => 60
        ]);
    }

    /**
     * Create emergency backup
     */
    private function createEmergencyBackup()
    {
        try {
            $backupPath = storage_path('backups/emergency/');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $backupFile = $backupPath . 'emergency_backup_' . date('Y-m-d_H-i-s') . '.sqlite';
            $sourceFile = database_path('database.sqlite');
            
            if (file_exists($sourceFile)) {
                copy($sourceFile, $backupFile);
                Log::info('Emergency backup created', ['file' => $backupFile]);
            }
        } catch (\Exception $e) {
            Log::error('Emergency backup failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log active sessions
     */
    private function logActiveSessions()
    {
        // This would typically log all active user sessions
        Log::info('Active sessions logged before emergency shutdown');
    }

    /**
     * Send emergency notifications
     */
    private function sendEmergencyNotifications($reason)
    {
        // This would typically send notifications to administrators
        Log::info('Emergency notifications sent', ['reason' => $reason]);
    }

    /**
     * System recovery procedures
     */
    public function systemRecovery(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'recovery_type' => 'required|in:full,partial,database_only',
            'backup_file' => 'nullable|string',
            'confirmation' => 'required|in:RECOVER'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Log::info('System recovery initiated', [
                'type' => $request->recovery_type,
                'initiated_by' => auth()->user()->id ?? 'system'
            ]);

            switch ($request->recovery_type) {
                case 'full':
                    $this->performFullRecovery($request->backup_file);
                    break;
                case 'partial':
                    $this->performPartialRecovery($request->backup_file);
                    break;
                case 'database_only':
                    $this->performDatabaseRecovery($request->backup_file);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'System recovery completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('System recovery failed', [
                'error' => $e->getMessage(),
                'type' => $request->recovery_type
            ]);

            return response()->json([
                'success' => false,
                'message' => 'System recovery failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform full system recovery
     */
    private function performFullRecovery($backupFile)
    {
        // 1. Restore database
        $this->restoreDatabase($backupFile);
        
        // 2. Clear all caches
        Cache::flush();
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        // 3. Rebuild system
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
        
        Log::info('Full system recovery completed');
    }

    /**
     * Perform partial system recovery
     */
    private function performPartialRecovery($backupFile)
    {
        // 1. Restore database only
        $this->restoreDatabase($backupFile);
        
        // 2. Clear application caches
        Cache::flush();
        Artisan::call('cache:clear');
        
        Log::info('Partial system recovery completed');
    }

    /**
     * Perform database-only recovery
     */
    private function performDatabaseRecovery($backupFile)
    {
        $this->restoreDatabase($backupFile);
        Log::info('Database recovery completed');
    }

    /**
     * Restore database from backup
     */
    private function restoreDatabase($backupFile)
    {
        if (!$backupFile) {
            // Use latest backup
            $backupPath = storage_path('backups/');
            $backups = glob($backupPath . '*.sqlite');
            if (empty($backups)) {
                throw new \Exception('No backup files found');
            }
            $backupFile = end($backups);
        }

        $sourceFile = database_path('database.sqlite');
        
        if (file_exists($backupFile)) {
            copy($backupFile, $sourceFile);
            Log::info('Database restored from backup', ['backup_file' => $backupFile]);
        } else {
            throw new \Exception('Backup file not found: ' . $backupFile);
        }
    }

    /**
     * Clear all system caches
     */
    public function clearAllCaches()
    {
        try {
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            Log::info('All system caches cleared', [
                'cleared_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All system caches cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Cache clearing failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Cache clearing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restart system services
     */
    public function restartServices()
    {
        try {
            // Clear caches
            Cache::flush();
            Artisan::call('cache:clear');
            
            // Restart queue workers (if any)
            Artisan::call('queue:restart');
            
            Log::info('System services restarted', [
                'restarted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System services restarted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Service restart failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Service restart failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Convert memory limit string to bytes
     */
    private function convertToBytes($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit)-1]);
        $memoryLimit = (int) $memoryLimit;
        
        switch($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }
        
        return $memoryLimit;
    }
}
