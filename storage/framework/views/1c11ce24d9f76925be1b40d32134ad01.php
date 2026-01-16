<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('page-description', 'Welcome back, ' . ($lecturer->title ?? '') . ' ' . ($lecturer->name ?? '')); ?>

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

<?php if(session('info')): ?>
<div id="flash-info" class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span><?php echo e(session('info')); ?></span>
    <button onclick="closeFlash('flash-info')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Welcome Section -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-green-600 via-green-500 to-green-400 rounded-xl p-6 sm:p-8 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold mb-1">
                        <?php
                            $title = $lecturer->title ?? '';
                            $name = $lecturer->name ?? '';
                        ?>
                        <?php if($title && !str_starts_with($name, $title)): ?>
                            <?php echo e($title . ' ' . $name); ?>

                        <?php else: ?>
                            <?php echo e($name); ?>

                        <?php endif; ?>
                    </h2>
                    <p class="text-green-100 text-sm sm:text-base">Here's what's happening with your classes today</p>
                </div>
                <div class="hidden sm:block mt-3 sm:mt-0">
                    <div class="text-right">
                        <p class="text-green-100 text-sm">Today's Date</p>
                        <p class="text-lg sm:text-xl font-extrabold" id="currentDate"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Total Classes -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 hover:shadow-2xl transition-shadow duration-200 group">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-500">Total Classes</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-gray-900"><?php echo e($stats['total_classes'] ?? 0); ?></p>
                    <p class="text-sm text-green-600">+2 this semester</p>
                </div>
            </div>
        </div>

        <!-- Active Students -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 hover:shadow-2xl transition-shadow duration-200 group">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-500">Active Students</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-gray-900"><?php echo e($stats['total_students'] ?? 0); ?></p>
                    <p class="text-sm text-green-600">+12 this week</p>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 hover:shadow-2xl transition-shadow duration-200 group">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-500">Today's Attendance</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-gray-900"><?php echo e($stats['today_attendance'] ?? 0); ?>%</p>
                    <p class="text-sm text-green-600">+5% vs yesterday</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 hover:shadow-2xl transition-shadow duration-200 group">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-500">Recent Activity</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-gray-900"><?php echo e($stats['recent_activity'] ?? 0); ?></p>
                    <p class="text-sm text-blue-600">New notifications</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <!-- Quick Actions -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="/lecturer/attendance/new" class="flex items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition group shadow-sm">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-semibold text-gray-900">Start Attendance</p>
                            <p class="text-sm text-gray-500">Begin capturing attendance</p>
                        </div>
                    </a>

                    <a href="/lecturer/classes" class="flex items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition group shadow-sm">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-semibold text-gray-900">Manage Classes</p>
                            <p class="text-sm text-gray-500">Create and edit courses</p>
                        </div>
                    </a>

                    <a href="/lecturer/attendance" class="flex items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition group shadow-sm">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-semibold text-gray-900">View Attendance</p>
                            <p class="text-sm text-gray-500">Check student records</p>
                        </div>
                    </a>

                    <a href="/lecturer/reports" class="flex items-center p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition group shadow-sm">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-base font-semibold text-gray-900">Generate Reports</p>
                            <p class="text-sm text-gray-500">Export data & analytics</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $recent_attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start">
                        <div class="w-6 h-6 sm:w-7 sm:h-7 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-2 flex-1">
                                <p class="text-sm font-semibold text-gray-900">
                                    Attendance captured for <?php echo e($attendance->classroom->class_name ?? 'Class'); ?>

                                </p>
                                <p class="text-xs text-gray-500"><?php echo e($attendance->captured_at ? $attendance->captured_at->diffForHumans() : 'Unknown time'); ?></p>
                    </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-gray-500">No recent activity.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Classes -->
    <div class="mt-4">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Today's Classes</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 sm:px-4 py-2 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Class</th>
                            <th class="px-2 sm:px-4 py-2 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Time</th>
                            <th class="px-2 sm:px-4 py-2 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Students</th>
                            <th class="px-2 sm:px-4 py-2 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-2 sm:px-4 py-2 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="text-base font-semibold text-gray-900"><?php echo e($class->course_code ?? $class['code'] ?? '-'); ?> - <?php echo e($class->class_name ?? $class['name'] ?? '-'); ?></div>
                                <div class="text-sm text-gray-500">Room <?php echo e($class->room ?? '-'); ?></div>
                            </td>
                            <td>
                                <div class="text-base text-gray-900"><?php echo e($class->schedule ?? $class['schedule'] ?? '--'); ?></div>
                            </td>
                            <td>
                                <div class="text-base text-gray-900"><?php echo e(isset($class->students) ? $class->students->count() : (isset($class['student_count']) ? $class['student_count'] : 0)); ?></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">Completed</span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('lecturer.class.detail', ['classId' => $class->id ?? $class['id']])); ?>" class="text-blue-600 hover:text-blue-900 text-base font-semibold">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set current date
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\dashboard.blade.php ENDPATH**/ ?>