<?php $__env->startSection('title', 'Attendance'); ?>
<?php $__env->startSection('page-title', 'Attendance'); ?>
<?php $__env->startSection('page-description', 'Manage and monitor attendance sessions for your classes'); ?>

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

<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-green-100 py-8">
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-green-200 p-10 mb-8 flex flex-col items-center">
            <h2 class="text-4xl font-extrabold text-green-800 mb-2 tracking-tight flex items-center gap-2">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a4 4 0 100-8 4 4 0 000 8zm0 2c-2.21 0-4 1.79-4 4h8c0-2.21-1.79-4-4-4z" /></svg>
                Manage Attendance
            </h2>
            <p class="text-lg text-green-700 mb-4">Enable, monitor, and manage attendance sessions for your classes</p>
        </div>
        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6" id="stats-cards">
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
                <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-chalkboard"></i></div>
                <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e(count($classes)); ?></div>
                <div class="text-sm font-semibold uppercase tracking-wider z-10">Total Classes</div>
            </div>
            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
                <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-check-circle"></i></div>
                <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e(collect($classes)->filter(fn($c) => $c['session_today'] && $c['session_today']->is_active)->count()); ?></div>
                <div class="text-sm font-semibold uppercase tracking-wider z-10">Active Sessions</div>
            </div>
            <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
                <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-users"></i></div>
                <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e(collect($classes)->sum('student_count')); ?></div>
                <div class="text-sm font-semibold uppercase tracking-wider z-10">Total Students</div>
            </div>
            <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
                <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-percentage"></i></div>
                <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e(count($classes) ? round(collect($classes)->filter(fn($c) => $c['session_today'] && $c['session_today']->is_active)->count() / count($classes) * 100) : 0); ?>%</div>
                <div class="text-sm font-semibold uppercase tracking-wider z-10">Active %</div>
            </div>
        </div>
        <script>const faScript=document.createElement('script');faScript.src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js';faScript.crossOrigin='anonymous';document.head.appendChild(faScript);</script>
        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl shadow border border-green-100 p-6 mb-8 flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex-1 flex gap-2">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search classes..." class="block w-full pl-12 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                </div>
            </div>
            <div class="flex gap-2 flex-1">
                <select id="statusFilter" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="ended">Ended</option>
                </select>
                <select id="levelFilter" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    <option value="">All Levels</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="300">300</option>
                    <option value="400">400</option>
                    <option value="500">500</option>
                </select>
            </div>
        </div>
        <?php if(session('success')): ?>
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded text-center font-semibold">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-green-200 rounded-xl overflow-hidden">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-bold text-green-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-green-700 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-green-700 uppercase tracking-wider">Level</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-green-700 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-green-700 uppercase tracking-wider">Attendance Today</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-green-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-green-100">
                    <?php $rowNum = 1; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-green-100 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-lg text-green-900 font-semibold"><?php echo e($rowNum++); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg text-green-900 font-semibold"><?php echo e($class['code']); ?> - <?php echo e($class['name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg text-green-800"><?php echo e($class['level']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg text-green-800"><?php echo e($class['student_count']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($class['session_today'] && $class['session_today']->is_active): ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-bold bg-green-100 text-green-800 shadow">Active</span>
                                <?php elseif($class['session_today'] && !$class['session_today']->is_active): ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-bold bg-red-100 text-red-700 shadow">Ended</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-bold bg-gray-100 text-gray-600">Not started</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($class['student_count'] > 0): ?>
                                    <?php if($class['session_today'] && $class['session_today']->is_active): ?>
                                        <a href="<?php echo e(route('lecturer.attendance.session', ['classId' => $class['id']])); ?>" class="px-5 py-2 bg-purple-600 text-white text-base rounded-lg font-bold shadow hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400 transition">Continue</a>
                                        <form action="<?php echo e(route('lecturer.attendance.end', ['sessionId' => $class['session_today']->id])); ?>" method="POST" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-5 py-2 bg-red-600 text-white text-base rounded-lg font-bold shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition ml-2">Stop Attendance</button>
                                        </form>
                                    <?php elseif(!$class['session_today'] || ($class['session_today'] && !$class['session_today']->is_active)): ?>
                                        <form action="<?php echo e(route('lecturer.attendance.start', ['classId' => $class['id']])); ?>" method="POST" style="display:inline;" class="start-attendance-form">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-5 py-2 bg-green-600 text-white text-base rounded-lg font-bold shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition">Start Attendance</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-base text-gray-400">No students</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-gray-400 py-8 text-lg">No classes found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

    <!-- Start Session Modal -->
    <div id="startSessionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Start Attendance Session</h3>
                <form id="startSessionForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="class_id" id="modal_class_id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Venue (Optional)</label>
                            <select name="venue_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">No specific venue (Anywhere)</option>
                                <?php $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($venue->id); ?>"><?php echo e($venue->name); ?> (<?php echo e($venue->radius); ?>km)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">If selected, students must be within range.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Minutes)</label>
                            <input type="number" name="duration" value="60" min="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Session will auto-close after this time.</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('startSessionModal').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Start Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notification function
        function showNotification(message, type = 'success') {
            let notif = document.createElement('div');
            notif.textContent = message;
            notif.style.position = 'fixed';
            notif.style.bottom = '2rem';
            notif.style.right = '2rem';
            notif.style.background = type === 'success' ? '#059669' : '#dc2626';
            notif.style.color = 'white';
            notif.style.padding = '1rem 2rem';
            notif.style.borderRadius = '0.5rem';
            notif.style.zIndex = 9999;
            document.body.appendChild(notif);
            setTimeout(() => { document.body.removeChild(notif); }, 3500);
        }

        // Handle Start Attendance Buttons
        const startButtons = document.querySelectorAll('.start-attendance-btn');
        const modal = document.getElementById('startSessionModal');
        const form = document.getElementById('startSessionForm');

        // Intercept form submissions for start buttons
        document.querySelectorAll('.start-attendance-form').forEach(formEl => {
            formEl.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = this.getAttribute('action');
                form.setAttribute('action', action);
                modal.classList.remove('hidden');
            });
        });

        // Handle Modal Submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Starting...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(new FormData(this)).toString()
            })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.status === 'success') {
                    showNotification('Attendance session started!', 'success');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showNotification(data.message || 'Failed to start attendance.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(() => {
                showNotification('Failed to start attendance.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\attendance.blade.php ENDPATH**/ ?>