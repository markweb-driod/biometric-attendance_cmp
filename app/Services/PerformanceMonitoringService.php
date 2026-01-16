<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringService
{
    /**
     * Monitor attendance capture performance
     */
    public function monitorAttendanceCapture($startTime, $endTime, $success = true, $error = null)
    {
        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $metrics = [
            'timestamp' => now(),
            'duration_ms' => $duration,
            'success' => $success,
            'error' => $error,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        // Log slow operations
        if ($duration > 2000) { // More than 2 seconds
            Log::warning('Slow attendance capture', $metrics);
        }
        
        // Store metrics for analysis
        $this->storeMetrics('attendance_capture', $metrics);
        
        return $metrics;
    }
    
    /**
     * Monitor page load performance
     */
    public function monitorPageLoad($page, $startTime, $endTime, $queries = 0)
    {
        $duration = ($endTime - $startTime) * 1000;
        
        $metrics = [
            'page' => $page,
            'timestamp' => now(),
            'duration_ms' => $duration,
            'queries' => $queries,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        // Log slow pages
        if ($duration > 1000) { // More than 1 second
            Log::warning('Slow page load', $metrics);
        }
        
        $this->storeMetrics('page_load', $metrics);
        
        return $metrics;
    }
    
    /**
     * Monitor database query performance
     */
    public function monitorQuery($query, $duration, $bindings = [])
    {
        $metrics = [
            'timestamp' => now(),
            'query' => $query,
            'duration_ms' => $duration * 1000,
            'bindings' => $bindings
        ];
        
        // Log slow queries
        if ($duration > 0.5) { // More than 500ms
            Log::warning('Slow database query', $metrics);
        }
        
        $this->storeMetrics('database_query', $metrics);
        
        return $metrics;
    }
    
    /**
     * Store performance metrics
     */
    private function storeMetrics($type, $metrics)
    {
        $key = "performance_metrics_{$type}_" . now()->format('Y-m-d-H');
        
        $existing = Cache::get($key, []);
        $existing[] = $metrics;
        
        // Keep only last 100 metrics per hour
        if (count($existing) > 100) {
            $existing = array_slice($existing, -100);
        }
        
        Cache::put($key, $existing, 3600); // Store for 1 hour
    }
    
    /**
     * Get performance statistics
     */
    public function getPerformanceStats($type = null, $hours = 24)
    {
        $stats = [];
        $now = now();
        
        for ($i = 0; $i < $hours; $i++) {
            $hour = $now->copy()->subHours($i);
            $key = "performance_metrics_{$type}_{$hour->format('Y-m-d-H')}";
            
            $metrics = Cache::get($key, []);
            
            if (!empty($metrics)) {
                $stats[] = [
                    'hour' => $hour->format('Y-m-d H:00'),
                    'count' => count($metrics),
                    'avg_duration' => $this->calculateAverage($metrics, 'duration_ms'),
                    'max_duration' => $this->calculateMax($metrics, 'duration_ms'),
                    'success_rate' => $this->calculateSuccessRate($metrics)
                ];
            }
        }
        
        return array_reverse($stats);
    }
    
    /**
     * Get system health metrics
     */
    public function getSystemHealth()
    {
        try {
            // Database performance
            $dbStart = microtime(true);
            DB::select('SELECT 1');
            $dbDuration = (microtime(true) - $dbStart) * 1000;
            
            // Memory usage
            $memoryUsage = memory_get_usage(true);
            $peakMemory = memory_get_peak_usage(true);
            $memoryLimit = ini_get('memory_limit');
            
            // Storage space
            $storagePath = storage_path();
            $totalSpace = disk_total_space($storagePath);
            $freeSpace = disk_free_space($storagePath);
            $usagePercentage = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);
            
            // Cache performance
            $cacheStart = microtime(true);
            Cache::put('health_check', 'ok', 60);
            $cacheDuration = (microtime(true) - $cacheStart) * 1000;
            
            return [
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'database' => [
                    'status' => 'connected',
                    'response_time_ms' => round($dbDuration, 2)
                ],
                'memory' => [
                    'current_usage' => $this->formatBytes($memoryUsage),
                    'peak_usage' => $this->formatBytes($peakMemory),
                    'limit' => $memoryLimit,
                    'usage_percentage' => round(($memoryUsage / $this->parseMemoryLimit($memoryLimit)) * 100, 2)
                ],
                'storage' => [
                    'total_space' => $this->formatBytes($totalSpace),
                    'free_space' => $this->formatBytes($freeSpace),
                    'usage_percentage' => $usagePercentage,
                    'status' => $usagePercentage > 90 ? 'critical' : ($usagePercentage > 80 ? 'warning' : 'healthy')
                ],
                'cache' => [
                    'status' => 'operational',
                    'response_time_ms' => round($cacheDuration, 2)
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Calculate average from metrics array
     */
    private function calculateAverage($metrics, $field)
    {
        if (empty($metrics)) return 0;
        
        $sum = array_sum(array_column($metrics, $field));
        return round($sum / count($metrics), 2);
    }
    
    /**
     * Calculate maximum from metrics array
     */
    private function calculateMax($metrics, $field)
    {
        if (empty($metrics)) return 0;
        
        return max(array_column($metrics, $field));
    }
    
    /**
     * Calculate success rate from metrics array
     */
    private function calculateSuccessRate($metrics)
    {
        if (empty($metrics)) return 0;
        
        $successful = array_filter($metrics, function($metric) {
            return isset($metric['success']) && $metric['success'];
        });
        
        return round((count($successful) / count($metrics)) * 100, 2);
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
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;
        
        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }
        
        return $memoryLimit;
    }
    
    /**
     * Clear old performance metrics
     */
    public function clearOldMetrics($days = 7)
    {
        $keys = Cache::get('performance_metrics_keys', []);
        $cutoff = now()->subDays($days);
        
        foreach ($keys as $key) {
            if (strpos($key, 'performance_metrics_') === 0) {
                $timestamp = Cache::get($key . '_timestamp');
                if ($timestamp && $timestamp < $cutoff) {
                    Cache::forget($key);
                }
            }
        }
    }
}
