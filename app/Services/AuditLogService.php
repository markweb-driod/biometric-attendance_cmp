<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AuditLogService
{
    /**
     * Get audit logs for department
     */
    public function getAuditLogs($departmentId, $filters = [])
    {
        // Include page in cache key for proper pagination caching
        $cacheKey = "audit_logs_{$departmentId}_" . md5(serialize($filters) . '_' . ($filters['page'] ?? 1));
        
        return Cache::remember($cacheKey, 300, function() use ($departmentId, $filters) {
            $query = AuditLog::where('department_id', $departmentId);

            // Apply filters
            if (!empty($filters['action'])) {
                $query->where('action', $filters['action']);
            }

            if (!empty($filters['resource_type'])) {
                $query->where('resource_type', $filters['resource_type']);
            }

            if (!empty($filters['severity'])) {
                $query->where('severity', $filters['severity']);
            }

            if (!empty($filters['user_type'])) {
                $query->where('user_type', $filters['user_type']);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('description', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('action', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('resource_type', 'like', '%' . $filters['search'] . '%');
                });
            }

            // Apply sorting
            $sortBy = $filters['sort_by'] ?? 'created_at';
            $sortOrder = $filters['sort_order'] ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $filters['per_page'] ?? 8;
            $page = $filters['page'] ?? 1;
            
            return $query->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Get audit statistics
     */
    public function getAuditStats($departmentId, $filters = [])
    {
        $cacheKey = "audit_stats_{$departmentId}_" . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function() use ($departmentId, $filters) {
            $query = AuditLog::where('department_id', $departmentId);

            // Apply date filters
            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $total = $query->count();
            $critical = $query->where('severity', 'critical')->count();
            $high = $query->where('severity', 'high')->count();
            $medium = $query->where('severity', 'medium')->count();
            $low = $query->where('severity', 'low')->count();

            // Get action breakdown
            $actionBreakdown = $query->select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->get();

            // Get user type breakdown
            $userTypeBreakdown = $query->select('user_type', DB::raw('count(*) as count'))
                ->groupBy('user_type')
                ->orderBy('count', 'desc')
                ->get();

            // Get daily activity for the last 30 days
            $dailyActivity = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            return [
                'total' => $total,
                'critical' => $critical,
                'high' => $high,
                'medium' => $medium,
                'low' => $low,
                'action_breakdown' => $actionBreakdown,
                'user_type_breakdown' => $userTypeBreakdown,
                'daily_activity' => $dailyActivity
            ];
        });
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($departmentId, $limit = 10)
    {
        return AuditLog::where('department_id', $departmentId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get security alerts
     */
    public function getSecurityAlerts($departmentId, $filters = [])
    {
        $query = AuditLog::where('department_id', $departmentId)
            ->whereIn('severity', ['high', 'critical'])
            ->whereIn('action', ['login', 'logout', 'unauthorized_access', 'data_export', 'bulk_operation']);

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Get compliance report
     */
    public function getComplianceReport($departmentId, $filters = [])
    {
        $query = AuditLog::where('department_id', $departmentId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Data access compliance
        $dataAccess = $query->whereIn('action', ['view', 'export', 'download'])
            ->select('user_type', 'resource_type', DB::raw('count(*) as count'))
            ->groupBy('user_type', 'resource_type')
            ->get();

        // User activity compliance
        $userActivity = $query->select('user_type', DB::raw('count(*) as total_actions'))
            ->groupBy('user_type')
            ->get();

        // Security compliance
        $securityEvents = $query->whereIn('severity', ['high', 'critical'])
            ->select('action', 'severity', DB::raw('count(*) as count'))
            ->groupBy('action', 'severity')
            ->get();

        // Data modification compliance
        $dataModifications = $query->whereIn('action', ['create', 'update', 'delete'])
            ->select('resource_type', 'action', DB::raw('count(*) as count'))
            ->groupBy('resource_type', 'action')
            ->get();

        return [
            'data_access' => $dataAccess,
            'user_activity' => $userActivity,
            'security_events' => $securityEvents,
            'data_modifications' => $dataModifications,
            'compliance_score' => $this->calculateComplianceScore($departmentId, $filters)
        ];
    }

    /**
     * Calculate compliance score
     */
    private function calculateComplianceScore($departmentId, $filters = [])
    {
        $query = AuditLog::where('department_id', $departmentId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $totalEvents = $query->count();
        $criticalEvents = $query->where('severity', 'critical')->count();
        $highEvents = $query->where('severity', 'high')->count();

        if ($totalEvents == 0) {
            return 100; // Perfect score if no events
        }

        $penaltyScore = ($criticalEvents * 10) + ($highEvents * 5);
        $complianceScore = max(0, 100 - ($penaltyScore / $totalEvents * 100));

        return round($complianceScore, 2);
    }

    /**
     * Export audit data
     */
    public function exportAuditData($departmentId, $filters = [])
    {
        $logs = $this->getAuditLogs($departmentId, $filters);
        
        $exportData = [];
        foreach ($logs->items() as $log) {
            $exportData[] = [
                'Date' => $log->created_at->format('Y-m-d H:i:s'),
                'Action' => ucfirst($log->action),
                'Resource Type' => ucfirst($log->resource_type),
                'Resource ID' => $log->resource_id,
                'Description' => $log->description,
                'User Type' => ucfirst($log->user_type),
                'User ID' => $log->user_id,
                'Severity' => ucfirst($log->severity),
                'IP Address' => $log->ip_address,
                'User Agent' => $log->user_agent,
                'Session ID' => $log->session_id
            ];
        }

        return $exportData;
    }

    /**
     * Log audit event
     */
    public static function log($action, $resourceType, $resourceId, $description, $user, $severity = 'medium', $departmentId = null, $additionalData = [])
    {
        $auditLog = AuditLog::create([
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'user_type' => class_basename($user),
            'user_id' => $user->id,
            'severity' => $severity,
            'department_id' => $departmentId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'old_values' => $additionalData['old_values'] ?? null,
            'new_values' => $additionalData['new_values'] ?? null
        ]);

        return $auditLog;
    }

    /**
     * Get audit trail for specific resource
     */
    public function getResourceAuditTrail($resourceType, $resourceId, $departmentId = null)
    {
        $query = AuditLog::where('resource_type', $resourceType)
            ->where('resource_id', $resourceId);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary($userId, $userType, $departmentId = null, $days = 30)
    {
        $query = AuditLog::where('user_id', $userId)
            ->where('user_type', $userType)
            ->where('created_at', '>=', Carbon::now()->subDays($days));

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $totalActions = $query->count();
        $actionBreakdown = $query->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->get();

        $resourceBreakdown = $query->select('resource_type', DB::raw('count(*) as count'))
            ->groupBy('resource_type')
            ->get();

        $dailyActivity = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return [
            'total_actions' => $totalActions,
            'action_breakdown' => $actionBreakdown,
            'resource_breakdown' => $resourceBreakdown,
            'daily_activity' => $dailyActivity
        ];
    }
}











