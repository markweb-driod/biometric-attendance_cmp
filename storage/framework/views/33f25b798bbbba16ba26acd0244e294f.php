<?php $__env->startSection('title', 'Lecturers'); ?>
<?php $__env->startSection('page-title', 'Lecturers'); ?>
<?php $__env->startSection('page-description', 'Manage all lecturers by department'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="preload" href="<?php echo e(asset('css/lecturers-optimized.css')); ?>" as="style">
<link rel="stylesheet" href="<?php echo e(asset('css/lecturers-optimized.css')); ?>">
<?php $__env->stopPush(); ?>

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

<div class="max-w-6xl mx-auto w-full px-2 py-3">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Lecturers</h1>
            <p class="text-sm text-gray-500">Manage and monitor department lecturers</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded shadow p-3 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
            <input id="lectSearch" type="text" placeholder="Search name, staff ID, email" class="px-2 py-1 border rounded text-sm">
            <select id="lectDept" class="px-2 py-1 border rounded text-sm">
                <option value="">All Departments</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->name); ?>"><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button id="lectReset" class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs hover:bg-gray-300">Reset</button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6 max-w-5xl mx-auto w-full">
        <div class="relative bg-white/90 shadow-xl border border-green-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-2xl font-extrabold text-green-700 mb-0.5" id="kpi-lecturers">-</div>
            <div class="text-sm font-semibold text-green-600 tracking-wide">Total</div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-blue-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-2xl font-extrabold text-blue-700 mb-0.5" id="kpi-active">-</div>
            <div class="text-sm font-semibold text-blue-600 tracking-wide">Active</div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-purple-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-2xl font-extrabold text-purple-700 mb-0.5" id="kpi-inactive">-</div>
            <div class="text-sm font-semibold text-purple-600 tracking-wide">Inactive</div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-orange-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-xl font-extrabold text-orange-700 mb-0.5" id="kpi-upload">-</div>
            <div class="text-sm font-semibold text-orange-600 tracking-wide">Upload</div>
        </div>
    </div>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-green-500 rounded-lg px-6 py-3 shadow mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Lecturers Management</h2>
            <p class="text-white text-base opacity-90">Upload, add, and manage all lecturers by department</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="downloadTemplate()" class="flex items-center px-5 py-2.5 bg-blue-100 text-blue-700 font-semibold rounded-lg border border-blue-200 hover:bg-blue-200 transition text-base shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Download Template
            </button>
            <button onclick="openUploadModal()" class="flex items-center px-5 py-2.5 bg-green-100 text-green-700 font-semibold rounded-lg border border-green-200 hover:bg-green-200 transition text-base shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16m8-8H4"/></svg>
                Upload
            </button>
            <button onclick="openAddModal()" class="flex items-center px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Lecturer
            </button>
        </div>
    </div>
    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow p-2 sm:p-3 mb-3 w-full">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">#</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Staff ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Department</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Title</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 uppercase text-xs tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php $__currentLoopData = $lecturers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lecturer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="even:bg-gray-50 hover:bg-green-50 transition-colors duration-200 align-middle">
                        <td class="px-4 py-3 font-semibold text-gray-700"><?php echo e($index + 1); ?></td>
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($lecturer->user->full_name ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($lecturer->staff_id); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($lecturer->user->email ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($lecturer->department->name ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($lecturer->title ?? 'N/A'); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo e($lecturer->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'); ?>">
                                <?php echo e($lecturer->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="<?php echo e(route('superadmin.lecturers.details', $lecturer->id)); ?>" class="p-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" /></svg>
                                </a>
                                <button onclick="openEditModal(<?php echo e($lecturer->id); ?>)" class="p-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button onclick="openPasswordModal(<?php echo e($lecturer->id); ?>, '<?php echo e($lecturer->user->full_name ?? $lecturer->staff_id); ?>')" class="p-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors" title="Update Password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                </button>
                                <button onclick="toggleLecturerStatus(<?php echo e($lecturer->id); ?>, <?php echo e($lecturer->is_active ? 'true' : 'false'); ?>)" 
                                    data-lecturer-toggle="<?php echo e($lecturer->id); ?>"
                                    class="p-1.5 <?php echo e($lecturer->is_active ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200'); ?> rounded-lg transition-colors" title="<?php echo e($lecturer->is_active ? 'Disable' : 'Enable'); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($lecturer->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'); ?>" />
                                    </svg>
                                </button>
                                <button onclick="openDeleteModal(<?php echo e($lecturer->id); ?>, '<?php echo e($lecturer->user->full_name ?? $lecturer->staff_id); ?>')" class="p-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($lecturers->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($lecturers->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<!-- Duplicate lecturers table scripts removed -->
<?php $__env->stopPush(); ?>

<!-- Add Lecturer Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 transition-all duration-300 ease-in-out opacity-0 modal-backdrop">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 ease-in-out scale-95 translate-y-4 modal-content">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Add New Lecturer</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 modal-close-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="addForm" class="p-6">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
                        <input type="text" name="staff_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Add Lecturer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lecturer Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 transition-all duration-300 ease-in-out opacity-0 modal-backdrop">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 ease-in-out scale-95 translate-y-4 modal-content">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Edit Lecturer</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 modal-close-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="editForm" class="p-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="lecturer_id" id="edit_lecturer_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
                        <input type="text" name="staff_id" id="edit_staff_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" id="edit_department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="edit_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Lecturer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 transition-all duration-300 ease-in-out opacity-0 modal-backdrop">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 ease-in-out scale-95 translate-y-4 modal-content">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 w-10 h-10 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Lecturer</h3>
                    <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete <span id="delete_lecturer_name" class="font-semibold"></span>? This action cannot be undone.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal('deleteModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                    <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 transition-all duration-300 ease-in-out opacity-0 modal-backdrop">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 ease-in-out scale-95 translate-y-4 modal-content">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Upload Lecturers</h3>
                <button onclick="closeModal('uploadModal')" class="text-gray-400 hover:text-gray-600 modal-close-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="uploadForm" class="p-6" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select File</label>
                        <input type="file" name="file" accept=".csv,.xlsx,.xls" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Supported formats: CSV, XLSX, XLS</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('uploadModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Password Update Modal -->
<div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 transition-all duration-300 ease-in-out opacity-0 modal-backdrop">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full transform transition-all duration-300 ease-in-out scale-95 translate-y-4 modal-content">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Update Password</h3>
                <button onclick="closeModal('passwordModal')" class="text-gray-400 hover:text-gray-600 modal-close-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="passwordForm" class="p-6">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lecturer</label>
                        <input type="text" id="passwordLecturerName" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="new_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" minlength="6">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="new_password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" minlength="6">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('passwordModal')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentDeleteId = null;
let currentPasswordId = null;

document.addEventListener('DOMContentLoaded', function() {
    fetchLecturerStats();
    
    // Auto-hide flash messages
    setTimeout(() => {
        const flashMessages = document.querySelectorAll('[id^="flash-"]');
        flashMessages.forEach(msg => {
            if (msg) {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 300);
            }
        });
    }, 5000);
});

function fetchLecturerStats() {
    fetch('/api/superadmin/lecturers/stats')
        .then(response => response.json())
        .then(data => {
            const stats = data;
            document.getElementById('kpi-lecturers').textContent = stats.total ?? '-';
            document.getElementById('kpi-active').textContent = stats.active ?? '-';
            document.getElementById('kpi-inactive').textContent = stats.inactive ?? '-';
            document.getElementById('kpi-upload').textContent = stats.last_upload ? new Date(stats.last_upload).toLocaleString() : '-';
        })
        .catch(() => {
            document.getElementById('kpi-lecturers').textContent = '-';
            document.getElementById('kpi-active').textContent = '-';
            document.getElementById('kpi-inactive').textContent = '-';
            document.getElementById('kpi-upload').textContent = '-';
        });
}

function closeFlash(id) {
    const element = document.getElementById(id);
    if (element) {
        element.style.opacity = '0';
        setTimeout(() => element.remove(), 300);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('div > div');
    
    // Animate out
    modal.style.opacity = '0';
    modalContent.style.transform = 'scale(0.95) translateY(16px)';
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function openAddModal() {
    const modal = document.getElementById('addModal');
    const modalContent = modal.querySelector('div > div');
    
    modal.classList.remove('hidden');
    
    // Force reflow
    modal.offsetHeight;
    
    // Animate in
    modal.style.opacity = '1';
    modalContent.style.transform = 'scale(1) translateY(0)';
}

function openEditModal(id) {
    // Get lecturer data from the table row instead of API call
    const row = document.querySelector(`button[onclick="openEditModal(${id})"]`).closest('tr');
    const cells = row.querySelectorAll('td');
    
    // Extract data from table cells
    const staffId = cells[0].textContent.trim();
    const fullName = cells[1].textContent.trim();
    const email = cells[2].textContent.trim();
    const department = cells[3].textContent.trim();
    const title = cells[4].textContent.trim();
    
    // Populate form
    document.getElementById('edit_lecturer_id').value = id;
    document.getElementById('edit_staff_id').value = staffId;
    document.getElementById('edit_name').value = fullName;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_department').value = department;
    document.getElementById('edit_title').value = title;
    
    const modal = document.getElementById('editModal');
    const modalContent = modal.querySelector('div > div');
    
    modal.classList.remove('hidden');
    
    // Force reflow
    modal.offsetHeight;
    
    // Animate in
    modal.style.opacity = '1';
    modalContent.style.transform = 'scale(1) translateY(0)';
}

function openDeleteModal(id, name) {
    currentDeleteId = id;
    document.getElementById('delete_lecturer_name').textContent = name;
    
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div > div');
    
    modal.classList.remove('hidden');
    
    // Force reflow
    modal.offsetHeight;
    
    // Animate in
    modal.style.opacity = '1';
    modalContent.style.transform = 'scale(1) translateY(0)';
}

function openPasswordModal(id, name) {
    currentPasswordId = id;
    document.getElementById('passwordLecturerName').value = name;
    
    const modal = document.getElementById('passwordModal');
    const modalContent = modal.querySelector('div > div');
    
    modal.classList.remove('hidden');
    
    // Force reflow
    modal.offsetHeight;
    
    // Animate in
    modal.style.opacity = '1';
    modalContent.style.transform = 'scale(1) translateY(0)';
}

function openUploadModal() {
    const modal = document.getElementById('uploadModal');
    const modalContent = modal.querySelector('div > div');
    
    modal.classList.remove('hidden');
    
    // Force reflow
    modal.offsetHeight;
    
    // Animate in
    modal.style.opacity = '1';
    modalContent.style.transform = 'scale(1) translateY(0)';
}

function downloadTemplate() {
    // Create a simple CSV template
    const csvContent = "staff id,full name,email,department,title\nLEC001,Dr. John Doe,john.doe@nsuk.edu.ng,Computer Science,Dr.\nLEC002,Prof. Jane Smith,jane.smith@nsuk.edu.ng,Computer Science,Prof.";
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'lecturers_template.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function confirmDelete() {
    if (currentDeleteId) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/superadmin/lecturers/${currentDeleteId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (response.ok) {
                showNotification('Lecturer deleted successfully', 'success');
                closeModal('deleteModal');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error deleting lecturer', 'error');
            }
        })
        .catch(error => {
            showNotification('Error deleting lecturer', 'error');
        });
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    
    const icon = type === 'success' 
        ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
        : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    
    notification.innerHTML = `
        ${icon}
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Form submissions
document.getElementById('addForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    // Submit to web route for flash notifications
    fetch('/superadmin/lecturers', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (response.ok) {
            showNotification('Lecturer added successfully', 'success');
            closeModal('addModal');
            this.reset();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error adding lecturer', 'error');
        }
    })
    .catch(error => {
        showNotification('Error adding lecturer', 'error');
    });
});

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const lecturerId = document.getElementById('edit_lecturer_id').value;
    
    // Add _method for PUT request
    formData.append('_method', 'PUT');
    
    fetch(`/superadmin/lecturers/${lecturerId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (response.ok) {
            showNotification('Lecturer updated successfully', 'success');
            closeModal('editModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error updating lecturer', 'error');
        }
    })
    .catch(error => {
        showNotification('Error updating lecturer', 'error');
    });
});

document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/superadmin/lecturers/bulk-upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (response.ok) {
            showNotification('Upload completed successfully', 'success');
            closeModal('uploadModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error uploading file', 'error');
        }
    })
    .catch(error => {
        showNotification('Error uploading file', 'error');
    });
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    // Validate password confirmation
    const password = formData.get('new_password');
    const confirmPassword = formData.get('new_password_confirmation');
    
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }
    
    if (password.length < 6) {
        showNotification('Password must be at least 6 characters', 'error');
        return;
    }
    
    fetch(`/superadmin/lecturers/${currentPasswordId}/update-password`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (response.ok) {
            showNotification('Password updated successfully', 'success');
            closeModal('passwordModal');
            this.reset();
        } else {
            showNotification('Error updating password', 'error');
        }
    })
    .catch(error => {
        showNotification('Error updating password', 'error');
    });
});

function toggleLecturerStatus(lecturerId, currentStatus) {
    if (!confirm(`Are you sure you want to ${currentStatus ? 'disable' : 'enable'} this lecturer?`)) {
        return;
    }
    
    const button = document.querySelector(`button[data-lecturer-toggle="${lecturerId}"]`);
    if (button) button.disabled = true;
    
    axios.post(`/superadmin/lecturers/${lecturerId}/toggle`)
        .then(response => {
            if (response.data.success) {
                showToast(response.data.message, 'success');
                setTimeout(() => window.location.reload(), 500);
            } else {
                showToast(response.data.message || 'Failed to toggle lecturer status', 'error');
                if (button) button.disabled = false;
            }
        })
        .catch(error => {
            showToast(error.response?.data?.message || 'Failed to toggle lecturer status', 'error');
            if (button) button.disabled = false;
        });
}
</script>

<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\lecturers.blade.php ENDPATH**/ ?>