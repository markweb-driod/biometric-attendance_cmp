

<?php $__env->startSection('title', 'Lecturer Details - ' . $lecturer->user->full_name); ?>

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

<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb Navigation -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?php echo e(route('hod.dashboard')); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
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
                        <a href="<?php echo e(route('hod.management.lecturers.index')); ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-purple-600 md:ml-2">Lecturer Management</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php echo e($lecturer->user->full_name); ?></span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Lecturer Header Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <div class="h-20 w-20 sm:h-28 sm:w-28 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg border-4 border-purple-200">
                        <span class="text-xl sm:text-2xl font-bold text-white"><?php echo e(substr($lecturer->user->full_name, 0, 2)); ?></span>
                    </div>
                    <?php if($lecturer->is_active): ?>
                        <div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-2 border-4 border-white shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($lecturer->user->full_name); ?></h1>
                    <p class="text-lg sm:text-xl text-gray-600 font-mono"><?php echo e($lecturer->staff_id); ?></p>
                    <?php if($lecturer->title): ?>
                        <p class="text-base sm:text-lg text-gray-500 mt-1"><?php echo e($lecturer->title); ?></p>
                    <?php endif; ?>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs sm:text-sm font-semibold rounded-full">
                            <?php echo e($lecturer->department->name ?? 'N/A'); ?>

                        </span>
                        <?php if($lecturer->is_active): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs sm:text-sm font-semibold rounded-full">
                                Active
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs sm:text-sm font-semibold rounded-full">
                                Inactive
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('hod.management.lecturers.index')); ?>" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Details
                </button>
            </div>
        </div>
    </div>

    <!-- Lecturer Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Information
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Full Name</span>
                    <span class="text-sm text-gray-900 font-medium"><?php echo e($lecturer->user->full_name); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Staff ID</span>
                    <span class="text-sm font-mono text-gray-900 font-medium"><?php echo e($lecturer->staff_id); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Email Address</span>
                    <span class="text-sm text-gray-900"><?php echo e($lecturer->user->email); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Phone Number</span>
                    <span class="text-sm text-gray-900"><?php echo e($lecturer->phone ?? 'Not provided'); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Department</span>
                    <span class="text-sm text-gray-900"><?php echo e($lecturer->department->name ?? 'Not assigned'); ?></span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-gray-500">Account Status</span>
                    <?php if($lecturer->is_active): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    <?php else: ?>
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Professional Information
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Title</span>
                    <span class="text-sm text-gray-900 font-medium"><?php echo e($lecturer->title ?? 'Not assigned'); ?></span>
                </div>
                <?php if($teachingStats): ?>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Active Classes</span>
                    <span class="text-sm text-gray-900 font-medium"><?php echo e($teachingStats->total_classes ?? 0); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Total Students</span>
                    <span class="text-sm text-gray-900 font-medium"><?php echo e($teachingStats->total_students ?? 0); ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Registration Date</span>
                    <span class="text-sm text-gray-900"><?php echo e($lecturer->created_at->format('M d, Y')); ?></span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-sm font-medium text-gray-500">Last Updated</span>
                    <span class="text-sm text-gray-900"><?php echo e($lecturer->updated_at->format('M d, Y')); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Teaching Statistics -->
    <?php if($teachingStats && $teachingStats->total_classes > 0): ?>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Teaching Statistics
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
            <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                <p class="text-sm text-purple-600 font-medium mb-1">Active Classes</p>
                <p class="text-2xl font-bold text-purple-900"><?php echo e($teachingStats->total_classes ?? 0); ?></p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-blue-600 font-medium mb-1">Total Students</p>
                <p class="text-2xl font-bold text-blue-900"><?php echo e($teachingStats->total_students ?? 0); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Active Classes -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Active Classes
        </h2>
        <?php if($lecturer->classrooms->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $lecturer->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base"><?php echo e($classroom->course->course_name ?? 'Unknown Course'); ?></h3>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                        <?php echo e($classroom->course->course_code ?? 'N/A'); ?>

                    </span>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Class:</span>
                        <span class="font-medium"><?php echo e($classroom->class_name); ?></span>
                    </div>
                    <?php if($classroom->schedule): ?>
                    <div class="flex justify-between">
                        <span>Schedule:</span>
                        <span><?php echo e($classroom->schedule); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span>Students:</span>
                        <span class="font-medium"><?php echo e($classroom->students->count()); ?></span>
                    </div>
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
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Classes</h3>
            <p class="text-gray-500">This lecturer is not currently teaching any classes.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
        <div class="flex flex-wrap gap-3 justify-center">
            <button onclick="openEditModal(<?php echo e($lecturer->id); ?>)" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Lecturer
            </button>
            <a href="<?php echo e(route('hod.monitoring.courses')); ?>?lecturer=<?php echo e($lecturer->id); ?>" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                View Teaching Report
            </a>
            <button onclick="confirmDelete(<?php echo e($lecturer->id); ?>)" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Lecturer
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal (will reuse from index page, but simplified for this page) -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Lecturer</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Redirecting to edit page...</p>
            <div class="flex space-x-3">
                <a href="<?php echo e(route('hod.management.lecturers.index')); ?>" class="flex-1 text-center bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition">Go to Management</a>
                <button onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function openEditModal(id) {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this lecturer? This action cannot be undone.')) {
        fetch(`<?php echo e(route("hod.management.lecturers.api.delete", ":id")); ?>`.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lecturer deleted successfully');
                window.location.href = '<?php echo e(route("hod.management.lecturers.index")); ?>';
            } else {
                alert('Delete failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Delete failed');
        });
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('hod.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\management\lecturers\show.blade.php ENDPATH**/ ?>