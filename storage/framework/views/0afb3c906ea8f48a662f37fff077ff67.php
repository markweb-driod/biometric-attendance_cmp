

<?php $__env->startSection('title', 'Lecturer Details - ' . ($lecturer->user->full_name ?? 'Lecturer')); ?>
<?php $__env->startSection('page-title', 'Lecturer Details'); ?>

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

<div class="min-h-screen bg-gray-50 w-full px-2 py-6">
    <div class="mb-4">
        <a href="<?php echo e(route('superadmin.lecturers')); ?>" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <div class="h-20 w-20 sm:h-28 sm:w-28 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg border-4 border-purple-200">
                        <span class="text-xl sm:text-2xl font-bold text-white"><?php echo e(substr($lecturer->user->full_name ?? 'NA', 0, 2)); ?></span>
                    </div>
                    <?php if($lecturer->is_active): ?>
                        <div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-2 border-4 border-white shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($lecturer->user->full_name ?? 'N/A'); ?></h1>
                    <p class="text-lg sm:text-xl text-gray-600 font-mono"><?php echo e($lecturer->staff_id); ?></p>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full"><?php echo e($lecturer->department->name ?? 'N/A'); ?></span>
                        <?php if($lecturer->title): ?>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full"><?php echo e($lecturer->title); ?></span>
                        <?php endif; ?>
                        <span class="px-3 py-1 <?php echo e($lecturer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> text-xs font-semibold rounded-full"><?php echo e($lecturer->is_active ? 'Active' : 'Inactive'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Full Name</span><span class="text-gray-900 font-medium"><?php echo e($lecturer->user->full_name ?? 'N/A'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Staff ID</span><span class="text-gray-900 font-mono"><?php echo e($lecturer->staff_id); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Email</span><span class="text-gray-900"><?php echo e($lecturer->user->email ?? 'N/A'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Phone</span><span class="text-gray-900"><?php echo e($lecturer->phone ?? 'Not provided'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Department</span><span class="text-gray-900"><?php echo e($lecturer->department->name ?? 'Not assigned'); ?></span></div>
                <div class="flex justify-between py-2"><span class="text-gray-500">Title</span><span class="text-gray-900"><?php echo e($lecturer->title ?? 'Not provided'); ?></span></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">System & Activity</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Status</span><span class="text-gray-900"><?php echo e($lecturer->is_active ? 'Active' : 'Inactive'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Created</span><span class="text-gray-900"><?php echo e($lecturer->created_at->format('M d, Y')); ?></span></div>
                <div class="flex justify-between py-2"><span class="text-gray-500">Last Updated</span><span class="text-gray-900"><?php echo e($lecturer->updated_at->format('M d, Y')); ?></span></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-center">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <div class="text-xs text-blue-600">Total Classrooms</div>
                    <div class="text-xl font-bold text-blue-900"><?php echo e($classroomsCount); ?></div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <div class="text-xs text-green-600">Active Classrooms</div>
                    <div class="text-xl font-bold text-green-900"><?php echo e($activeClassrooms); ?></div>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg">
                    <div class="text-xs text-purple-600">Assigned Courses</div>
                    <div class="text-xl font-bold text-purple-900"><?php echo e($lecturer->courses->count() ?? 0); ?></div>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <div class="text-xs text-orange-600">Inactive Classrooms</div>
                    <div class="text-xl font-bold text-orange-900"><?php echo e($classroomsCount - $activeClassrooms); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Assigned Courses</h2>
        </div>
        <?php if($lecturer->courses && $lecturer->courses->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $lecturer->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-semibold text-gray-900 text-sm"><?php echo e($course->course_name ?? 'Unknown'); ?></div>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full"><?php echo e($course->course_code ?? 'N/A'); ?></span>
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between"><span>Level:</span><span class="font-medium"><?php echo e($course->academicLevel->name ?? 'N/A'); ?></span></div>
                    <div class="flex justify-between"><span>Credit Units:</span><span><?php echo e($course->credit_units ?? 'N/A'); ?></span></div>
                    <?php if($course->departments && $course->departments->count() > 0): ?>
                    <div class="flex justify-between"><span>Departments:</span><span class="font-medium"><?php echo e($course->departments->pluck('name')->join(', ')); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="text-center text-gray-500">No assigned courses</div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Classrooms</h2>
        <?php if($recentClassrooms && $recentClassrooms->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Class Name</th>
                        <th class="px-3 py-2 text-left">Course</th>
                        <th class="px-3 py-2 text-left">PIN</th>
                        <th class="px-3 py-2 text-left">Students</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $recentClassrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium"><?php echo e($classroom->class_name); ?></td>
                        <td class="px-3 py-2">
                            <div class="text-xs text-gray-500"><?php echo e($classroom->course->course_code ?? 'N/A'); ?></div>
                            <div class="font-medium"><?php echo e($classroom->course->course_name ?? 'N/A'); ?></div>
                        </td>
                        <td class="px-3 py-2 font-mono text-xs"><?php echo e($classroom->pin); ?></td>
                        <td class="px-3 py-2"><?php echo e($classroom->students_count ?? 0); ?></td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold <?php echo e($classroom->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>"><?php echo e($classroom->is_active ? 'Active' : 'Inactive'); ?></span>
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-500"><?php echo e($classroom->created_at->format('M d, Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-gray-500">No classrooms created yet.</div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\lecturer_details.blade.php ENDPATH**/ ?>