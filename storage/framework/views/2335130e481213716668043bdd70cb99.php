<?php $__env->startSection('title', 'Student Details - ' . ($student->user->full_name ?? 'Student')); ?>
<?php $__env->startSection('page-title', 'Student Details'); ?>

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
        <a href="<?php echo e(url('/superadmin/students')); ?>" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    <?php if($student->reference_image_path): ?>
                        <img src="<?php echo e(asset('storage/' . $student->reference_image_path)); ?>" alt="<?php echo e($student->user->full_name); ?>" class="h-20 w-20 sm:h-28 sm:w-28 rounded-full object-cover border-4 border-blue-200 shadow-lg">
                    <?php else: ?>
                        <div class="h-20 w-20 sm:h-28 sm:w-28 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg border-4 border-blue-200">
                            <span class="text-xl sm:text-2xl font-bold text-white"><?php echo e(substr($student->user->full_name ?? 'NA', 0, 2)); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($student->face_registration_enabled): ?>
                        <div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-2 border-4 border-white shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($student->user->full_name); ?></h1>
                    <p class="text-lg sm:text-xl text-gray-600 font-mono"><?php echo e($student->matric_number); ?></p>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full"><?php echo e($student->academicLevel->name ?? 'N/A'); ?></span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full"><?php echo e($student->department->name ?? 'N/A'); ?></span>
                        <span class="px-3 py-1 <?php echo e($student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> text-xs font-semibold rounded-full"><?php echo e($student->is_active ? 'Active' : 'Inactive'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Full Name</span><span class="text-gray-900 font-medium"><?php echo e($student->user->full_name); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Matric Number</span><span class="text-gray-900 font-mono"><?php echo e($student->matric_number); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Email</span><span class="text-gray-900"><?php echo e($student->user->email); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Phone</span><span class="text-gray-900"><?php echo e($student->phone ?? 'Not provided'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Department</span><span class="text-gray-900"><?php echo e($student->department->name ?? 'Not assigned'); ?></span></div>
                <div class="flex justify-between py-2"><span class="text-gray-500">Academic Level</span><span class="text-gray-900"><?php echo e($student->academicLevel->name ?? 'Not assigned'); ?></span></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">System & Attendance</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Face Registration</span><span class="text-gray-900"><?php echo e($student->face_registration_enabled ? 'Enabled' : 'Disabled'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Reference Image</span><span class="text-gray-900"><?php echo e($student->reference_image_path ? 'Available' : 'Not Available'); ?></span></div>
                <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Registered</span><span class="text-gray-900"><?php echo e($student->created_at->format('M d, Y')); ?></span></div>
                <div class="flex justify-between py-2"><span class="text-gray-500">Last Updated</span><span class="text-gray-900"><?php echo e($student->updated_at->format('M d, Y')); ?></span></div>
            </div>
            <?php if($attendanceStats): ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-center">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <div class="text-xs text-blue-600">Total</div>
                    <div class="text-xl font-bold text-blue-900"><?php echo e($attendanceStats->total_records); ?></div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <div class="text-xs text-green-600">Present</div>
                    <div class="text-xl font-bold text-green-900"><?php echo e($attendanceStats->present_count); ?></div>
                </div>
                <div class="bg-red-50 p-3 rounded-lg">
                    <div class="text-xs text-red-600">Absent</div>
                    <div class="text-xl font-bold text-red-900"><?php echo e($attendanceStats->absent_count); ?></div>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg">
                    <div class="text-xs text-yellow-600">Late</div>
                    <div class="text-xl font-bold text-yellow-900"><?php echo e($attendanceStats->late_count); ?></div>
                </div>
            </div>
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Last 30 days</h3>
                <canvas id="attChart" height="120"></canvas>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($student->reference_image_path): ?>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Registered Image</h2>
        <div class="bg-gray-50 rounded-lg p-4 flex justify-center">
            <img src="<?php echo e(asset('storage/' . $student->reference_image_path)); ?>" class="max-w-full h-auto rounded-lg shadow-md border" style="max-height: 380px;">
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Enrolled Courses</h2>
            <div class="flex gap-2">
                <form method="POST" action="<?php echo e($student->face_registration_enabled ? route('superadmin.students.face-registration-management.disable', $student->id) : route('superadmin.students.face-registration-management.enable', $student->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="px-3 py-1.5 text-xs rounded-lg <?php echo e($student->face_registration_enabled ? 'bg-yellow-100 text-yellow-800' : 'bg-green-600 text-white'); ?> hover:opacity-90">
                        <?php echo e($student->face_registration_enabled ? 'Disable Face Reg' : 'Enable Face Reg'); ?>

                    </button>
                </form>
            </div>
        </div>
        <?php if($student->classrooms->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $student->classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-semibold text-gray-900 text-sm"><?php echo e($classroom->course->course_name ?? 'Unknown'); ?></div>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full"><?php echo e($classroom->course->course_code ?? 'N/A'); ?></span>
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between"><span>Class:</span><span class="font-medium"><?php echo e($classroom->class_name); ?></span></div>
                    <div class="flex justify-between"><span>Lecturer:</span><span class="font-medium"><?php echo e($classroom->lecturer->user->full_name ?? 'N/A'); ?></span></div>
                    <?php if($classroom->schedule): ?>
                    <div class="flex justify-between"><span>Schedule:</span><span><?php echo e($classroom->schedule); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <div class="text-center text-gray-500">No enrolled courses</div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Attendance Activity</h2>
        <?php if(isset($recentAttendances) && $recentAttendances->count()): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Date/Time</th>
                        <th class="px-3 py-2 text-left">Course</th>
                        <th class="px-3 py-2 text-left">Class</th>
                        <th class="px-3 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $recentAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-3 py-2"><?php echo e($a->created_at->format('Y-m-d H:i')); ?></td>
                        <td class="px-3 py-2"><?php echo e($a->classroom->course->course_name ?? '-'); ?></td>
                        <td class="px-3 py-2"><?php echo e($a->classroom->class_name ?? '-'); ?></td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold <?php echo e($a->status === 'present' ? 'bg-green-100 text-green-800' : ($a->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')); ?>"><?php echo e(ucfirst($a->status)); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-gray-500">No recent attendance records.</div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const series = <?php echo json_encode($attendanceSeries ?? [], 15, 512) ?>;
    if (series.length && document.getElementById('attChart')) {
        const labels = series.map(p => p.date.slice(5));
        const present = series.map(p => p.present);
        const absent = series.map(p => p.absent);
        const late = series.map(p => p.late);
        new Chart(document.getElementById('attChart'), {
            type: 'line',
            data: { labels, datasets: [
                { label: 'Present', data: present, borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,0.1)', tension: 0.3 },
                { label: 'Absent', data: absent, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.1)', tension: 0.3 },
                { label: 'Late', data: late, borderColor: '#ca8a04', backgroundColor: 'rgba(202,138,4,0.1)', tension: 0.3 }
            ]},
            options: { plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true, ticks: { precision:0 } } } }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\student_details.blade.php ENDPATH**/ ?>