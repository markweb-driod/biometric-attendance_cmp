

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo e(route('lecturer.grading.index')); ?>" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Back to Classrooms
        </a>
        <h1 class="text-3xl font-bold text-gray-800"><?php echo e($classroom->class_name); ?> - Grading</h1>
        <p class="text-gray-600 mt-2"><?php echo e($classroom->course->course_name ?? 'N/A'); ?></p>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Grade Distribution Chart -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Grade Distribution</h2>
        <div class="max-w-md mx-auto">
            <canvas id="gradeChart"></canvas>
        </div>
    </div>

    <!-- Grading Configuration -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Grading Configuration</h2>
        <form action="<?php echo e(route('lecturer.grading.updateRules', $classroom->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade => $threshold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Grade <?php echo e($grade); ?></label>
                        <div class="flex items-center">
                            <input type="number" 
                                   name="rules[<?php echo e($grade); ?>]" 
                                   value="<?php echo e($threshold); ?>" 
                                   min="0" 
                                   max="100" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="ml-2 text-gray-600">%</span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors">
                Update Rules
            </button>
        </form>
    </div>

    <!-- Bulk Actions Toolbar -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-4" id="bulkActionsToolbar" style="display: none;">
        <div class="flex items-center justify-between">
            <span class="text-gray-700 font-medium"><span id="selectedCount">0</span> student(s) selected</span>
            <div class="space-x-2">
                <button onclick="openOverrideModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md transition-colors">
                    Override Grades
                </button>
                <button onclick="sendNotifications()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition-colors">
                    Send Notifications
                </button>
            </div>
        </div>
    </div>

    <!-- Student Grades Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Student Grades</h2>
            <div class="space-x-2">
                <a href="<?php echo e(route('lecturer.grading.export', $classroom->id)); ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors inline-block">
                    Export CSV
                </a>
                <a href="<?php echo e(route('lecturer.grading.exportPdf', $classroom->id)); ?>" 
                   target="_blank"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors inline-block">
                    Print PDF
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matric Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attended</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="student-checkbox rounded" value="<?php echo e($item['student']->id); ?>" onchange="updateBulkActions()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item['student']->matric_number); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item['student']->user->full_name ?? 'N/A'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item['attendance']['total_sessions']); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo e($item['attendance']['total_present']); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?php if($item['attendance']['percentage'] >= 75): ?> bg-green-100 text-green-800
                                    <?php elseif($item['attendance']['percentage'] >= 60): ?> bg-blue-100 text-blue-800
                                    <?php elseif($item['attendance']['percentage'] >= 50): ?> bg-yellow-100 text-yellow-800
                                    <?php else: ?> bg-red-100 text-red-800
                                    <?php endif; ?>">
                                    <?php echo e($item['attendance']['percentage']); ?>%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold
                                <?php if($item['grade'] == 'A'): ?> text-green-600
                                <?php elseif($item['grade'] == 'B'): ?> text-blue-600
                                <?php elseif($item['grade'] == 'C'): ?> text-yellow-600
                                <?php elseif($item['grade'] == 'D'): ?> text-orange-600
                                <?php else: ?> text-red-600
                                <?php endif; ?>">
                                <?php echo e($item['grade']); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No students found in this classroom
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Grade Override Modal -->
<div id="overrideModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Override Grades</h3>
        <form action="<?php echo e(route('lecturer.grading.bulkOverride', $classroom->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="student_ids" id="overrideStudentIds">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Grade</label>
                <select name="grade" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Grade</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="F">F</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Required)</label>
                <textarea name="reason" required rows="3" maxlength="500" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Explain why you are overriding these grades..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeOverrideModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md transition-colors">
                    Override Grades
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Load grade distribution chart
fetch('<?php echo e(route('lecturer.grading.distribution', $classroom->id)); ?>')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('gradeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Number of Students',
                    data: data.data,
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.7)',   // Green for A
                        'rgba(59, 130, 246, 0.7)',  // Blue for B
                        'rgba(234, 179, 8, 0.7)',   // Yellow for C
                        'rgba(249, 115, 22, 0.7)',  // Orange for D
                        'rgba(239, 68, 68, 0.7)'    // Red for F
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(249, 115, 22, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: `Total Students: ${data.total}`
                    }
                }
            }
        });
    });

// Bulk actions
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.student-checkbox:checked');
    const count = checkboxes.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActionsToolbar').style.display = count > 0 ? 'block' : 'none';
}

function openOverrideModal() {
    const checkboxes = document.querySelectorAll('.student-checkbox:checked');
    const studentIds = Array.from(checkboxes).map(cb => cb.value);
    document.getElementById('overrideStudentIds').value = JSON.stringify(studentIds);
    document.getElementById('overrideModal').classList.remove('hidden');
    document.getElementById('overrideModal').classList.add('flex');
}

function closeOverrideModal() {
    document.getElementById('overrideModal').classList.add('hidden');
    document.getElementById('overrideModal').classList.remove('flex');
}

function sendNotifications() {
    const checkboxes = document.querySelectorAll('.student-checkbox:checked');
    const studentIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (confirm(`Send grade notifications to ${studentIds.length} student(s)?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route('lecturer.grading.notify', $classroom->id)); ?>';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrf);
        
        studentIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\grading\show.blade.php ENDPATH**/ ?>