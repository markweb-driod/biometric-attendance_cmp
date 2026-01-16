<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Services\AuditLogService;

class HodAuditController extends Controller
{
    protected $auditLogService;

    public function __construct()
    {
        $this->middleware(['auth:hod', 'hod.role']);
        $this->auditLogService = new AuditLogService();
    }

    /**
     * Display audit logs dashboard
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        // Get audit logs for the department
        $auditLogs = $this->auditLogService->getAuditLogs($hod->department_id, $filters);
        $auditStats = $this->auditLogService->getAuditStats($hod->department_id, $filters);
        $recentActivities = $this->auditLogService->getRecentActivities($hod->department_id);
        $securityAlerts = $this->auditLogService->getSecurityAlerts($hod->department_id, $filters);
        
        // Get filter options
        $filterOptions = $this->getFilterOptions();
        
        return view('hod.audit.index', compact(
            'auditLogs',
            'auditStats',
            'recentActivities',
            'securityAlerts',
            'filterOptions',
            'filters'
        ));
    }

    /**
     * Get audit logs via API
     */
    public function getAuditLogs(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $logs = $this->auditLogService->getAuditLogs($hod->department_id, $filters);
        
        return response()->json($logs);
    }

    /**
     * Get audit statistics
     */
    public function getAuditStats(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $stats = $this->auditLogService->getAuditStats($hod->department_id, $filters);
        
        return response()->json($stats);
    }

    /**
     * Get security alerts
     */
    public function getSecurityAlerts(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $alerts = $this->auditLogService->getSecurityAlerts($hod->department_id, $filters);
        
        return response()->json($alerts);
    }

    /**
     * Get compliance report
     */
    public function getComplianceReport(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $report = $this->auditLogService->getComplianceReport($hod->department_id, $filters);
        
        return response()->json($report);
    }

    /**
     * Export audit data
     */
    public function exportData(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $hod = Auth::guard('hod')->user();
        
        $exportData = $this->auditLogService->exportAuditData($hod->department_id, $filters);
        
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
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 8),
            'page' => $request->get('page', 1)
        ];
    }

    /**
     * Get single audit log details
     */
    public function getAuditLogDetails($id)
    {
        $hod = Auth::guard('hod')->user();
        
        $log = AuditLog::where('department_id', $hod->department_id)
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $log
        ]);
    }

    /**
     * Get filter options
     */
    private function getFilterOptions()
    {
        return [
            'actions' => ['create', 'update', 'delete', 'login', 'logout', 'view', 'export', 'override'],
            'resource_types' => ['student', 'lecturer', 'course', 'classroom', 'attendance', 'exam_eligibility'],
            'severities' => ['low', 'medium', 'high', 'critical'],
            'user_types' => ['student', 'lecturer', 'hod', 'superadmin']
        ];
    }
}











