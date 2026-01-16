

<?php $__env->startSection('title', 'API Key Logs'); ?>
<?php $__env->startSection('page-title', 'API Key Logs — ' . $apiKey->name); ?>

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

<div class="w-full px-2 py-10 space-y-6">
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Logs for <?php echo e($apiKey->name); ?></h2>
            <a href="<?php echo e(route('superadmin.api-keys.show', $apiKey->id)); ?>" class="px-4 py-2 bg-blue-200 text-blue-800 rounded-lg hover:bg-blue-300">Back to Details</a>
        </div>
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full px-2 py-1 border rounded">
                    <option value="">Any</option>
                    <option value="success" <?php echo e(request('status')==='success' ? 'selected' : ''); ?>>Success</option>
                    <option value="error" <?php echo e(request('status')==='error' ? 'selected' : ''); ?>>Error</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Endpoint</label>
                <input type="text" name="endpoint" placeholder="/api/endpoint" class="w-full px-2 py-1 border rounded" value="<?php echo e(request('endpoint')); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">From</label>
                <input type="date" name="from" class="w-full px-2 py-1 border rounded" value="<?php echo e(request('from')); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">To</label>
                <input type="date" name="to" class="w-full px-2 py-1 border rounded" value="<?php echo e(request('to')); ?>">
            </div>
            <div class="md:col-span-4 flex gap-2 items-end mt-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Filter</button>
                <a href="?" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Reset</a>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Endpoint</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Latency</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                        <td class="px-3 py-2 whitespace-nowrap"><?php echo e($log->endpoint); ?></td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($log->is_success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo e($log->is_success ? 'Success' : 'Error'); ?>

                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap"><?php echo e($log->response_code); ?></td>
                        <td class="px-3 py-2 whitespace-nowrap"><?php echo e($log->ip_address ?? '-'); ?></td>
                        <td class="px-3 py-2 whitespace-nowrap"><?php echo e($log->latency_ms); ?> ms</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-4">No logs found for this filter.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <?php echo e($logs->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\api-keys\logs.blade.php ENDPATH**/ ?>