

<?php $__env->startSection('title', 'Lecturer Management'); ?>

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

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-lg border-l-4 border-purple-500">
        <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-purple-800 break-words" style="font-family: 'Montserrat', sans-serif;">Lecturer Management</h1>
                    <p class="mt-1 text-xs sm:text-sm text-purple-600 font-medium">Manage lecturers in your department</p>
                </div>
                <button onclick="downloadTemplate()" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-purple-300 rounded-lg shadow-sm text-xs sm:text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 transition-all sm:flex-shrink-0">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Download Template</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div id="statistics" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6"></div>

        <!-- Upload Section -->
                    <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-purple-200">
            <h3 class="text-lg font-semibold text-purple-800 mb-4">Bulk Upload Lecturers</h3>
            <form id="uploadForm" enctype="multipart/form-data" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select File (CSV/Excel)</label>
                    <input type="file" id="uploadFile" name="file" accept=".csv,.xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                </div>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload
                </button>
            </form>
        </div>

        <!-- Search -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-purple-200">
            <h3 class="text-lg font-semibold text-purple-800 mb-4">Search Lecturers</h3>
            <input type="text" id="searchInput" placeholder="Search by name, staff ID, or email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Staff ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Full Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="lecturersTable"></tbody>
                </table>
            </div>
            <div id="pagination" class="bg-purple-50 px-6 py-4"></div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-3xl shadow-xl rounded-lg bg-white my-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-900">Lecturer Details</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="viewModalContent" class="space-y-6">
            <!-- Content will be loaded here -->
        </div>
        <div class="mt-8 flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button onclick="closeViewModal()" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Close</button>
            <button id="viewDetailsBtn" onclick="goToAdvancedDetails()" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                View Advanced Details
            </button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Lecturer</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="editFullName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
                    <input type="text" id="editStaffId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="editEmail" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="editPhone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="editTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="e.g., Dr., Prof.">
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="editIsActive" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active Status</span>
                    </label>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition">Save</button>
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadLecturers();
    document.getElementById('searchInput').addEventListener('input', debounce(() => { currentPage = 1; loadLecturers(); }, 500));
    document.getElementById('uploadForm').addEventListener('submit', handleUpload);
});

function loadStatistics() {
    fetch('<?php echo e(route("hod.management.lecturers.api.statistics")); ?>')
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('statistics').innerHTML = `
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-sm text-purple-600 font-medium">Total Lecturers</p>
                        <p class="text-2xl font-bold text-purple-800">${d.data.total}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-green-600 font-medium">Active</p>
                        <p class="text-2xl font-bold text-green-800">${d.data.active}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                        <p class="text-sm text-red-600 font-medium">Inactive</p>
                        <p class="text-2xl font-bold text-red-800">${d.data.inactive}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-blue-600 font-medium">With Classes</p>
                        <p class="text-2xl font-bold text-blue-800">${d.data.with_classes}</p>
                    </div>
                `;
            }
        });
}

function loadLecturers() {
    const params = new URLSearchParams({
        page: currentPage,
        search: document.getElementById('searchInput').value,
    });
    
    fetch(`<?php echo e(route("hod.management.lecturers.api.list")); ?>?${params}`)
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                renderTable(d.data.data);
                renderPagination(d.data);
            }
        });
}

function renderTable(lecturers) {
    const tbody = document.getElementById('lecturersTable');
    if (lecturers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center">No lecturers found</td></tr>';
        return;
    }
    
    tbody.innerHTML = lecturers.map(l => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900 font-mono">${l.staff_id}</td>
            <td class="px-6 py-4 text-sm text-gray-900 font-medium">${l.full_name}</td>
            <td class="px-6 py-4 text-sm text-gray-700">${l.email}</td>
            <td class="px-6 py-4 text-sm text-gray-700">${l.title || 'N/A'}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full ${l.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${l.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center space-x-2">
                    <button onclick="viewLecturer(${l.id})" class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-600 rounded-md hover:bg-purple-100 transition text-xs font-medium shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View
                    </button>
                    <button onclick="openEditModal(${l.id})" class="inline-flex items-center px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-md hover:bg-yellow-100 transition text-xs font-medium shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button onclick="deleteLecturer(${l.id})" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition text-xs font-medium shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderPagination(data) {
    const pagination = document.getElementById('pagination');
    if (data.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let html = `<div class="flex justify-between items-center">`;
    html += `<div class="text-sm text-gray-700 font-medium">Showing ${data.from} to ${data.to} of ${data.total} lecturers</div>`;
    html += `<div class="flex space-x-2">`;
    if (data.prev_page_url) html += `<button onclick="changePage(${data.current_page - 1})" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium text-sm shadow-sm">Previous</button>`;
    if (data.next_page_url) html += `<button onclick="changePage(${data.current_page + 1})" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium text-sm shadow-sm">Next</button>`;
    html += `</div></div>`;
    pagination.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadLecturers();
}

function handleUpload(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fetch('<?php echo e(route("hod.management.lecturers.api.upload")); ?>', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: fd
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert(`Upload completed! Created: ${d.created}, Skipped: ${d.skipped}`);
            loadLecturers();
            loadStatistics();
            e.target.reset();
        }
    });
}

function downloadTemplate() {
    window.location.href = '<?php echo e(route("hod.management.lecturers.api.template")); ?>';
}

let currentLecturerId = null;

function viewLecturer(id) {
    currentLecturerId = id;
    fetch(`<?php echo e(route("hod.management.lecturers.api.show", ":id")); ?>`.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const lecturer = data.data;
                const content = `
                    <!-- Lecturer Image Section -->
                    <div class="flex flex-col items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="relative mb-4">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center border-4 border-purple-200 shadow-lg">
                                <span class="text-3xl font-bold text-white">${lecturer.full_name ? lecturer.full_name.substring(0, 2).toUpperCase() : 'N/A'}</span>
                            </div>
                            ${lecturer.is_active 
                                ? `<div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-2 border-4 border-white">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>`
                                : ''
                            }
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-1">${lecturer.full_name}</h4>
                        <p class="text-base text-gray-600 font-mono">${lecturer.staff_id}</p>
                        ${lecturer.title ? `<p class="text-sm text-gray-500 mt-1">${lecturer.title}</p>` : ''}
                    </div>

                    <!-- Lecturer Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h5 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">Personal Information</h5>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Staff ID</span>
                                    <span class="text-sm font-mono text-gray-900 font-semibold">${lecturer.staff_id}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Full Name</span>
                                    <span class="text-sm text-gray-900 font-semibold text-right max-w-xs">${lecturer.full_name}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Email</span>
                                    <span class="text-sm text-gray-900 text-right max-w-xs break-words">${lecturer.email}</span>
                                </div>
                                <div class="flex justify-between items-start py-3">
                                    <span class="text-sm font-medium text-gray-500">Phone</span>
                                    <span class="text-sm text-gray-900">${lecturer.phone || 'Not provided'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">Professional Information</h5>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Title</span>
                                    <span class="text-sm text-gray-900 font-semibold">${lecturer.title || 'Not provided'}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Department</span>
                                    <span class="text-sm text-gray-900 font-semibold">${lecturer.department}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Status</span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${lecturer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${lecturer.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                                <div class="flex justify-between items-start py-3">
                                    <span class="text-sm font-medium text-gray-500">Active Classes</span>
                                    <span class="text-sm text-gray-900 font-semibold">${lecturer.classrooms_count || 0}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('viewModalContent').innerHTML = content;
                document.getElementById('viewModal').classList.remove('hidden');
            } else {
                alert('Failed to load lecturer details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load lecturer details');
        });
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    currentLecturerId = null;
}

function goToAdvancedDetails() {
    if (currentLecturerId) {
        window.location.href = `<?php echo e(route("hod.management.lecturers.show", ":id")); ?>`.replace(':id', currentLecturerId);
    }
}

function openEditModal(id) {
    currentLecturerId = id;
    fetch(`<?php echo e(route("hod.management.lecturers.api.show", ":id")); ?>`.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const lecturer = data.data;
                document.getElementById('editId').value = lecturer.id;
                document.getElementById('editFullName').value = lecturer.full_name;
                document.getElementById('editStaffId').value = lecturer.staff_id;
                document.getElementById('editEmail').value = lecturer.email;
                document.getElementById('editPhone').value = lecturer.phone || '';
                document.getElementById('editTitle').value = lecturer.title || '';
                document.getElementById('editIsActive').checked = lecturer.is_active;
                document.getElementById('editModal').classList.remove('hidden');
            } else {
                alert('Failed to load lecturer data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load lecturer data');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentLecturerId = null;
}

document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    const formData = {
        staff_id: document.getElementById('editStaffId').value,
        full_name: document.getElementById('editFullName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        title: document.getElementById('editTitle').value,
        is_active: document.getElementById('editIsActive').checked,
    };
    
    fetch(`<?php echo e(route("hod.management.lecturers.api.update", ":id")); ?>`.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Lecturer updated successfully');
            closeEditModal();
            loadLecturers();
            loadStatistics();
        } else {
            alert('Update failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Update failed');
    });
});

function deleteLecturer(id) {
    if (confirm('Are you sure you want to delete this lecturer?')) {
        fetch(`<?php echo e(route("hod.management.lecturers.api.delete", ":id")); ?>`.replace(':id', id), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                alert('Lecturer deleted successfully');
                loadLecturers();
                loadStatistics();
            } else {
                alert('Delete failed: ' + (d.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Delete failed');
        });
    }
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('hod.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\management\lecturers\index.blade.php ENDPATH**/ ?>