<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\ApiKeyLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiAnalyticsService
{
    /**
     * Get usage statistics for an API key
     */
    public function getUsageStats(ApiKey $apiKey): array
    {
        $now = Carbon::now();

        // Today's stats
        $todayStart = $now->copy()->startOfDay();
        $todayCount = $apiKey->getUsageCount($todayStart, $now);
        $todaySuccessful = $apiKey->logs()
            ->where('created_at', '>=', $todayStart)
            ->successful()
            ->count();
        $todayFailed = $apiKey->logs()
            ->where('created_at', '>=', $todayStart)
            ->failed()
            ->count();

        // This week's stats
        $weekStart = $now->copy()->startOfWeek();
        $weekCount = $apiKey->getUsageCount($weekStart, $now);

        // This month's stats
        $monthStart = $now->copy()->startOfMonth();
        $monthCount = $apiKey->getUsageCount($monthStart, $now);

        // Total stats
        $totalCount = $apiKey->getTotalUsageCount();
        $totalSuccessful = $apiKey->logs()->successful()->count();
        $totalFailed = $apiKey->logs()->failed()->count();

        // Average response time
        $avgResponseTime = $apiKey->logs()
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        // Popular endpoints
        $popularEndpoints = $apiKey->logs()
            ->select('endpoint', DB::raw('COUNT(*) as count'))
            ->groupBy('endpoint')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($log) {
                return [
                    'endpoint' => $log->endpoint,
                    'count' => $log->count,
                ];
            });

        // Usage by hour (last 24 hours)
        $hourlyUsage = [];
        for ($i = 23; $i >= 0; $i--) {
            $hourStart = $now->copy()->subHours($i)->startOfHour();
            $hourEnd = $hourStart->copy()->endOfHour();
            $count = $apiKey->getUsageCount($hourStart, $hourEnd);
            $hourlyUsage[] = [
                'hour' => $hourStart->format('H:00'),
                'timestamp' => $hourStart->toIso8601String(),
                'count' => $count,
            ];
        }

        // Usage by day (last 30 days)
        $dailyUsage = [];
        for ($i = 29; $i >= 0; $i--) {
            $dayStart = $now->copy()->subDays($i)->startOfDay();
            $dayEnd = $dayStart->copy()->endOfDay();
            $count = $apiKey->getUsageCount($dayStart, $dayEnd);
            $dailyUsage[] = [
                'date' => $dayStart->format('Y-m-d'),
                'timestamp' => $dayStart->toIso8601String(),
                'count' => $count,
            ];
        }

        // Error rate
        $errorRate = $totalCount > 0 ? ($totalFailed / $totalCount) * 100 : 0;

        return [
            'today' => [
                'total' => $todayCount,
                'successful' => $todaySuccessful,
                'failed' => $todayFailed,
            ],
            'this_week' => [
                'total' => $weekCount,
            ],
            'this_month' => [
                'total' => $monthCount,
            ],
            'total' => [
                'total' => $totalCount,
                'successful' => $totalSuccessful,
                'failed' => $totalFailed,
                'success_rate' => $totalCount > 0 ? round(($totalSuccessful / $totalCount) * 100, 2) : 0,
                'error_rate' => round($errorRate, 2),
            ],
            'performance' => [
                'avg_response_time_ms' => round($avgResponseTime ?? 0, 2),
            ],
            'popular_endpoints' => $popularEndpoints,
            'hourly_usage' => $hourlyUsage,
            'daily_usage' => $dailyUsage,
            'last_used_at' => $apiKey->last_used_at?->toIso8601String(),
        ];
    }

    /**
     * Generate analytics report
     */
    public function generateReport(ApiKey $apiKey, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $logs = $apiKey->logs()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalRequests = $logs->count();
        $successfulRequests = $logs->where('response_status', '>=', 200)
            ->where('response_status', '<', 300)
            ->count();
        $failedRequests = $logs->where('response_status', '>=', 400)->count();

        $avgResponseTime = $logs->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        $endpoints = $logs->groupBy('endpoint')
            ->map(function($endpointLogs) {
                return [
                    'count' => $endpointLogs->count(),
                    'avg_response_time' => $endpointLogs->whereNotNull('response_time_ms')->avg('response_time_ms'),
                ];
            });

        return [
            'period' => [
                'start' => $startDate->toIso8601String(),
                'end' => $endDate->toIso8601String(),
            ],
            'summary' => [
                'total_requests' => $totalRequests,
                'successful_requests' => $successfulRequests,
                'failed_requests' => $failedRequests,
                'success_rate' => $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 2) : 0,
                'avg_response_time_ms' => round($avgResponseTime ?? 0, 2),
            ],
            'endpoints' => $endpoints,
            'generated_at' => now()->toIso8601String(),
        ];
    }
}

