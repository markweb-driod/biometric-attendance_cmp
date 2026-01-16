<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use App\Models\Department;

class SuperadminAuditController extends Controller
{
    protected $auditLogService;

    public function __construct()
    {
        $this->middleware('auth:superadmin');
        $this->auditLogService = new AuditLogService();
    }

    /**
     * Display system-wide audit logs dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        
        // Get system-wide audit logs (no department filter)
        $auditLogs = $this->getSystemAuditLogs($filters);
        $auditStats = $this->getSystemAuditStats($filters);
        $recentActivities = $this->getSystemRecentActivities();
        $securityAlerts = $this->getSystemSecurityAlerts($filters);
        $activityCharts = $this->getActivityChartsData($filters);
        $userActivitySummary = $this->getUserActivitySummary($filters);
        $departmentActivitySummary = $this->getDepartmentActivitySummary($filters);
        
        // Get filter options
        $filterOptions = $this->getFilterOptions();
        
        return view('superadmin.audit.index', compact(
            'auditLogs',
            'auditStats',
            'recentActivities',
            'securityAlerts',
            'activityCharts',
            'userActivitySummary',
            'departmentActivitySummary',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * Get system-wide audit logs (no department restriction)
     */
    private function getSystemAuditLogs($filters = [])
    {
        $query = AuditLog::with('department:id,name');

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

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
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
                  ->orWhere('resource_type', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('ip_address', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 7);
    }

    /**
     * Get system-wide audit statistics
     */
    private function getSystemAuditStats($filters = [])
    {
        $query = AuditLog::query();

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        $total = $query->count();
        $critical = (clone $query)->where('severity', 'critical')->count();
        $high = (clone $query)->where('severity', 'high')->count();
        $medium = (clone $query)->where('severity', 'medium')->count();
        $low = (clone $query)->where('severity', 'low')->count();

        // Get action breakdown
        $actionBreakdown = (clone $query)->select('action', \DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Get user type breakdown
        $userTypeBreakdown = (clone $query)->select('user_type', \DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->orderBy('count', 'desc')
            ->get();

        // Get hourly activity for the last 24 hours
        $driver = \DB::connection()->getDriverName();
        $hourExpression = $driver === 'sqlite' 
            ? "CAST(strftime('%H', created_at) AS INTEGER) as hour"
            : 'HOUR(created_at) as hour';
        $hourlyActivity = (clone $query)->select(\DB::raw($hourExpression), \DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        // Get daily activity for the last 30 days
        $dateExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at) as date"
            : 'DATE(created_at) as date';
        $dailyActivity = (clone $query)->select(\DB::raw($dateExpression), \DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
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
            'hourly_activity' => $hourlyActivity,
            'daily_activity' => $dailyActivity
        ];
    }

    /**
     * Get system-wide recent activities
     */
    private function getSystemRecentActivities($limit = 20)
    {
        return AuditLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get system-wide security alerts
     */
    private function getSystemSecurityAlerts($filters = [])
    {
        $query = AuditLog::whereIn('severity', ['high', 'critical'])
            ->whereIn('action', ['login', 'logout', 'unauthorized_access', 'data_export', 'bulk_operation', 'password_change', 'role_change']);

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    /**
     * Get activity charts data
     */
    private function getActivityChartsData($filters = [])
    {
        $query = AuditLog::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Activity by hour (last 24 hours)
        $driver = \DB::connection()->getDriverName();
        $hourExpression = $driver === 'sqlite' 
            ? "CAST(strftime('%H', created_at) AS INTEGER) as hour"
            : 'HOUR(created_at) as hour';
        $hourly = (clone $query)->select(\DB::raw($hourExpression), \DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->keyBy('hour');

        // Activity by day (last 30 days)
        $dateExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at) as date"
            : 'DATE(created_at) as date';
        $daily = (clone $query)->select(\DB::raw($dateExpression), \DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Activity by user type
        $byUserType = (clone $query)->select('user_type', \DB::raw('count(*) as count'))
            ->whereNotNull('user_type')
            ->groupBy('user_type')
            ->orderBy('count', 'desc')
            ->get();

        // Activity by action
        $byAction = (clone $query)->select('action', \DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Activity by severity
        $bySeverity = (clone $query)->select('severity', \DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'hourly' => $hourly,
            'daily' => $daily,
            'by_user_type' => $byUserType,
            'by_action' => $byAction,
            'by_severity' => $bySeverity
        ];
    }

    /**
     * Get user activity summary
     */
    private function getUserActivitySummary($filters = [])
    {
        $query = AuditLog::whereNotNull('user_id')->whereNotNull('user_type');

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->select('user_type', 'user_id', \DB::raw('count(*) as activity_count'))
            ->groupBy('user_type', 'user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Get department activity summary
     */
    private function getDepartmentActivitySummary($filters = [])
    {
        $query = AuditLog::whereNotNull('department_id');

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $results = $query->select('department_id', \DB::raw('count(*) as activity_count'))
            ->groupBy('department_id')
            ->orderBy('activity_count', 'desc')
            ->get();
        
        // Load department relationships separately
        foreach ($results as $result) {
            if ($result->department_id) {
                $result->department = \App\Models\Department::find($result->department_id);
            }
        }
        
        return $results;
    }

    /**
     * Get audit logs via API
     */
    public function getAuditLogs(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $logs = $this->getSystemAuditLogs($filters);
        
        return response()->json($logs);
    }

    /**
     * Get audit statistics via API
     */
    public function getAuditStats(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $stats = $this->getSystemAuditStats($filters);
        
        return response()->json($stats);
    }

    /**
     * Get activity charts data via API
     */
    public function getActivityCharts(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $charts = $this->getActivityChartsData($filters);
        
        return response()->json($charts);
    }

    /**
     * Export audit data
     */
    public function exportData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $logs = $this->getSystemAuditLogs(array_merge($filters, ['per_page' => 10000]));
        
        $exportData = [];
        foreach ($logs->items() as $log) {
            $exportData[] = [
                'Date' => $log->created_at->format('Y-m-d H:i:s'),
                'Action' => ucfirst($log->action),
                'Resource Type' => ucfirst($log->resource_type ?? 'N/A'),
                'Resource ID' => $log->resource_id ?? 'N/A',
                'Description' => $log->description,
                'User Type' => ucfirst($log->user_type ?? 'N/A'),
                'User ID' => $log->user_id ?? 'N/A',
                'Department' => $log->department ? $log->department->name : 'N/A',
                'Severity' => ucfirst($log->severity),
                'IP Address' => $log->ip_address,
                'User Agent' => $log->user_agent,
                'Session ID' => $log->session_id
            ];
        }

        return response()->json($exportData);
    }

    /**
     * Get filters from request
     */
    private function getFiltersFromRequest(Request $request)
    {
        return [
            'action' => $request->get('action'),
            'resource_type' => $request->get('resource_type'),
            'severity' => $request->get('severity'),
            'user_type' => $request->get('user_type'),
            'department_id' => $request->get('department_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 7)
        ];
    }

    /**
     * Get filter options
     */
    private function getFilterOptions()
    {
        return [
            'actions' => AuditLog::distinct()->pluck('action')->filter()->values()->toArray(),
            'resource_types' => AuditLog::distinct()->pluck('resource_type')->filter()->values()->toArray(),
            'severities' => ['low', 'medium', 'high', 'critical'],
            'user_types' => AuditLog::distinct()->pluck('user_type')->filter()->values()->toArray(),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray()
        ];
    }

    /**
     * Show detailed audit log information
     */
    public function show($id)
    {
        $auditLog = AuditLog::with(['department'])->findOrFail($id);
        
        return view('superadmin.audit.show', compact('auditLog'));
    }
}

