<?php $__env->startSection('title', 'Student Details - ' . $student->user->full_name); ?>
<?php $__env->startSection('page-title', 'Student Details'); ?>
<?php $__env->startSection('page-description', 'Complete information for ' . $student->user->full_name); ?>

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

<div class="max-w-7xl w-full mx-auto px-2 sm:px-6 lg:px-8 py-6">
    <!-- Breadcrumb Navigation -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?php echo e(route('lecturer.dashboard')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-green-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="<?php echo e(route('lecturer.students')); ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-green-600 md:ml-2">My Students</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php echo e($student->user->full_name); ?></span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Student Header Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-6">
                <div class="h-24 w-24 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg">
                    <span class="text-2xl font-bold text-white"><?php echo e(substr($student->user->full_name, 0, 2)); ?></span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo e($student->user->full_name); ?></h1>
                    <p class="text-xl text-gray-600 font-mono"><?php echo e($student->matric_number); ?></p>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                            <?php echo e($student->academicLevel->name ?? 'N/A'); ?>

                        </span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                            <?php echo e($student->department->name ?? 'N/A'); ?>

                        </span>
                        <?php if($student->is_active): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                Active
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                                Inactive
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="flex space-x-4">
                <a href="<?php echo e(route('lecturer.student.attendance', $student->id)); ?>" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    View Attendance
                </a>
                <button onclick="printStudentDetails()" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Details
                </button>
            </div>
        </div>
    </div>

    <!-- Student Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Personal Information -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Full Name</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->user->full_name); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Matric Number</span>
                    <span class="text-sm font-mono text-gray-900"><?php echo e($student->matric_number); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Email Address</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->user->email); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Phone Number</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->phone ?? 'Not provided'); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Department</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->department->name ?? 'Not assigned'); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Academic Level</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->academicLevel->name ?? 'Not assigned'); ?></span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-gray-500">Account Status</span>
                    <?php if($student->is_active): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    <?php else: ?>
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Academic Information
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Enrolled Courses</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->classrooms->count()); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Face Registration</span>
                    <?php if($student->face_registration_enabled): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Enabled</span>
                    <?php else: ?>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Disabled</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Reference Image</span>
                    <?php if($student->reference_image_path): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Available</span>
                    <?php else: ?>
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Not Available</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Registration Date</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->created_at->format('M d, Y')); ?></span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-gray-500">Last Updated</span>
                    <span class="text-sm text-gray-900"><?php echo e($student->updated_at->format('M d, Y')); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Enrolled Courses
        </h2>
        <?php if($student->classrooms->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $student->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900"><?php echo e($classroom->course->course_name ?? 'Unknown Course'); ?></h3>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                        <?php echo e($classroom->course->course_code ?? 'N/A'); ?>

                    </span>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Class:</span>
                        <span><?php echo e($classroom->class_name); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Lecturer:</span>
                        <span><?php echo e($classroom->lecturer->user->full_name ?? 'N/A'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>PIN:</span>
                        <span class="font-mono"><?php echo e($classroom->pin); ?></span>
                    </div>
                    <?php if($classroom->schedule): ?>
                    <div class="flex justify-between">
                        <span>Schedule:</span>
                        <span><?php echo e($classroom->schedule); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <?php if($classroom->is_active): ?>
                            <span class="text-green-600 font-semibold">Active</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="text-center py-8">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Enrolled Courses</h3>
            <p class="text-gray-500">This student is not enrolled in any courses yet.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Attendance Summary -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Recent Attendance Summary
        </h2>
        <div class="text-center py-8">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Attendance Records</h3>
            <p class="text-gray-500 mb-4">View detailed attendance records for this student.</p>
            <a href="<?php echo e(route('lecturer.student.attendance', $student->id)); ?>" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                View Full Attendance History
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function printStudentDetails() {
    window.print();
}

// Print styles
const printStyles = `
    @media print {
        .no-print { display: none !important; }
        body { font-size: 12px; }
        .bg-gradient-to-br { background: #10b981 !important; }
        .text-white { color: white !important; }
    }
`;

const styleSheet = document.createElement("style");
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\student_detail.blade.php ENDPATH**/ ?>