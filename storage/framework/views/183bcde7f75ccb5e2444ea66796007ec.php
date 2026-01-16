

<?php $__env->startSection('header', 'Eligibility Configuration'); ?>

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

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="eligibilityConfigApp()">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-white shadow-lg rounded-xl border-l-4 border-green-500 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-green-800 break-words">Eligibility Configuration</h1>
                    <p class="mt-2 text-xs sm:text-sm text-green-600 font-medium">Configure eligibility rules and thresholds</p>
                </div>
                <div class="flex flex-shrink-0">
                    <button @click="saveConfiguration()" 
                            class="bg-green-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-green-700 transition-colors shadow-lg text-xs sm:text-sm w-full sm:w-auto">
                        <span>Save Configuration</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Attendance Threshold Configuration -->
        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-blue-500">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2  жест 2 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Attendance Threshold
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Default Threshold (%)
                    </label>
                    <input type="number" 
                           x-model="config.threshold" 
                           min="0" 
                           max="100" 
                           step="0.5"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Minimum attendance percentage required for eligibility</p>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Current Setting:</strong> Students must attend at least 
                        <span class="font-bold text-blue-600" x-text="config.threshold + '%'"></span> 
                        of classes to be eligible for exams.
                    </p>
                </div>
            </div>
        </div>

        <!-- Semester Configuration -->
        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-purple-500">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Academic Calendar
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Semester</label>
                    <select x-model="config.semester" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="First">First Semester</option>
                        <option value="Second">Second Semester</option>
                        sui option value="Summer">Summer Semester</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Academic Year</label>
                    <input type="text" 
                           x-model="config.academicYear" 
                           placeholder="2025/2026"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <p class="mt-1 text-xs text-gray-500">Format: YYYY/YYYY</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Eligibility Calculation Rules -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-green-500">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Calculation Rules
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Attendance Calculation</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Calculated per course for each student
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Based on total present vs total sessions
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Unique per student-course-semester-year
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-700 mb-2">Eligibility Status</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Eligible: Attendance >= threshold
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Ineligible: Attendance < threshold
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Overridden: Manually changed by HOD
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-orange-500">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Run Eligibility Calculation
        </h3>

        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-orange-800">
                <strong>⚠️ Attention:</strong> Running eligibility calculation requires two-factor authentication. 
                This will process all students in your department and create eligibility records.
            </p>
        </div>

        <button @click="runCalculation()" 
                class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors font-semibold shadow-lg">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h..paramsM15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Calculate Eligibility (Requires 2FA)
        </button>
    </div>
</div>

<script>
function eligibilityConfigApp() {
    return {
        config: {
            threshold: 75.0,
            semester: 'First',
            academicYear: '2025/2026'
        },
        
        async saveConfiguration() {
            try {
                const response = await fetch('/hod/exam/api/configuration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.config)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    window.showNotificationModal = window.showNotificationModal || function(type, title, message) {
                        const modal = document.createElement('div');
                        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center';
                        modal.innerHTML = `
                            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                                <div class="flex items-center mb-4 ${type === 'success' ? 'text-green-600' : 'text-red-600'}">
                                    <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>'}
                                    </svg>
                                    <h3 class="text-lg font-medium">${title}</h3>
                                </div>
                                <p class="text-sm text-gray-700 mb-4">${message}</p>
                                <button onclick="this.closest('div').remove()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">OK</button>
                            </div>
                        `;
                        document.body.appendChild(modal);
                        setTimeout(() => modal.remove(), 3000);
                    };
                    showNotificationModal('success', 'Success', 'Configuration saved successfully!');
                } else {
                    showNotificationModal('error', 'Error', 'Error: ' + data.message);
                }
            } catch (error) {
                showNotificationModal('error', 'Error', 'Error saving configuration: ' + error.message);
            }
        },
        
        async runCalculation() {
            if (!confirm('Are you sure you want to run eligibility calculation? This will process all students in your department.')) {
                return;
            }
            
            try {
                const response = await fetch('/hod/exam/api/eligibility/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.config)
                });
                
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") === -1) {
                    // Server returned HTML (redirect), handle it
                    window.location.href = '/hod/two-factor/verify';
                    return;
                }
                
                const data = await response.json();
                
                if (data.requires_2fa) {
                    // Redirect to 2FA page
                    window.location.href = data.redirect || '/hod/two-factor/verify';
                } else if (response.ok && data.success) {
                    showNotificationModal('success', 'Calculation Complete', 'Calculation completed successfully! Eligible: ' + data.data.eligible + ', Ineligible: ' + data.data.ineligible);
                    setTimeout(() => {
                        window.location.href = '/hod/exam/eligibility';
                    }, 2000);
                } else {
                    showNotificationModal('error', 'Error', 'Error: ' + data.message);
                }
            } catch (error) {
                showNotificationModal('error', 'Error', 'Error running calculation: ' + error.message);
            }
        }
    }
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('hod.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\exam\configuration.blade.php ENDPATH**/ ?>