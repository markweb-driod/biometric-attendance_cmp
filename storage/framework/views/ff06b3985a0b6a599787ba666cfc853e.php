<?php $__env->startSection('title', 'Attendance Audit & Live Monitoring'); ?>
<?php $__env->startSection('page-title', 'Attendance Audit & Live Monitoring'); ?>
<?php $__env->startSection('page-description', 'Comprehensive attendance tracking with real-time activity monitoring, charts, and detailed analytics'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .kpi-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .kpi-main-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    .kpi-sub-value {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }
    .kpi-change {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }
    .kpi-change.positive { color: #10b981; }
    .kpi-change.negative { color: #ef4444; }
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    .live-activity-item {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .live-activity-item.new {
        background-color: #f0fdf4;
        border-left: 4px solid #10b981;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-badge.present {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-badge.denied {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Flash Messages -->
<?php if(session('success')): ?>
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span><?php echo e(session('success')); ?></span>
    <button onclick="closeFlash('flash-success')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span><?php echo e(session('error')); ?></span>
    <button onclick="closeFlash('flash-error')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Header with Live Indicator -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Attendance Audit & Live Monitoring</h2>
            <p class="text-gray-600 mt-1">Real-time system activity and comprehensive analytics</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="pulse-dot"></div>
                <span class="text-sm font-medium text-gray-700" id="live-indicator">Live</span>
            </div>
            <button onclick="toggleAutoRefresh()" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition" id="auto-refresh-btn">
                Auto-Refresh: ON
            </button>
            <a href="<?php echo e(route('superadmin.attendance.audit.export')); ?>" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">
                Export CSV
            </a>
        </div>
    </div>

    <!-- Enhanced KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Attempts -->
        <div class="kpi-card border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Total Attempts</h3>
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="kpi-main-value text-blue-600" id="kpi-total"><?php echo e(number_format($stats['overall']['total_attempts'] ?? 0)); ?></div>
            <div class="kpi-sub-value">All time records</div>
            <div class="kpi-sub-value mt-2">
                Today: <strong id="kpi-today-attempts"><?php echo e($stats['today']['attempts'] ?? 0); ?></strong> | 
                Week: <strong id="kpi-week-attempts"><?php echo e($stats['this_week']['attempts'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- Present -->
        <div class="kpi-card border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Present</h3>
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="kpi-main-value text-green-600" id="kpi-present"><?php echo e(number_format($stats['overall']['total_present'] ?? 0)); ?></div>
            <div class="kpi-sub-value">
                Success Rate: <strong id="kpi-success-rate"><?php echo e($stats['overall']['total_attempts'] > 0 ? round(($stats['overall']['total_present'] / $stats['overall']['total_attempts']) * 100, 1) : 0); ?>%</strong>
            </div>
            <div class="kpi-sub-value mt-2">
                Today: <strong id="kpi-today-present"><?php echo e($stats['today']['present'] ?? 0); ?></strong> | 
                Month: <strong id="kpi-month-present"><?php echo e($stats['this_month']['present'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- Denied -->
        <div class="kpi-card border-l-4 border-red-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Denied</h3>
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="kpi-main-value text-red-600" id="kpi-denied"><?php echo e(number_format($stats['overall']['total_denied'] ?? 0)); ?></div>
            <div class="kpi-sub-value">
                Denial Rate: <strong id="kpi-denial-rate"><?php echo e($stats['overall']['total_attempts'] > 0 ? round(($stats['overall']['total_denied'] / $stats['overall']['total_attempts']) * 100, 1) : 0); ?>%</strong>
            </div>
            <div class="kpi-sub-value mt-2">
                Today: <strong id="kpi-today-denied"><?php echo e($stats['today']['denied'] ?? 0); ?></strong> | 
                This Week: <strong id="kpi-week-denied"><?php echo e($stats['this_week']['denied'] ?? 0); ?></strong>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="kpi-card border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Active Sessions</h3>
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="kpi-main-value text-purple-600" id="kpi-active-sessions"><?php echo e($stats['sessions']['active'] ?? 0); ?></div>
            <div class="kpi-sub-value">
                Total Sessions: <strong id="kpi-total-sessions"><?php echo e($stats['sessions']['total'] ?? 0); ?></strong>
            </div>
            <div class="kpi-sub-value mt-2">
                Created Today: <strong id="kpi-sessions-today"><?php echo e($stats['today']['sessions'] ?? 0); ?></strong> | 
                Completed: <strong id="kpi-sessions-completed"><?php echo e($stats['sessions']['completed_today'] ?? 0); ?></strong>
            </div>
        </div>
    </div>

    <!-- Additional KPI Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Unique Students -->
        <div class="kpi-card border-l-4 border-indigo-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Unique Students</h3>
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-indigo-600" id="kpi-unique"><?php echo e($stats['overall']['unique_students'] ?? 0); ?></div>
            <div class="text-xs text-gray-500 mt-1">Students who attempted attendance</div>
        </div>

        <!-- Suspected Spoofers -->
        <div class="kpi-card border-l-4 border-orange-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Suspected Spoofers</h3>
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-orange-600" id="kpi-spoofers"><?php echo e($stats['overall']['suspected_spoofers'] ?? 0); ?></div>
            <div class="text-xs text-gray-500 mt-1">Students with 3+ denied attempts</div>
        </div>

        <!-- Biometric Success Rate -->
        <div class="kpi-card border-l-4 border-teal-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Biometric Success</h3>
                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-teal-600" id="kpi-biometric-rate"><?php echo e($stats['biometric']['biometric_success_rate'] ?? 0); ?>%</div>
            <div class="text-xs text-gray-500 mt-1">
                Biometric: <?php echo e($stats['biometric']['total_biometric'] ?? 0); ?> | Manual: <?php echo e($stats['biometric']['total_manual'] ?? 0); ?>

            </div>
        </div>

        <!-- System Health -->
        <div class="kpi-card border-l-4 border-cyan-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">System Health</h3>
                <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="text-2xl font-bold text-cyan-600" id="kpi-system-health">Healthy</div>
            <div class="text-xs text-gray-500 mt-1" id="kpi-last-update">Last updated: <span id="last-update-time">-</span></div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Trends Chart (30 days) -->
        <div class="chart-container">
            <h3 class="text-lg font-semibold mb-4">Attendance Trends (Last 30 Days)</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="dailyTrendsChart"></canvas>
            </div>
        </div>

        <!-- Hourly Activity Chart (Today) -->
        <div class="chart-container">
            <h3 class="text-lg font-semibold mb-4">Today's Hourly Activity</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        <!-- Status Breakdown Pie Chart -->
        <div class="chart-container">
            <h3 class="text-lg font-semibold mb-4">Status Breakdown</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>

        <!-- Method Breakdown Pie Chart -->
        <div class="chart-container">
            <h3 class="text-lg font-semibold mb-4">Method Breakdown (Biometric vs Manual)</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="methodPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Breakdown Chart -->
    <div class="chart-container mb-6">
        <h3 class="text-lg font-semibold mb-4">Attendance by Department</h3>
        <div style="position: relative; height: 350px;">
            <canvas id="departmentChart"></canvas>
        </div>
    </div>

    <!-- Live Activity Monitor -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Live Activity Feed</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Auto-refresh every <span id="refresh-interval">5</span> seconds</span>
                <button onclick="refreshActivity()" class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto" id="live-activity-container">
            <div class="space-y-2" id="live-activity-list">
                <!-- Live activities will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Enhanced Data Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Detailed Attendance Records</h3>
            <div class="flex gap-2">
                <input type="text" id="table-search" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <select id="table-filter-status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Status</option>
                    <option value="present">Present</option>
                    <option value="denied">Denied</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="attendance-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                    <?php
                        $deniedCounts = [];
                        foreach ($attendances as $a) {
                            $sid = $a->student->id ?? null;
                            if ($a->status === 'denied' && $sid) {
                                $deniedCounts[$sid] = ($deniedCounts[$sid] ?? 0) + 1;
                            }
                        }
                    ?>
                    <?php $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $sid = $attendance->student->id ?? null;
                        $spoofing = ($sid && ($deniedCounts[$sid] ?? 0) >= 3);
                    ?>
                    <tr <?php if($spoofing): ?> class="bg-yellow-50" <?php endif; ?> data-status="<?php echo e($attendance->status); ?>" data-search="<?php echo e(strtolower(($attendance->student->user->full_name ?? '') . ' ' . ($attendance->student->matric_number ?? '') . ' ' . ($attendance->classroom->class_name ?? '')))); ?>">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-medium text-gray-900"><?php echo e($attendance->student->user->full_name ?? '-'); ?></div>
                            <div class="text-sm text-gray-500"><?php echo e($attendance->student->matric_number ?? ''); ?></div>
                            <?php if($spoofing): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">SUSPECTED SPOOFING</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($attendance->student->department->name ?? '-'); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($attendance->classroom->class_name ?? '-'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($attendance->classroom->course->course_code ?? ''); ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($attendance->classroom->course->course_name ?? '-'); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($attendance->attendanceSession->code ?? '-'); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($attendance->captured_at ? \Carbon\Carbon::parse($attendance->captured_at)->format('Y-m-d') : '-'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($attendance->captured_at ? \Carbon\Carbon::parse($attendance->captured_at)->format('H:i:s') : '-'); ?></div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php if($attendance->status === 'present'): ?>
                                <span class="status-badge present">Present</span>
                            <?php else: ?>
                                <span class="status-badge denied">Denied</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($attendance->image_path ? 'Biometric' : 'Manual'); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            <?php if($attendance->latitude && $attendance->longitude): ?>
                                <div>Lat: <?php echo e(number_format($attendance->latitude, 6)); ?></div>
                                <div>Lng: <?php echo e(number_format($attendance->longitude, 6)); ?></div>
                            <?php else: ?>
                                <span class="text-gray-400">No location</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4"><?php echo e($attendances->links()); ?></div>
    </div>
</div>

<link rel="stylesheet" href="/js/vendor/leaflet/leaflet.css" />
<script src="/js/vendor/leaflet/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let charts = {};
let autoRefreshInterval = null;
let autoRefreshEnabled = true;
let lastUpdateId = null;

// Initialize charts and live monitoring
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    refreshActivity();
    
    // Setup auto-refresh
    if (autoRefreshEnabled) {
        startAutoRefresh();
    }
    
    // Setup table search and filters
    setupTableFilters();
    
    // Update last update time
    updateLastUpdateTime();
    setInterval(updateLastUpdateTime, 1000);
});

function loadDashboardData() {
    fetch('<?php echo e(route('superadmin.attendance.audit.stats')); ?>')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateKPIs(data.stats);
                renderCharts(data);
            }
        })
        .catch(err => console.error('Error loading dashboard data:', err));
}

function updateKPIs(stats) {
    // Update KPI values
    updateElement('kpi-total', formatNumber(stats.overall.total_attempts));
    updateElement('kpi-present', formatNumber(stats.overall.total_present));
    updateElement('kpi-denied', formatNumber(stats.overall.total_denied));
    updateElement('kpi-unique', formatNumber(stats.overall.unique_students));
    updateElement('kpi-spoofers', formatNumber(stats.overall.suspected_spoofers));
    updateElement('kpi-active-sessions', stats.sessions.active || 0);
    updateElement('kpi-total-sessions', stats.sessions.total || 0);
    
    // Calculate and update rates
    const totalAttempts = stats.overall.total_attempts || 0;
    const successRate = totalAttempts > 0 ? round((stats.overall.total_present / totalAttempts) * 100, 1) : 0;
    const denialRate = totalAttempts > 0 ? round((stats.overall.total_denied / totalAttempts) * 100, 1) : 0;
    
    updateElement('kpi-success-rate', successRate + '%');
    updateElement('kpi-denial-rate', denialRate + '%');
    
    // Update time-based KPIs
    updateElement('kpi-today-attempts', stats.today.attempts || 0);
    updateElement('kpi-week-attempts', stats.this_week.attempts || 0);
    updateElement('kpi-today-present', stats.today.present || 0);
    updateElement('kpi-month-present', stats.this_month.present || 0);
    updateElement('kpi-today-denied', stats.today.denied || 0);
    updateElement('kpi-week-denied', stats.this_week.denied || 0);
    updateElement('kpi-sessions-today', stats.today.sessions || 0);
    updateElement('kpi-sessions-completed', stats.sessions.completed_today || 0);
    updateElement('kpi-biometric-rate', (stats.biometric.biometric_success_rate || 0) + '%');
}

function renderCharts(data) {
    // Destroy existing charts
    Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
    });
    charts = {};
    
    // Daily Trends Chart
    const dailyCtx = document.getElementById('dailyTrendsChart');
    if (dailyCtx && data.daily_data) {
        charts.daily = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: data.daily_data.map(d => d.label),
                datasets: [
                    {
                        label: 'Present',
                        data: data.daily_data.map(d => d.present),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Denied',
                        data: data.daily_data.map(d => d.denied),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Total',
                        data: data.daily_data.map(d => d.total),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true,
                        hidden: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    
    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart');
    if (hourlyCtx && data.hourly_data) {
        charts.hourly = new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: data.hourly_data.map(d => d.hour),
                datasets: [
                    {
                        label: 'Present',
                        data: data.hourly_data.map(d => d.present),
                        backgroundColor: '#10b981'
                    },
                    {
                        label: 'Denied',
                        data: data.hourly_data.map(d => d.denied),
                        backgroundColor: '#ef4444'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    x: { stacked: true },
                    y: { beginAtZero: true, stacked: true }
                }
            }
        });
    }
    
    // Status Pie Chart
    const statusCtx = document.getElementById('statusPieChart');
    if (statusCtx && data.status_breakdown) {
        charts.status = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Denied'],
                datasets: [{
                    data: [data.status_breakdown.present, data.status_breakdown.denied],
                    backgroundColor: ['#10b981', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' }
                }
            }
        });
    }
    
    // Method Pie Chart
    const methodCtx = document.getElementById('methodPieChart');
    if (methodCtx && data.method_breakdown) {
        charts.method = new Chart(methodCtx, {
            type: 'doughnut',
            data: {
                labels: ['Biometric', 'Manual'],
                datasets: [{
                    data: [data.method_breakdown.biometric, data.method_breakdown.manual],
                    backgroundColor: ['#3b82f6', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' }
                }
            }
        });
    }
    
    // Department Chart
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx && data.department_breakdown) {
        charts.department = new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: data.department_breakdown.map(d => d.name),
                datasets: [
                    {
                        label: 'Total Attempts',
                        data: data.department_breakdown.map(d => d.total),
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Present',
                        data: data.department_breakdown.map(d => d.present),
                        backgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
}

function refreshActivity() {
    fetch('<?php echo e(route('superadmin.attendance.audit.live')); ?>?limit=20')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderLiveActivity(data.activities);
            }
        })
        .catch(err => console.error('Error refreshing activity:', err));
}

function renderLiveActivity(activities) {
    const container = document.getElementById('live-activity-list');
    if (!container) return;
    
    const currentIds = Array.from(container.children).map(child => child.dataset.id);
    const newIds = activities.map(a => a.id.toString());
    const isNew = lastUpdateId === null || !currentIds.includes(activities[0]?.id?.toString());
    
    if (isNew) {
        container.innerHTML = '';
        activities.forEach(activity => {
            const item = createActivityItem(activity, true);
            container.appendChild(item);
        });
    } else {
        // Only add new items
        activities.forEach(activity => {
            if (!currentIds.includes(activity.id.toString())) {
                const item = createActivityItem(activity, true);
                container.insertBefore(item, container.firstChild);
            }
        });
    }
    
    // Limit to 20 items
    while (container.children.length > 20) {
        container.removeChild(container.lastChild);
    }
    
    lastUpdateId = activities[0]?.id || null;
}

function createActivityItem(activity, isNew = false) {
    const div = document.createElement('div');
    div.className = `live-activity-item ${isNew ? 'new' : ''}`;
    div.dataset.id = activity.id;
    
    div.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-900">${activity.student_name}</span>
                    <span class="text-xs text-gray-500">${activity.matric}</span>
                    <span class="text-xs text-gray-400">${activity.department}</span>
                </div>
                <div class="text-sm text-gray-600 mt-1">
                    ${activity.class_name} • ${activity.course} • ${activity.session_code}
                </div>
            </div>
            <div class="flex items-center gap-3 ml-4">
                <span class="status-badge ${activity.status}">${activity.status}</span>
                <span class="text-xs text-gray-500">${activity.method}</span>
                <span class="text-xs font-medium text-gray-700">${activity.date} ${activity.time}</span>
            </div>
        </div>
    `;
    
    // Remove 'new' class after animation
    setTimeout(() => div.classList.remove('new'), 3000);
    
    return div;
}

function startAutoRefresh() {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    autoRefreshInterval = setInterval(() => {
        loadDashboardData();
        refreshActivity();
    }, 5000); // Refresh every 5 seconds
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    const btn = document.getElementById('auto-refresh-btn');
    if (autoRefreshEnabled) {
        btn.textContent = 'Auto-Refresh: ON';
        btn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition';
        startAutoRefresh();
    } else {
        btn.textContent = 'Auto-Refresh: OFF';
        btn.className = 'px-4 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition';
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }
}

function setupTableFilters() {
    const searchInput = document.getElementById('table-search');
    const statusFilter = document.getElementById('table-filter-status');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
}

function filterTable() {
    const search = document.getElementById('table-search').value.toLowerCase();
    const status = document.getElementById('table-filter-status').value;
    const rows = document.querySelectorAll('#table-body tr');
    
    rows.forEach(row => {
        const searchText = row.dataset.search || '';
        const rowStatus = row.dataset.status || '';
        const matchesSearch = search === '' || searchText.includes(search);
        const matchesStatus = status === '' || rowStatus === status;
        
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}

function updateLastUpdateTime() {
    const now = new Date();
    document.getElementById('last-update-time').textContent = now.toLocaleTimeString();
}

function updateElement(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function formatNumber(num) {
    return new Intl.NumberFormat().format(num || 0);
}

function round(num, decimals = 1) {
    return Number(num.toFixed(decimals));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\attendance_audit.blade.php ENDPATH**/ ?>