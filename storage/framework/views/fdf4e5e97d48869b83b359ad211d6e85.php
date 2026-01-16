

<?php $__env->startSection('title', 'Audit Logs & Compliance'); ?>

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

<div x-data="auditLogsApp()" class="space-y-6">
<!-- Modern Hero Section -->
<div class="relative overflow-hidden bg-green-600 rounded-3xl shadow-2xl mb-6">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full -translate-y-48 translate-x-48"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white opacity-5 rounded-full translate-y-40 -translate-x-40"></div>
    
    <div class="relative px-6 py-8">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-white to-blue-100 bg-clip-text ">
                    Audit Logs & Compliance
                </h1>
                <p class="text-xl text-blue-100 mb-6">Monitor system activities and ensure compliance with policies</p>
            </div>
            <div class="hidden lg:block">
                <div class="flex space-x-3">
                    <button onclick="generateComplianceReport()" 
                            class="group relative px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-2xl hover:bg-opacity-30 transition-all duration-300 hover:scale-105 shadow-xl">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-semibold">Compliance Report</span>
                        </div>
                    </button>
                    <button onclick="exportData()" 
                            class="group relative px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-2xl hover:bg-opacity-30 transition-all duration-300 hover:scale-105 shadow-xl">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-semibold">Export Data</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-green-200">
    <h3 class="text-lg font-semibold text-green-800 mb-4" style="font-family: 'Montserrat', sans-serif;">Filters</h3>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Action</label>
            <select x-model="filters.action" @change="applyFilters()" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Actions</option>
                <?php $__currentLoopData = $filterOptions['actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($action); ?>"><?php echo e(ucfirst($action)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Resource Type</label>
            <select x-model="filters.resource_type" @change="applyFilters()" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Resources</option>
                <?php $__currentLoopData = $filterOptions['resource_types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>"><?php echo e(ucfirst($type)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Severity</label>
            <select x-model="filters.severity" @change="applyFilters()" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Severities</option>
                <?php $__currentLoopData = $filterOptions['severities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $severity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($severity); ?>"><?php echo e(ucfirst($severity)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">User Type</label>
            <select x-model="filters.user_type" @change="applyFilters()" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Users</option>
                <?php $__currentLoopData = $filterOptions['user_types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>"><?php echo e(ucfirst($type)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date Range</label>
            <div class="flex space-x-2">
                <input type="date" x-model="filters.date_from" @change="applyFilters()" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <input type="date" x-model="filters.date_to" @change="applyFilters()" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
        </div>
    </div>
    <div class="mt-4">
        <div class="flex space-x-4">
            <div class="flex-1">
                <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()" 
                       placeholder="Search in descriptions, actions, or resource types..."
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
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Total Events</dt>
                        <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.total"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-red-500 hover:shadow-xl transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Critical</dt>
                        <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.critical"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">High</dt>
                        <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.high"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-yellow-500 hover:shadow-xl transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Medium</dt>
                        <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.medium"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500 hover:shadow-xl transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Low</dt>
                        <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;" x-text="stats.low"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Security Alerts -->
<div x-show="securityAlerts.length > 0" class="mb-6">
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 shadow-lg">
        <div class="flex">
            <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-lg">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-red-800" style="font-family: 'Montserrat', sans-serif;">Security Alerts</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>You have <span class="font-bold" x-text="securityAlerts.length"></span> high-priority security events that require attention.</p>
                    <button @click="showSecurityAlerts = true" class="mt-3 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-all duration-200">
                        View Details
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="bg-white shadow-lg rounded-xl">
    <div class="px-6 py-4 border-b border-green-200 bg-green-50">
        <h3 class="text-lg font-semibold text-green-800" style="font-family: 'Montserrat', sans-serif;">Audit Logs</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">Date/Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">Resource</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">Severity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider" style="font-family: 'Montserrat', sans-serif;">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="log in auditLogs" :key="log.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="new Date(log.created_at).toLocaleString()"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" x-text="log.action.charAt(0).toUpperCase() + log.action.slice(1)"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div x-text="log.resource_type.charAt(0).toUpperCase() + log.resource_type.slice(1)"></div>
                            <div class="text-xs text-gray-500" x-text="log.resource_id ? 'ID: ' + log.resource_id : ''"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div x-text="log.user_type.charAt(0).toUpperCase() + log.user_type.slice(1)"></div>
                            <div class="text-xs text-gray-500" x-text="'ID: ' + log.user_id"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="{
                                      'bg-red-100 text-red-800': log.severity === 'critical',
                                      'bg-orange-100 text-orange-800': log.severity === 'high',
                                      'bg-yellow-100 text-yellow-800': log.severity === 'medium',
                                      'bg-green-100 text-green-800': log.severity === 'low'
                                  }"
                                  x-text="log.severity.charAt(0).toUpperCase() + log.severity.slice(1)"></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate" x-text="log.description"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="log.ip_address"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button @click="viewDetails(log.id)" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-md hover:bg-green-100 transition text-xs font-medium">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </button>
                        </td>
                    </tr>
                </template>
                <tr x-show="auditLogs.length === 0">
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">No audit logs found</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="bg-green-50 px-6 py-4 border-t border-green-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing <span x-text="pagination.from"></span> to <span x-text="pagination.to"></span> of <span x-text="pagination.total"></span> results
            </div>
            <div class="flex items-center space-x-2">
                <button @click="changePage(pagination.current_page - 1)" 
                        :disabled="!pagination.prev_page_url"
                        :class="{'opacity-50 cursor-not-allowed': !pagination.prev_page_url, 'hover:bg-green-100': pagination.prev_page_url}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition">
                    Previous
                </button>
                <span class="text-sm text-gray-700">
                    Page <span x-text="pagination.current_page"></span> of <span x-text="pagination.last_page"></span>
                </span>
                <button @click="changePage(pagination.current_page + 1)"
                        :disabled="!pagination.next_page_url"
                        :class="{'opacity-50 cursor-not-allowed': !pagination.next_page_url, 'hover:bg-green-100': pagination.next_page_url}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 transition">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div x-show="showDetailsModal" 
     x-cloak
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
     @click.away="closeDetailsModal()">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-3xl shadow-xl rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Activity Details</h3>
            <button @click="closeDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div x-show="loadingDetails" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-green-600 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-gray-600">Loading details...</p>
        </div>
        <div x-show="!loadingDetails && logDetails" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">Date & Time</label>
                    <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? new Date(logDetails.created_at).toLocaleString() : ''"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">Action</label>
                    <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? (logDetails.action.charAt(0).toUpperCase() + logDetails.action.slice(1)) : ''"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">Resource Type</label>
                    <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? (logDetails.resource_type.charAt(0).toUpperCase() + logDetails.resource_type.slice(1)) : ''"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">Resource ID</label>
                    <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? (logDetails.resource_id || 'N/A') : ''"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">User Type</label>
                    <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? (logDetails.user_type.charAt(0).toUpperCase() + logDetails.user_type.slice(1)) : ''"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">User ID</label>
                    <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? logDetails.user_id : ''"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">Severity</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1"
                          :class="{
                              'bg-red-100 text-red-800': logDetails && logDetails.severity === 'critical',
                              'bg-orange-100 text-orange-800': logDetails && logDetails.severity === 'high',
                              'bg-yellow-100 text-yellow-800': logDetails && logDetails.severity === 'medium',
                              'bg-green-100 text-green-800': logDetails && logDetails.severity === 'low'
                          }"
                          x-text="logDetails ? (logDetails.severity.charAt(0).toUpperCase() + logDetails.severity.slice(1)) : ''"></span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm font-medium text-gray-500">IP Address</label>
                    <p class="text-sm text-gray-900 mt-1 font-mono" x-text="logDetails ? logDetails.ip_address : ''"></p>
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <label class="text-sm font-medium text-gray-500">Description</label>
                <p class="text-sm text-gray-900 mt-1" x-text="logDetails ? logDetails.description : ''"></p>
            </div>
            <div x-show="logDetails && logDetails.user_agent" class="bg-gray-50 p-4 rounded-lg">
                <label class="text-sm font-medium text-gray-500">User Agent</label>
                <p class="text-xs text-gray-700 mt-1 break-all" x-text="logDetails ? logDetails.user_agent : ''"></p>
            </div>
            <div x-show="logDetails && logDetails.session_id" class="bg-gray-50 p-4 rounded-lg">
                <label class="text-sm font-medium text-gray-500">Session ID</label>
                <p class="text-xs text-gray-700 mt-1 font-mono" x-text="logDetails ? logDetails.session_id : ''"></p>
            </div>
            <div x-show="logDetails && (logDetails.old_values || logDetails.new_values)" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div x-show="logDetails && logDetails.old_values" class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <label class="text-sm font-medium text-red-700">Old Values</label>
                    <pre class="text-xs text-gray-700 mt-2 whitespace-pre-wrap" x-text="logDetails ? JSON.stringify(logDetails.old_values, null, 2) : ''"></pre>
                </div>
                <div x-show="logDetails && logDetails.new_values" class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <label class="text-sm font-medium text-green-700">New Values</label>
                    <pre class="text-xs text-gray-700 mt-2 whitespace-pre-wrap" x-text="logDetails ? JSON.stringify(logDetails.new_values, null, 2) : ''"></pre>
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button @click="closeDetailsModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Close</button>
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
function auditLogsApp() {
    return {
        filters: {
            action: '<?php echo e($filters["action"] ?? ""); ?>',
            resource_type: '<?php echo e($filters["resource_type"] ?? ""); ?>',
            severity: '<?php echo e($filters["severity"] ?? ""); ?>',
            user_type: '<?php echo e($filters["user_type"] ?? ""); ?>',
            date_from: '<?php echo e($filters["date_from"] ?? ""); ?>',
            date_to: '<?php echo e($filters["date_to"] ?? ""); ?>',
            search: '<?php echo e($filters["search"] ?? ""); ?>'
        },
        auditLogs: <?php echo json_encode($auditLogs->items(), 15, 512) ?>,
        pagination: {
            current_page: <?php echo e($auditLogs->currentPage()); ?>,
            last_page: <?php echo e($auditLogs->lastPage()); ?>,
            from: <?php echo e($auditLogs->firstItem() ?? 0); ?>,
            to: <?php echo e($auditLogs->lastItem() ?? 0); ?>,
            total: <?php echo e($auditLogs->total()); ?>,
            prev_page_url: '<?php echo e($auditLogs->previousPageUrl()); ?>',
            next_page_url: '<?php echo e($auditLogs->nextPageUrl()); ?>'
        },
        stats: <?php echo json_encode($auditStats, 15, 512) ?>,
        securityAlerts: <?php echo json_encode($securityAlerts, 15, 512) ?>,
        showSecurityAlerts: false,
        loading: false,
        loadingMessage: 'Loading...',
        showDetailsModal: false,
        logDetails: null,
        loadingDetails: false,

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
            
            // Reset to page 1 when applying filters
            params.append('page', 1);
            params.append('per_page', 8);
            
            window.location.href = '<?php echo e(route("hod.audit.index")); ?>?' + params.toString();
        },

        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            
            const params = new URLSearchParams();
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key]) {
                    params.append(key, this.filters[key]);
                }
            });
            params.append('page', page);
            params.append('per_page', 8);
            
            window.location.href = '<?php echo e(route("hod.audit.index")); ?>?' + params.toString();
        },

        async viewDetails(logId) {
            this.showDetailsModal = true;
            this.loadingDetails = true;
            this.logDetails = null;
            
            try {
                const response = await fetch(`<?php echo e(route("hod.audit.api.logs.details", ":id")); ?>`.replace(':id', logId));
                const result = await response.json();
                
                if (result.success) {
                    this.logDetails = result.data;
                } else {
                    alert('Failed to load log details');
                    this.closeDetailsModal();
                }
            } catch (error) {
                console.error('Error loading log details:', error);
                alert('Error loading log details');
                this.closeDetailsModal();
            } finally {
                this.loadingDetails = false;
            }
        },

        closeDetailsModal() {
            this.showDetailsModal = false;
            this.logDetails = null;
            this.loadingDetails = false;
        },

        clearFilters() {
            this.filters = {
                action: '',
                resource_type: '',
                severity: '',
                user_type: '',
                date_from: '',
                date_to: '',
                search: ''
            };
            this.applyFilters();
        },

        async generateComplianceReport() {
            this.loading = true;
            this.loadingMessage = 'Generating compliance report...';

            try {
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });

                const response = await fetch('<?php echo e(route("hod.audit.api.compliance-report")); ?>?' + params.toString());
                const report = await response.json();
                
                // Display report in a modal or new window
                this.displayComplianceReport(report);
            } catch (error) {
                alert('Error generating compliance report: ' + error.message);
            } finally {
                this.loading = false;
            }
        },

        displayComplianceReport(report) {
            // Create a modal or new window to display the compliance report
            const reportWindow = window.open('', '_blank', 'width=800,height=600');
            reportWindow.document.write(`
                <html>
                <head>
                    <title>Compliance Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { background: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                        .section { margin-bottom: 30px; }
                        .section h3 { color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
                        th { background: #f9fafb; }
                        .score { font-size: 24px; font-weight: bold; color: #059669; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Compliance Report</h1>
                        <p>Generated on: ${new Date().toLocaleString()}</p>
                        <p class="score">Compliance Score: ${report.compliance_score}%</p>
                    </div>
                    
                    <div class="section">
                        <h3>Data Access Compliance</h3>
                        <table>
                            <tr><th>User Type</th><th>Resource Type</th><th>Count</th></tr>
                            ${report.data_access.map(item => 
                                `<tr><td>${item.user_type}</td><td>${item.resource_type}</td><td>${item.count}</td></tr>`
                            ).join('')}
                        </table>
                    </div>
                    
                    <div class="section">
                        <h3>User Activity Summary</h3>
                        <table>
                            <tr><th>User Type</th><th>Total Actions</th></tr>
                            ${report.user_activity.map(item => 
                                `<tr><td>${item.user_type}</td><td>${item.total_actions}</td></tr>`
                            ).join('')}
                        </table>
                    </div>
                    
                    <div class="section">
                        <h3>Security Events</h3>
                        <table>
                            <tr><th>Action</th><th>Severity</th><th>Count</th></tr>
                            ${report.security_events.map(item => 
                                `<tr><td>${item.action}</td><td>${item.severity}</td><td>${item.count}</td></tr>`
                            ).join('')}
                        </table>
                    </div>
                </body>
                </html>
            `);
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

                const response = await fetch('<?php echo e(route("hod.audit.api.export")); ?>?' + params.toString());
                const blob = await response.blob();
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'audit_logs_' + new Date().toISOString().split('T')[0] + '.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                alert('Error exporting data: ' + error.message);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('hod.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\audit\index.blade.php ENDPATH**/ ?>