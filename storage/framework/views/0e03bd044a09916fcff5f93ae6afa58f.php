<?php $__env->startSection('header', 'Exam Eligibility Management'); ?>

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

<?php if(session('execute_waiver')): ?>
<script>
    // Auto-execute waiver after 2FA verification
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?php echo e(route("hod.exam.api.eligibility.execute-waiver")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                if (window.examEligibilityApp && window.examEligibilityApp.showNotificationModal) {
                    window.examEligibilityApp.showNotificationModal('success', 'Waiver Complete', result.message || 'Eligibility requirement waived successfully.');
                } else {
                    alert('Success: ' + (result.message || 'Eligibility requirement waived successfully.'));
                }
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                if (window.examEligibilityApp && window.examEligibilityApp.showNotificationModal) {
                    window.examEligibilityApp.showNotificationModal('error', 'Error', result.message || 'Failed to execute waiver.');
                } else {
                    alert('Error: ' + (result.message || 'Failed to execute waiver.'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error executing waiver: ' + error.message);
        });
    });
</script>
<?php endif; ?>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="examEligibilityApp()">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-white shadow-lg rounded-xl border-l-4 border-green-500 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-green-800 break-words" style="font-family: 'Montserrat', sans-serif;">Exam Eligibility Management</h1>
                    <p class="mt-2 text-xs sm:text-sm text-green-600 font-medium">Manage student exam eligibility based on attendance records</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:space-x-3 sm:flex-shrink-0">
                    <button @click="calculateEligibility()" 
                            class="bg-green-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-lg text-xs sm:text-sm">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span>Calculate Eligibility</span>
                    </button>
                    <button @click="exportData()" 
                            class="bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-green-800 transition-all duration-200 shadow-lg text-xs sm:text-sm">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Export Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-green-200">
        <h3 class="text-lg font-semibold text-green-800 mb-4" style="font-family: 'Montserrat', sans-serif;">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Semester</label>
                <select x-model="filters.semester" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Semesters</option>
                    <?php $__currentLoopData = $filterOptions['semesters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($semester); ?>"><?php echo e($semester); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Academic Year</label>
                <select x-model="filters.academic_year" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Years</option>
                    <?php $__currentLoopData = $filterOptions['academic_years']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Course</label>
                <select x-model="filters.course_id" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Courses</option>
                    <?php $__currentLoopData = $filterOptions['courses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>"><?php echo e($course->code); ?> - <?php echo e($course->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Statuses</option>
                    <?php $__currentLoopData = $filterOptions['statuses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex space-x-4">
                <div class="flex-1">
                    <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()" 
                           placeholder="Search by student name or matric number..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <button @click="clearFilters()" 
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all duration-200 shadow-lg">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Eligible Students</dt>
                            <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.eligible"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-600">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Ineligible Students</dt>
                            <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.ineligible"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-400">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-400 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Overridden</dt>
                            <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.overridden"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-700 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Average Attendance</dt>
                            <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.average_attendance + '%'"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- At-Risk Students Alert -->
    <div x-show="atRiskStudents.length > 0" class="mb-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">At-Risk Students</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>You have <span x-text="atRiskStudents.length"></span> students with attendance below the required threshold.</p>
                        <button @click="showAtRiskModal = true" class="mt-2 text-red-600 hover:text-red-500 font-medium">
                            View Details →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Eligibility Data Table -->
    <div class="bg-white shadow-lg rounded-xl border-l-4 border-green-500">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Exam Eligibility Records</h3>
        </div>
        <div class="overflow-x-auto">
            <!-- Empty State -->
            <div x-show="!eligibilityData || eligibilityData.length === 0" class="p-12 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-xl font-semibold text-gray-900">No Eligibility Records Found</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        No exam eligibility records have been calculated yet for the selected filters.
                    </p>
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-4">To generate eligibility records:</p>
                        <ol class="text-left text-sm text-gray-600 max-w-xs mx-auto space-y-2">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-green-100 text-green-600 rounded-full text-xs font-semibold mr-3 mt-0.5">1</span>
                                <span>Ensure students have attendance records</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-green-100 text-green-600 rounded-full text-xs font-semibold mr-3 mt-0.5">2</span>
                                <span>Navigate to Eligibility Configuration</span>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-green-100 text-green-600 rounded-full text-xs font-semibold mr-3 mt-0.5">3</span>
                                <span>Click "Calculate Eligibility" (requires 2FA)</span>
                            </li>
                        </ol>
                        <div class="mt-6">
                            <a href="<?php echo e(route('hod.exam.eligibility.configuration')); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin=" القديرound" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.我们将c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
 duas
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Go to Configuration
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Records Table -->
            <div x-show="eligibilityData && eligibilityData.length > 0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="record in eligibilityData" :key="record.id">
                            <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="record.student.full_name"></div>
                                    <div class="text-sm text-gray-500" x-text="record.student.matric_number"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900" x-text="record.course.code"></div>
                                    <div class="text-sm text-gray-500" x-text="record.course.name"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="record.semester"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900" x-text="record.attendance_percentage + '%'"></div>
                                    <div class="ml-2 text-xs text-gray-500">/ <span x-text="record.required_threshold + '%'"></span></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': record.status === 'eligible',
                                          'bg-red-100 text-red-800': record.status === 'ineligible',
                                          'bg-yellow-100 text-yellow-800': record.status === 'overridden'
                                      }"
                                      x-text="record.status.charAt(0).toUpperCase() + record.status.slice(1)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openOverrideModal(record)" 
                                        class="text-green-600 hover:text-green-900 mr-3">
                                    Override
                                </button>
                                <button @click="openWaiverModal(record)" 
                                        class="text-orange-600 hover:text-orange-900 mr-3"
                                        :class="record.status === 'eligible' ? 'opacity-50 cursor-not-allowed' : ''"
                                        :disabled="record.status === 'eligible'"
                                        title="Waive eligibility requirement (Requires 2FA)">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Waive
                                </button>
                                <button @click="viewDetails(record)" 
                                        class="text-blue-600 hover:text-blue-900">
                                    Details
                                </button>
                            </td>
                        </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Override Modal -->
    <div x-show="showOverrideModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Override Exam Eligibility</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <div class="text-sm text-gray-900" x-text="selectedRecord ? selectedRecord.student.full_name : ''"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <div class="text-sm text-gray-900" x-text="selectedRecord ? selectedRecord.course.code + ' - ' + selectedRecord.course.name : ''"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select x-model="overrideForm.status" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="eligible">Eligible</option>
                            <option value="ineligible">Ineligible</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <textarea x-model="overrideForm.reason" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Enter reason for override..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button @click="showOverrideModal = false" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button @click="submitOverride()" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Submit Override
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div x-show="showNotification" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.away="showNotification = false">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center mb-4" :class="notificationType === 'success' ? 'text-green-600' : 'text-red-600'">
                    <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="notificationType === 'success'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="notificationType === 'error'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="text-lg font-medium" x-text="notificationTitle"></h3>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-700" x-text="notificationMessage"></p>
                </div>
                <div class="flex justify-end">
                    <button @click="showNotification = false" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Waiver Modal -->
    <div x-show="showWaiverModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-orange-900">Waive Eligibility Requirement (Requires 2FA)</h3>
                    <button @click="showWaiverModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-orange-800">
                        <strong>Important:</strong> Waiving eligibility requirements requires two-factor authentication verification. This action will permanently mark the student as eligible regardless of attendance records.
                    </p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <div class="text-sm text-gray-900" x-text="selectedRecord ? selectedRecord.student.full_name + ' (' + selectedRecord.student.matric_number + ')' : ''"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <div class="text-sm text-gray-900" x-text="selectedRecord ? selectedRecord.course.code + ' - ' + selectedRecord.course.name : ''"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                        <div class="text-sm" 
                             :class="selectedRecord && selectedRecord.status === 'eligible' ? 'text-green-600' : 'text-red-600'"
                             x-text="selectedRecord ? (selectedRecord.status.charAt(0).toUpperCase() + selectedRecord.status.slice(1)) : ''"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Waiver <span class="text-red-500">*</span></label>
                        <textarea x-model="waiverForm.reason" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                  placeholder="Provide detailed reason for waiving eligibility requirement (e.g., medical emergency, approved absence, special circumstances)..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimum 50 characters required</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Documentation Reference (Optional)</label>
                        <input type="text" x-model="waiverForm.document_ref" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                               placeholder="Reference number, document ID, etc.">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button @click="showWaiverModal = false" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button @click="submitWaiver()" 
                            class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Proceed to 2FA Verification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium" x-text="loadingMessage"></span>
        </div>
    </div>
</div>

<script>
function examEligibilityApp() {
    return {
        filters: {
            semester: '<?php echo e($filters["semester"] ?? ""); ?>',
            academic_year: '<?php echo e($filters["academic_year"] ?? ""); ?>',
            course_id: '<?php echo e($filters["course_id"] ?? ""); ?>',
            status: '<?php echo e($filters["status"] ?? ""); ?>',
            search: '<?php echo e($filters["search"] ?? ""); ?>'
        },
        eligibilityData: <?php echo json_encode($eligibilityData, 15, 512) ?>,
        stats: <?php echo json_encode($eligibilityStats, 15, 512) ?>,
        atRiskStudents: <?php echo json_encode($atRiskStudents, 15, 512) ?>,
        showOverrideModal: false,
        showWaiverModal: false,
        showNotification: false,
        notificationType: 'success',
        notificationTitle: '',
        notificationMessage: '',
        selectedRecord: null,
        overrideForm: {
            status: 'eligible',
            reason: ''
        },
        waiverForm: {
            reason: '',
            document_ref: ''
        },
        loading: false,
        loadingMessage: 'Loading...',

        applyFilters() {
            this.loading = true;
            this.loadingMessage = 'Applying filters...';
            
            // Reload page with new filters
            const params = new URLSearchParams();
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key]) {
                    params.append(key, this.filters[key]);
                }
            });
            
            window.location.href = '<?php echo e(route("hod.exam.eligibility")); ?>?' + params.toString();
        },

        clearFilters() {
            this.filters = {
                semester: '',
                academic_year: '',
                course_id: '',
                status: '',
                search: ''
            };
            this.applyFilters();
        },

        openOverrideModal(record) {
            this.selectedRecord = record;
            this.overrideForm = {
                status: record.status === 'eligible' ? 'ineligible' : 'eligible',
                reason: ''
            };
            this.showOverrideModal = true;
        },

        showNotificationModal(type, title, message) {
            this.notificationType = type;
            this.notificationTitle = title;
            this.notificationMessage = message;
            this.showNotification = true;
        },

        openWaiverModal(record) {
            if (record.status === 'eligible') {
                this.showNotificationModal('error', 'Invalid Action', 'Student is already eligible. Waiver is only for ineligible students.');
                return;
            }
            this.selectedRecord = record;
            this.waiverForm = {
                reason: '',
                document_ref: ''
            };
            this.showWaiverModal = true;
        },

        async submitWaiver() {
            if (!this.waiverForm.reason.trim() || this.waiverForm.reason.trim().length < 50) {
                this.showNotificationModal('error', 'Validation Error', 'Please provide a detailed reason (minimum 50 characters) for the waiver.');
                return;
            }

            // Store waiver data in session and redirect to 2FA
            try {
                const response = await fetch('<?php echo e(route("hod.exam.api.eligibility.prepare-waiver")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        student_id: this.selectedRecord.student_id,
                        course_id: this.selectedRecord.course_id,
                        semester: this.selectedRecord.semester,
                        academic_year: this.selectedRecord.academic_year,
                        reason: this.waiverForm.reason,
                        document_ref: this.waiverForm.document_ref || null
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Redirect to 2FA page with waiver intent
                    window.location.href = result.redirect_url;
                } else {
                    this.showNotificationModal('error', 'Error', result.message || 'Failed to prepare waiver request.');
                }
            } catch (error) {
                this.showNotificationModal('error', 'Error', 'Error preparing waiver: ' + error.message);
            }
        },

        async submitOverride() {
            if (!this.overrideForm.reason.trim()) {
                this.showNotificationModal('error', 'Validation Error', 'Please provide a reason for the override.');
                return;
            }

            this.loading = true;
            this.loadingMessage = 'Submitting override...';

            try {
                const response = await fetch('<?php echo e(route("hod.exam.api.eligibility.override")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        student_id: this.selectedRecord.student_id,
                        course_id: this.selectedRecord.course_id,
                        semester: this.selectedRecord.semester,
                        academic_year: this.selectedRecord.academic_year,
                        override_reason: this.overrideForm.reason,
                        status: this.overrideForm.status
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showOverrideModal = false;
                    this.showNotificationModal('success', 'Success', 'Eligibility override applied successfully.');
                    setTimeout(() => {
                        this.applyFilters(); // Reload data
                    }, 1500);
                } else {
                    this.showNotificationModal('error', 'Error', result.message || 'Failed to apply override.');
                }
            } catch (error) {
                this.showNotificationModal('error', 'Error', 'Error submitting override: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async calculateEligibility() {
            this.loading = true;
            this.loadingMessage = 'Calculating eligibility...';

            try {
                const response = await fetch('<?php echo e(route("hod.exam.api.eligibility.calculate")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        semester: this.filters.semester || 'First Semester',
                        academic_year: this.filters.academic_year || '2024/2025'
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showNotificationModal('success', 'Calculation Complete', 
                        'Eligibility calculation completed successfully! Eligible: ' + (result.data?.eligible || 0) + ', Ineligible: ' + (result.data?.ineligible || 0));
                    setTimeout(() => {
                        this.applyFilters(); // Reload data
                    }, 2000);
                } else {
                    if (result.requires_2fa) {
                        window.location.href = result.redirect || '<?php echo e(route("hod.two-factor.show")); ?>';
                    } else {
                        this.showNotificationModal('error', 'Error', result.message || 'Failed to calculate eligibility.');
                    }
                }
            } catch (error) {
                this.showNotificationModal('error', 'Error', 'Error calculating eligibility: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        async exportData() {
            this.loading = true;
            this.loadingMessage = 'Exporting data...';

            try {
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });

                const response = await fetch('<?php echo e(route("hod.exam.api.eligibility.export")); ?>?' + params.toString());
                const blob = await response.blob();
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'exam_eligibility_' + new Date().toISOString().split('T')[0] + '.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                this.showNotificationModal('success', 'Export Complete', 'Data exported successfully.');
            } catch (error) {
                this.showNotificationModal('error', 'Export Error', 'Error exporting data: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        viewDetails(record) {
            // Implement view details functionality
            console.log('View details for:', record);
        }
    }
}
</script>
<?php $__env->stopSection(); ?>







<?php echo $__env->make('hod.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\exam\eligibility.blade.php ENDPATH**/ ?>