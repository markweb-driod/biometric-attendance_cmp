

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Attendance Grading</h1>
        <p class="text-gray-600 mt-2">Select a classroom to view and configure attendance grading</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo e($classroom->class_name); ?></h3>
                <p class="text-gray-600 mb-4"><?php echo e($classroom->course->course_name ?? 'N/A'); ?></p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500"><?php echo e($classroom->students->count()); ?> students</span>
                    <a href="<?php echo e(route('lecturer.grading.show', $classroom->id)); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        View Grading
                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No active classrooms found</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\grading\index.blade.php ENDPATH**/ ?>