

<?php $__env->startSection('title', 'Reporting Dashboard'); ?>
<?php $__env->startSection('page-title', 'Reporting Dashboard'); ?>
<?php $__env->startSection('page-description', 'Comprehensive analytics and reporting for attendance and system performance'); ?>

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
    <!-- Header -->
    <div class="bg-white shadow-lg border-l-4 border-green-500 rounded-lg mb-6">
        <div class="px-6 py-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-green-800">Reporting Dashboard</h1>
            <p class="text-sm text-green-600 mt-1">Comprehensive analytics and system performance metrics</p>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Attendance Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Attendance</h3>
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Today:</span>
                    <span class="font-semibold"><?php echo e($stats['attendance']['today'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">This Week:</span>
                    <span class="font-semibold"><?php echo e($stats['attendance']['this_week'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">This Month:</span>
                    <span class="font-semibold"><?php echo e($stats['attendance']['this_month'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm border-t pt-2 mt-2">
                    <span class="text-gray-700 font-medium">Total:</span>
                    <span class="font-bold text-blue-600"><?php echo e($stats['attendance']['total'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Sessions Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Sessions</h3>
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Today:</span>
                    <span class="font-semibold"><?php echo e($stats['sessions']['today'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">This Week:</span>
                    <span class="font-semibold"><?php echo e($stats['sessions']['this_week'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">This Month:</span>
                    <span class="font-semibold"><?php echo e($stats['sessions']['this_month'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm border-t pt-2 mt-2">
                    <span class="text-gray-700 font-medium">Total:</span>
                    <span class="font-bold text-purple-600"><?php echo e($stats['sessions']['total'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Users Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Users</h3>
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Students:</span>
                    <span class="font-semibold"><?php echo e($stats['users']['students'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Lecturers:</span>
                    <span class="font-semibold"><?php echo e($stats['users']['lecturers'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm border-t pt-2 mt-2">
                    <span class="text-gray-700 font-medium">Total Active:</span>
                    <span class="font-bold text-green-600"><?php echo e($stats['users']['total'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Academic Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-600">Academic</h3>
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Departments:</span>
                    <span class="font-semibold"><?php echo e($stats['academic']['departments'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Courses:</span>
                    <span class="font-semibold"><?php echo e($stats['academic']['courses'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Classrooms:</span>
                    <span class="font-semibold"><?php echo e($stats['academic']['classrooms'] ?? 0); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Attendance Trends Chart -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Attendance Trends (Last 30 Days)</h2>
            <div style="position: relative; height: 300px;">
                <canvas id="attendanceTrendsChart"></canvas>
            </div>
        </div>

        <!-- Top Performing Classes -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Performing Classes</h2>
            <div class="space-y-3">
                <?php if(isset($topPerformingClasses) && $topPerformingClasses->count() > 0): ?>
                    <?php $__currentLoopData = $topPerformingClasses->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="font-semibold text-sm text-gray-900"><?php echo e($class['class_name'] ?? 'N/A'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($class['course_name'] ?? 'N/A'); ?> • <?php echo e($class['lecturer_name'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-green-600"><?php echo e($class['attendance_rate'] ?? 0); ?>%</div>
                            <div class="text-xs text-gray-500"><?php echo e($class['attendance_count'] ?? 0); ?> / <?php echo e($class['session_count'] ?? 0); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center text-gray-500 py-8">No data available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Department Statistics</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs">Department</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs">Code</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs">Students</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs">Lecturers</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs">Courses</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(isset($departmentStats) && $departmentStats->count() > 0): ?>
                        <?php $__currentLoopData = $departmentStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($dept['name'] ?? 'N/A'); ?></td>
                            <td class="px-4 py-3 text-gray-600 font-mono text-xs"><?php echo e($dept['code'] ?? 'N/A'); ?></td>
                            <td class="px-4 py-3"><?php echo e($dept['students_count'] ?? 0); ?></td>
                            <td class="px-4 py-3"><?php echo e($dept['lecturers_count'] ?? 0); ?></td>
                            <td class="px-4 py-3"><?php echo e($dept['courses_count'] ?? 0); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No department data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Generation Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Generate Reports</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <form action="<?php echo e(route('superadmin.reporting.attendance-report')); ?>" method="POST" target="_blank" class="inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="format" value="excel">
                <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Attendance Report
                </button>
            </form>
            <form action="<?php echo e(route('superadmin.reporting.student-performance-report')); ?>" method="POST" target="_blank" class="inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="format" value="excel">
                <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    Student Performance
                </button>
            </form>
            <form action="<?php echo e(route('superadmin.reporting.system-analytics-report')); ?>" method="POST" target="_blank" class="inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="format" value="excel">
                <button type="submit" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                    System Analytics
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Store chart instance to prevent duplicates
    let attendanceTrendsChartInstance = null;

    // Attendance Trends Chart
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('attendanceTrendsChart');
        if (!canvas) return;

        // Destroy existing chart if it exists
        if (attendanceTrendsChartInstance) {
            attendanceTrendsChartInstance.destroy();
            attendanceTrendsChartInstance = null;
        }

        const trendsData = <?php echo json_encode($attendanceTrends ?? [], 15, 512) ?>;
        if (trendsData.length === 0) return;

        const labels = trendsData.map(t => {
            const date = new Date(t.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const attendanceData = trendsData.map(t => t.attendance ?? 0);
        const sessionsData = trendsData.map(t => t.sessions ?? 0);
        const rateData = trendsData.map(t => t.attendance_rate ?? 0);

        const ctx = canvas.getContext('2d');
        attendanceTrendsChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Attendance Count',
                        data: attendanceData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Sessions',
                        data: sessionsData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Attendance Rate %',
                        data: rateData,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            max: 100,
                            stepSize: 10
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    });

</script>
<?php $__env->stopPush(); ?>


                    <?php if(isset($departmentStats) && $departmentStats->count() > 0): ?>

                        <?php $__currentLoopData = $departmentStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($dept['name'] ?? 'N/A'); ?></td>

                            <td class="px-4 py-3 text-gray-600 font-mono text-xs"><?php echo e($dept['code'] ?? 'N/A'); ?></td>

                            <td class="px-4 py-3"><?php echo e($dept['students_count'] ?? 0); ?></td>

                            <td class="px-4 py-3"><?php echo e($dept['lecturers_count'] ?? 0); ?></td>

                            <td class="px-4 py-3"><?php echo e($dept['courses_count'] ?? 0); ?></td>

                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No department data available</td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>



    <!-- Report Generation Section -->

    <div class="bg-white rounded-xl shadow-lg p-6">

        <h2 class="text-xl font-semibold text-gray-900 mb-4">Generate Reports</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <form action="<?php echo e(route('superadmin.reporting.attendance-report')); ?>" method="POST" target="_blank" class="inline">

                <?php echo csrf_field(); ?>

                <input type="hidden" name="format" value="excel">

                <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">

                    Attendance Report

                </button>

            </form>

            <form action="<?php echo e(route('superadmin.reporting.student-performance-report')); ?>" method="POST" target="_blank" class="inline">

                <?php echo csrf_field(); ?>

                <input type="hidden" name="format" value="excel">

                <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">

                    Student Performance

                </button>

            </form>

            <form action="<?php echo e(route('superadmin.reporting.system-analytics-report')); ?>" method="POST" target="_blank" class="inline">

                <?php echo csrf_field(); ?>

                <input type="hidden" name="format" value="excel">

                <button type="submit" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">

                    System Analytics

                </button>

            </form>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // Store chart instance to prevent duplicates

    let attendanceTrendsChartInstance = null;



    // Attendance Trends Chart

    document.addEventListener('DOMContentLoaded', function() {

        const canvas = document.getElementById('attendanceTrendsChart');

        if (!canvas) return;



        // Destroy existing chart if it exists

        if (attendanceTrendsChartInstance) {

            attendanceTrendsChartInstance.destroy();

            attendanceTrendsChartInstance = null;

        }



        const trendsData = <?php echo json_encode($attendanceTrends ?? [], 15, 512) ?>;

        if (trendsData.length === 0) return;



        const labels = trendsData.map(t => {

            const date = new Date(t.date);

            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

        });

        const attendanceData = trendsData.map(t => t.attendance ?? 0);

        const sessionsData = trendsData.map(t => t.sessions ?? 0);

        const rateData = trendsData.map(t => t.attendance_rate ?? 0);



        const ctx = canvas.getContext('2d');

        attendanceTrendsChartInstance = new Chart(ctx, {

            type: 'line',

            data: {

                labels: labels,

                datasets: [

                    {

                        label: 'Attendance Count',

                        data: attendanceData,

                        borderColor: '#10b981',

                        backgroundColor: 'rgba(16, 185, 129, 0.1)',

                        tension: 0.3,

                        fill: false,

                        yAxisID: 'y'

                    },

                    {

                        label: 'Sessions',

                        data: sessionsData,

                        borderColor: '#3b82f6',

                        backgroundColor: 'rgba(59, 130, 246, 0.1)',

                        tension: 0.3,

                        fill: false,

                        yAxisID: 'y'

                    },

                    {

                        label: 'Attendance Rate %',

                        data: rateData,

                        borderColor: '#8b5cf6',

                        backgroundColor: 'rgba(139, 92, 246, 0.1)',

                        tension: 0.3,

                        fill: false,

                        yAxisID: 'y1'

                    }

                ]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {

                    mode: 'index',

                    intersect: false,

                },

                scales: {

                    x: {

                        display: true,

                        grid: {

                            display: false

                        }

                    },

                    y: {

                        type: 'linear',

                        display: true,

                        position: 'left',

                        beginAtZero: true,

                        ticks: {

                            stepSize: 1

                        },

                        grid: {

                            color: 'rgba(0, 0, 0, 0.05)'

                        }

                    },

                    y1: {

                        type: 'linear',

                        display: true,

                        position: 'right',

                        beginAtZero: true,

                        max: 100,

                        ticks: {

                            max: 100,

                            stepSize: 10

                        },

                        grid: {

                            drawOnChartArea: false

                        }

                    }

                },

                plugins: {

                    legend: {

                        display: true,

                        position: 'top'

                    },

                    tooltip: {

                        enabled: true

                    }

                }

            }

        });

    });



</script>

<?php $__env->stopPush(); ?>





<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\reporting.blade.php ENDPATH**/ ?>