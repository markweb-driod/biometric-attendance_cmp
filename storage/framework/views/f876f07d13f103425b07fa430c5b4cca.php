<?php $__env->startSection('title', 'Students'); ?>
<?php $__env->startSection('page-title', 'Students'); ?>
<?php $__env->startSection('page-description', 'Manage all students across levels'); ?>

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
    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6 max-w-5xl mx-auto w-full" id="stats-cards">
        <div class="relative bg-white/90 shadow-xl border border-green-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-green-700 mb-0.5" id="kpi-students">-</div>
            <div class="text-sm font-semibold text-green-600 tracking-wide">Total</div>
            <div class="absolute bottom-2 left-0 w-full px-2">
                <div class="h-0.5 bg-green-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full transition-all duration-1000" style="width: 0%" id="kpi-students-bar"></div>
                </div>
            </div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-blue-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-blue-700 mb-0.5" id="kpi-active">-</div>
            <div class="text-sm font-semibold text-blue-600 tracking-wide">Active</div>
            <div class="absolute bottom-2 left-0 w-full px-2">
                <div class="h-0.5 bg-blue-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-1000" style="width: 0%" id="kpi-active-bar"></div>
                </div>
            </div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-purple-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-2xl font-extrabold text-purple-700 mb-0.5" id="kpi-inactive">-</div>
            <div class="text-sm font-semibold text-purple-600 tracking-wide">Inactive</div>
            <div class="absolute bottom-2 left-0 w-full px-2">
                <div class="h-0.5 bg-purple-100 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 rounded-full transition-all duration-1000" style="width: 0%" id="kpi-inactive-bar"></div>
                </div>
            </div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-orange-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
            </div>
            <div class="text-xl font-extrabold text-orange-700 mb-0.5" id="kpi-upload">-</div>
            <div class="text-sm font-semibold text-orange-600 tracking-wide">Upload</div>
            <div class="absolute bottom-2 left-0 w-full px-2">
                <div class="h-0.5 bg-orange-100 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full transition-all duration-1000" style="width: 0%" id="kpi-upload-bar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-green-500 rounded-lg px-6 py-3 shadow mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Students Management</h2>
            <p class="text-white text-base opacity-90">Upload, add, and manage all students by level</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="downloadStudentTemplate()" class="flex items-center px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Download Template
            </button>
            <button onclick="openUploadModal()" class="flex items-center px-5 py-2.5 bg-green-100 text-green-700 font-semibold rounded-lg border border-green-600 hover:bg-green-200 transition text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload
            </button>
            <button onclick="openAddModal()" class="flex items-center px-5 py-2.5 bg-green-700 text-white font-semibold rounded-lg hover:bg-green-800 transition text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add
            </button>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="bg-white rounded shadow p-2 sm:p-3 mb-3 w-full">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-2 mb-2">
            <div class="col-span-2 md:col-span-2">
                <input type="text" id="searchInput" placeholder="Search name, matric, email" 
                       class="w-full px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-xs">
            </div>
            <select id="levelFilter" class="w-full px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-xs">
                <option value="">All Levels</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="400">400</option>
            </select>
            <select id="departmentFilter" class="w-full px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-xs">
                <option value="">All Departments</option>
            </select>
            <select id="statusFilter" class="w-full px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-xs">
                <option value="">Any Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select id="faceFilter" class="w-full px-2 py-1 border border-gray-200 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-xs">
                <option value="">Face: Any</option>
                <option value="registered">Registered</option>
                <option value="not_registered">Not Registered</option>
            </select>
            <button id="resetFilters" class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-xs hover:bg-gray-300">Reset</button>
        </div>
        
        <div class="overflow-x-auto rounded border border-gray-100">
            <div class="flex justify-between mb-1">
                <div></div>
                <button id="bulkDeleteBtn" onclick="bulkDeleteStudents()" class="flex items-center gap-1 px-2 py-0.5 bg-red-600 text-white rounded font-medium hover:bg-red-700 transition text-xs hidden">
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-3 w-3' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' />
                    </svg> 
                    Delete
                </button>
            </div>
            
            <div class="min-w-full overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm" id="studentsTable">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                            <th class="w-8 px-0.5 py-1"><input type='checkbox' id='selectAll' onchange='toggleSelectAll(this)' class="rounded border-gray-300 text-green-600 focus:ring-green-500"></th>
                            <th class="w-24 px-0.5 py-1 text-left font-semibold text-gray-700 uppercase text-sm">Matric</th>
                            <th class="w-40 px-0.5 py-1 text-left font-semibold text-gray-700 uppercase text-sm">Name</th>
                            <th class="w-32 px-0.5 py-1 text-left font-semibold text-gray-700 uppercase text-sm hidden sm:table-cell">Email</th>
                            <th class="w-16 px-0.5 py-1 text-left font-semibold text-gray-700 uppercase text-sm">Level</th>
                            <th class="w-20 px-0.5 py-1 text-left font-semibold text-gray-700 uppercase text-sm">Status</th>
                            <th class="w-32 px-0.5 py-1 text-left font-semibold text-gray-700 uppercase text-sm">Actions</th>
                    </tr>
                </thead>
                    <tbody id="studentsTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="7" class="text-center text-gray-400 py-4 animate-pulse">
                                <div class="flex flex-col items-center">
                                    <svg class="w-5 h-5 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Loading...
                                </div>
                            </td>
                        </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination Controls -->
    <div class="bg-white rounded shadow p-2 sm:p-3 mb-3">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing <span id="showing-start">0</span> to <span id="showing-end">0</span> of <span id="total-records">0</span> students
            </div>
            <div class="flex items-center gap-2" id="pagination-controls">
                <button id="prev-page" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Previous
                </button>
                <div class="flex items-center gap-1" id="page-numbers">
                    <!-- Page numbers will be inserted here -->
                </div>
                <button id="next-page" class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8 relative animate-fade-in">
        <button onclick="closeUploadModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Upload Students</h3>
        <p class="text-gray-600 mb-6">Upload a CSV or Excel file (.csv, .xlsx, .xls) with columns: <b>full name</b>, <b>matric number</b>, <b>level</b>. You can drag and drop or click to select a file.</p>
        <form id="uploadForm" enctype="multipart/form-data" onsubmit="submitUploadForm(event)" class="space-y-6">
            <label for="csvFile" class="flex flex-col items-center justify-center border-2 border-dashed border-green-400 rounded-xl py-8 cursor-pointer hover:bg-green-50 transition-all duration-200">
                <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16v-4a4 4 0 014-4 4 4 0 014 4v4m-4 4v-4m0 0V4m0 8h4m-4 0H7" />
                </svg>
                <span class="text-green-700 font-semibold">Drag & drop or click to select file</span>
                <input type="file" id="csvFile" accept=".csv,.xlsx,.xls" class="hidden">
            </label>
            <div id="uploadFileName" class="text-sm text-gray-600"></div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeUploadModal()" class="px-6 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-xl hover:bg-gray-300 transition-all duration-200">Cancel</button>
                <button type="submit" id="uploadSubmitBtn" class="px-6 py-2 bg-green-600 text-white text-base font-medium rounded-xl hover:bg-green-700 transition-all duration-200 flex items-center gap-2">
                    <span id="uploadBtnText">Upload</span>
                    <svg id="uploadSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8 relative animate-fade-in">
        <button onclick="closeAddModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h3 class="text-2xl font-bold text-gray-900 mb-6" id="addModalTitle">Add Student</h3>
        <form id="addForm" onsubmit="submitStudentForm(event)" class="space-y-6">
            <input type="hidden" id="studentId">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Matric Number</label>
                <input type="text" id="matric" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                <input type="text" id="fullName" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Level</label>
                <select id="level" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base transition-all duration-200">
                    <option value="">Select Level</option>
                    <option value="1">100 Level</option>
                    <option value="2">200 Level</option>
                    <option value="3">300 Level</option>
                    <option value="4">400 Level</option>
                    <option value="5">500 Level</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeAddModal()" class="px-6 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-xl hover:bg-gray-300 transition-all duration-200">Cancel</button>
                <button type="submit" id="studentSubmitBtn" class="px-6 py-2 bg-green-600 text-white text-base font-medium rounded-xl hover:bg-green-700 transition-all duration-200 flex items-center gap-2">
                    <span id="studentSubmitText">Save</span>
                    <svg id="studentSubmitSpinner" class="w-4 h-4 animate-spin hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedStudents = new Set();
let editingStudentId = null;
let currentPage = 1;
let totalPages = 1;
let totalRecords = 0;

document.addEventListener('DOMContentLoaded', function() {
    fetchStudents();
    fetchStudentStats();
    fetchDepartments();
    
    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1; // Reset to first page on search
        fetchStudents();
    }, 300));
    document.getElementById('levelFilter').addEventListener('change', () => {
        currentPage = 1; // Reset to first page on filter
        fetchStudents();
    });
    document.getElementById('departmentFilter').addEventListener('change', () => { currentPage = 1; fetchStudents(); });
    document.getElementById('statusFilter').addEventListener('change', () => { currentPage = 1; fetchStudents(); });
    document.getElementById('faceFilter').addEventListener('change', () => { currentPage = 1; fetchStudents(); });
    document.getElementById('resetFilters').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('levelFilter').value = '';
        document.getElementById('departmentFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('faceFilter').value = '';
        currentPage = 1;
        fetchStudents();
    });
    
    // Pagination event listeners
    document.getElementById('prev-page').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            fetchStudents();
        }
    });
    
    document.getElementById('next-page').addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            fetchStudents();
        }
    });
    
    // File upload handling
    document.getElementById('csvFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || '';
        document.getElementById('uploadFileName').textContent = fileName;
    });
    
    // Modal backdrop clicks
    document.getElementById('uploadModal').addEventListener('click', function(e) {
        if (e.target === this) closeUploadModal();
    });
    document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) closeAddModal();
    });
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function fetchStudentStats() {
    axios.get('/api/superadmin/students/stats')
        .then(res => {
            // Try both possible response shapes
            const stats = res.data.success && typeof res.data.total !== 'undefined' ? res.data : (res.data.data || {});
            const total = stats.total || 0;
            const active = stats.active || 0;
            const inactive = stats.inactive || 0;
            
            // Animate the numbers
            animateNumber('kpi-students', total);
            animateNumber('kpi-active', active);
            animateNumber('kpi-inactive', inactive);
            
            // Update last upload
            const lastUpload = stats.last_upload ? new Date(stats.last_upload).toLocaleDateString() : 'Never';
            document.getElementById('kpi-upload').textContent = lastUpload;
            
            // Animate progress bars
            setTimeout(() => {
                document.getElementById('kpi-students-bar').style.width = '100%';
                document.getElementById('kpi-active-bar').style.width = total > 0 ? (active / total * 100) + '%' : '0%';
                document.getElementById('kpi-inactive-bar').style.width = total > 0 ? (inactive / total * 100) + '%' : '0%';
                document.getElementById('kpi-upload-bar').style.width = '100%';
            }, 500);
        })
        .catch(err => {
            console.error('KPI stats error:', err);
            showToast('Failed to load student stats', 'error');
        });
}

function animateNumber(elementId, targetValue) {
    const element = document.getElementById(elementId);
    const startValue = 0;
    const duration = 1000;
    const startTime = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
        
        element.textContent = currentValue;
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

function submitUploadForm(e) {
    e.preventDefault();
    const fileInput = document.getElementById('csvFile');
    const uploadBtn = document.getElementById('uploadSubmitBtn');
    const uploadBtnText = document.getElementById('uploadBtnText');
    const uploadSpinner = document.getElementById('uploadSpinner');
    
    if (!fileInput.files.length) {
        showToast('Please select a file.', 'error');
        return;
    }
    
    uploadBtn.disabled = true;
    uploadBtnText.textContent = 'Uploading...';
    uploadSpinner.classList.remove('hidden');
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    
    axios.post('/api/superadmin/students/bulk-upload', formData, { 
        headers: { 'Content-Type': 'multipart/form-data' } 
    })
        .then(res => {
            closeUploadModal();
            fetchStudents();
            fetchStudentStats();
            let msg = 'Upload complete. ' + (res.data.created || 0) + ' students added.';
            if (res.data.errors && res.data.errors.length) {
                msg += '\nSome errors:\n' + res.data.errors.join('\n');
            }
            showToast(msg, 'success');
        })
        .catch(err => {
            let msg = 'Failed to upload.';
            if (err.response && err.response.data && err.response.data.message) {
                msg = err.response.data.message;
            }
            showToast(msg, 'error');
            console.error('Upload error:', err);
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtnText.textContent = 'Upload';
            uploadSpinner.classList.add('hidden');
            fileInput.value = '';
            document.getElementById('uploadFileName').textContent = '';
        });
}

function fetchStudents() {
    const tableBody = document.getElementById('studentsTableBody');
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-4 animate-pulse">
                <div class="flex flex-col items-center">
                    <svg class="w-5 h-5 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Loading...
                </div>
            </td>
        </tr>
    `;
    
    const search = document.getElementById('searchInput').value;
    const level = document.getElementById('levelFilter').value;
    const department = document.getElementById('departmentFilter').value;
    const status = document.getElementById('statusFilter').value;
    const face = document.getElementById('faceFilter').value;
    let url = `/api/superadmin/students?page=${currentPage}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (level) url += `&level=${encodeURIComponent(level)}`;
    if (department) url += `&department=${encodeURIComponent(department)}`;
    if (status) url += `&status=${encodeURIComponent(status)}`;
    if (face) url += `&face_status=${encodeURIComponent(face)}`;
    
    axios.get(url)
        .then(res => {
            const students = res.data.data.data;
            totalRecords = res.data.data.total;
            totalPages = res.data.data.last_page;
            currentPage = res.data.data.current_page;

            if (!students.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-gray-400 py-4">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-500 mb-2">No students found</p>
                                <p class="text-sm text-gray-400">Try adjusting your search or add some students</p>
                            </div>
                        </td>
                    </tr>
                `;
                document.getElementById('bulkDeleteBtn').classList.add('hidden');
                return;
            }
            
            tableBody.innerHTML = '';
            students.forEach(s => {
                tableBody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-0.5 py-1 align-middle">
                            <input type='checkbox' class='rowCheckbox rounded border-gray-300 text-green-600 focus:ring-green-500' value='${s.id}' onchange='toggleSelectStudent(${s.id}, this)'>
                        </td>
                        <td class="px-0.5 py-1 align-middle font-medium text-gray-900 text-sm truncate" title="${s.matric_number}">${s.matric_number}</td>
                        <td class="px-0.5 py-1 align-middle font-medium text-gray-900 text-sm truncate" title="${s.full_name || 'N/A'}">${s.full_name || 'N/A'}</td>
                        <td class="px-0.5 py-1 align-middle text-gray-600 text-sm truncate hidden sm:table-cell" title="${s.email || '-'}">${s.email || '-'}</td>
                        <td class="px-0.5 py-1 align-middle">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-sm font-medium bg-green-100 text-green-800">
                                ${s.level || s.academic_level || 'N/A'}
                            </span>
                        </td>
                        <td class="px-0.5 py-1 align-middle">
                            ${s.is_active ? 
                                '<span class="inline-flex items-center px-1 py-0.5 rounded text-sm font-medium bg-green-100 text-green-800">Active</span>' : 
                                '<span class="inline-flex items-center px-1 py-0.5 rounded text-sm font-medium bg-red-100 text-red-800">Inactive</span>'
                            }
                        </td>
                        <td class="px-0.5 py-1 align-middle">
                            <div class='flex items-center gap-2'>
                                <button class='text-indigo-600 hover:text-indigo-800 transition-colors duration-200 p-1 rounded hover:bg-indigo-50' onclick='viewStudent(${s.id})' title='View'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                      <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button class='text-blue-600 hover:text-blue-800 transition-colors duration-200 p-1 rounded hover:bg-blue-50' onclick='editStudent(${s.id})' title='Edit'>                   
                                    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>                  
                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z' />                                 
                                    </svg>                                        
                                </button>
                                <button class='${s.is_active ? 'text-orange-600 hover:text-orange-800 hover:bg-orange-50' : 'text-green-600 hover:text-green-800 hover:bg-green-50'} transition-colors duration-200 p-1 rounded' onclick='toggleStudentStatus(${s.id}, ${s.is_active})' data-student-toggle='${s.id}' title='${s.is_active ? 'Disable' : 'Enable'}'>                   
                                    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>                  
                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='${s.is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}' />                                 
                                    </svg>                                        
                                </button>
                                <button class='text-red-600 hover:text-red-800 transition-colors duration-200 p-1 rounded hover:bg-red-50' onclick='deleteStudent(${s.id})' title='Delete'>
                                    <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' />
                                    </svg>
                                </button>
                                <button class='text-green-600 hover:text-green-800 transition-colors duration-200 p-1 rounded hover:bg-green-50' onclick='toggleFaceRegistration(${s.id}, ${!!s.face_registration_enabled})' title='${s.face_registration_enabled ? "Disable" : "Enable"} Face Registration'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="ml-1 text-xs font-semibold">${s.face_registration_enabled ? 'Disable' : 'Enable'} Face Reg</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            updateBulkDeleteBtn();
            updatePaginationControls();
        })
        .catch(err => {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-red-400 py-4">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-medium text-red-500 mb-2">Failed to load students</p>
                            <p class="text-sm text-red-400">Please try again later</p>
                        </div>
                    </td>
                </tr>
            `;
            document.getElementById('bulkDeleteBtn').classList.add('hidden');
            console.error('API error:', err);
        });
}

function fetchDepartments() {
    axios.get('/api/superadmin/classes') // reuse a list source to infer departments if dedicated endpoint not present
        .then(() => {
            // Fallback: fetch from a lightweight endpoint if exists; otherwise, prefill from DOM cache set in lecturers view
            return axios.get('/superadmin/academic-structure/dropdown-data');
        })
        .then(res => {
            const deptSelect = document.getElementById('departmentFilter');
            if (res.data && res.data.departments) {
                res.data.departments.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.textContent = d.name;
                    deptSelect.appendChild(opt);
                });
            }
        })
        .catch(() => {
            // Silent fail; departments optional for filtering if endpoint unavailable
        });
}

const storageBaseUrl = '<?php echo e(asset('storage')); ?>';
function viewStudent(id) {
    axios.get('/api/superadmin/students/' + id)
        .then(res => {
            const s = res.data.data;
            const imageUrl = s.reference_image_path ? `${storageBaseUrl}/${s.reference_image_path}` : null;
            const html = `
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-4 mb-4">
                        ${imageUrl ? `<img src="${imageUrl}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-4 border-blue-200 shadow"/>` : `<div class=\"w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold\">${(s.full_name||'NA').substring(0,2)}</div>`}
                        <div>
                            <div class="text-xl font-bold text-gray-900">${s.full_name}</div>
                            <div class="text-sm text-gray-600 font-mono">${s.matric_number}</div>
                            <div class="text-sm text-gray-600">${s.email || '-'}</div>
                            <div class="flex gap-2 mt-2">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">${s.academic_level || '-'}</span>
                                <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">${s.department || '-'}</span>
                                <span class="px-2 py-0.5 ${s.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs rounded-full">${s.is_active ? 'Active' : 'Inactive'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div><span class="text-gray-500">Face Registration:</span> <span class="text-gray-800 font-medium">${s.face_registration_enabled ? 'Enabled' : 'Disabled'}</span></div>
                        <div><span class="text-gray-500">Reference Image:</span> <span class="text-gray-800 font-medium">${s.reference_image_path ? 'Available' : 'Not Available'}</span></div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <a href="/superadmin/students/${s.id}/details" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Advanced Details</a>
                        <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300" onclick="this.closest('.fixed').remove()">Close</button>
                    </div>
                </div>`;
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-3';
            modal.innerHTML = `<div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative border border-gray-100"> 
                <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700" onclick="this.closest('.fixed').remove()">✕</button>
                ${html}
            </div>`;
            document.body.appendChild(modal);
        });
}

function openAddModal() {
    editingStudentId = null;
    document.getElementById('addModalTitle').textContent = 'Add Student';
    document.getElementById('addForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function editStudent(id) {
    editingStudentId = id;
    document.getElementById('addModalTitle').textContent = 'Edit Student';
    axios.get('/api/superadmin/students/' + id)
        .then(res => {
            const s = res.data.data;
            document.getElementById('studentId').value = s.id;
            document.getElementById('matric').value = s.matric_number;
            document.getElementById('fullName').value = s.full_name;
            document.getElementById('email').value = s.email || '';
            document.getElementById('level').value = s.academic_level;
            document.getElementById('addModal').classList.remove('hidden');
        });
}

function submitStudentForm(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = document.getElementById('studentSubmitBtn');
    const submitText = document.getElementById('studentSubmitText');
    const submitSpinner = document.getElementById('studentSubmitSpinner');
    
    submitBtn.disabled = true;
    submitText.textContent = editingStudentId ? 'Updating...' : 'Saving...';
    submitSpinner.classList.remove('hidden');
    
    const data = {
        matric_number: document.getElementById('matric').value,
        full_name: document.getElementById('fullName').value,
        email: document.getElementById('email').value,
        academic_level: document.getElementById('level').value,
    };
    
    if (editingStudentId) {
        axios.put('/api/superadmin/students/' + editingStudentId, data)
            .then(() => {
                closeAddModal();
                fetchStudents();
                fetchStudentStats();
                showToast('✅ Student updated successfully!', 'success');
            })
            .catch(err => {
                showValidationError(err);
            })
            .finally(() => {
                // Reset loading state
                submitBtn.disabled = false;
                submitText.textContent = 'Save';
                submitSpinner.classList.add('hidden');
            });
    } else {
        axios.post('/api/superadmin/students', data)
            .then(() => {
                closeAddModal();
                fetchStudents();
                fetchStudentStats();
                showToast('✅ Student added successfully!', 'success');
            })
            .catch(err => {
                showValidationError(err);
            })
            .finally(() => {
                // Reset loading state
                submitBtn.disabled = false;
                submitText.textContent = 'Save';
                submitSpinner.classList.add('hidden');
            });
    }
}

function showValidationError(err) {
    if (err.response && err.response.data && err.response.data.errors) {
        let msg = Object.values(err.response.data.errors).map(e => e[0]).join('\n');
        showToast(msg, 'error');
    } else {
        showToast('Failed to save student.', 'error');
        console.error(err);
    }
}

function confirmDelete(message) {
    return new Promise((resolve) => {
        const result = confirm(message);
        resolve(result);
    });
}

function deleteStudent(id) {
    confirmDelete('Are you sure you want to delete this student?').then(result => {
        if (result) {
            axios.delete('/api/superadmin/students/' + id)
                .then(() => { 
                    fetchStudents(); 
                    fetchStudentStats(); 
                    showToast('Student deleted successfully', 'success');
                })
                .catch(() => showToast('Failed to delete student.', 'error'));
        }
    });
}

function toggleSelectStudent(id, checkbox) {
    if (checkbox.checked) {
        selectedStudents.add(id);
    } else {
        selectedStudents.delete(id);
    }
    updateBulkDeleteBtn();
}

function toggleSelectAll(headerCheckbox) {
    const checkboxes = document.querySelectorAll('.rowCheckbox');
    checkboxes.forEach(cb => {
        cb.checked = headerCheckbox.checked;
        if (headerCheckbox.checked) {
            selectedStudents.add(Number(cb.value));
        } else {
            selectedStudents.delete(Number(cb.value));
        }
    });
    updateBulkDeleteBtn();
}

function updateBulkDeleteBtn() {
    const btn = document.getElementById('bulkDeleteBtn');
    if (selectedStudents.size > 0) {
        btn.classList.remove('hidden');
        btn.textContent = `Delete ${selectedStudents.size} Students`;
    } else {
        btn.classList.add('hidden');
    }
}

function bulkDeleteStudents() {
    if (selectedStudents.size === 0) return showToast('No students selected.', 'error');
    
    confirmDelete(`Are you sure you want to delete ${selectedStudents.size} students?`).then(result => {
        if (result) {
            const ids = Array.from(selectedStudents);
            Promise.all(ids.map(id => axios.delete('/api/superadmin/students/' + id)))
                .then(() => {
                    selectedStudents.clear();
                    fetchStudents();
                    fetchStudentStats();
                    showToast(`${ids.length} students deleted successfully`, 'success');
                })
                .catch(() => showToast('Failed to delete some students.', 'error'));
        }
    });
}

function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm').reset();
    document.getElementById('uploadFileName').textContent = '';
}

function updatePaginationControls() {
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const pageNumbersDiv = document.getElementById('page-numbers');
    const showingStart = document.getElementById('showing-start');
    const showingEnd = document.getElementById('showing-end');
    const totalRecordsSpan = document.getElementById('total-records');

    // Update showing info
    const perPage = 20;
    const start = (currentPage - 1) * perPage + 1;
    const end = Math.min(currentPage * perPage, totalRecords);
    
    showingStart.textContent = totalRecords > 0 ? start : 0;
    showingEnd.textContent = end;
    totalRecordsSpan.textContent = totalRecords;

    // Update button states
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;

    // Update page numbers
    pageNumbersDiv.innerHTML = '';
    
    if (totalPages <= 1) {
        return; // No pagination needed
    }

    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'px-2 py-1 text-sm text-gray-500';
        ellipsis.textContent = '...';
        pageNumbersDiv.appendChild(ellipsis);
    }

    for (let i = startPage; i <= endPage; i++) {
        const span = document.createElement('span');
        span.className = `px-2 py-1 text-sm rounded cursor-pointer transition-colors ${
            i === currentPage 
                ? 'bg-green-500 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
        }`;
        span.textContent = i;
        span.addEventListener('click', () => {
            currentPage = i;
            fetchStudents();
        });
        pageNumbersDiv.appendChild(span);
    }

    if (endPage < totalPages) {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'px-2 py-1 text-sm text-gray-500';
        ellipsis.textContent = '...';
        pageNumbersDiv.appendChild(ellipsis);
    }
}

function closeFlash(id) {
    const flash = document.getElementById(id);
    if (flash) {
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-10px)';
        setTimeout(() => flash.remove(), 300);
    }
}

// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        ['flash-success', 'flash-error'].forEach(id => {
            const flash = document.getElementById(id);
            if (flash) {
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(() => flash.remove(), 300);
            }
        });
    }, 5000);
});

<?php if(session('success')): ?>
setTimeout(() => {
    if (window.showSuccessModal) {
        window.showSuccessModal('<?php echo e(session('success')); ?>');
    } else {
        showToast('<?php echo e(session('success')); ?>', 'success');
    }
}, 300);
<?php endif; ?>

<?php if(session('error')): ?>
setTimeout(() => {
    if (window.showErrorModal) {
        window.showErrorModal('<?php echo e(session('error')); ?>');
    } else {
        showToast('<?php echo e(session('error')); ?>', 'error');
    }
}, 300);
<?php endif; ?>

function showToast(message, type = 'info') {
    // Enhanced toast notification with icons and animations
    const toast = document.createElement('div');
    
    const icons = {
        success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
        info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };
    
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl text-white font-medium shadow-2xl flex items-center gap-3 min-w-[300px] transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-gradient-to-r from-green-500 to-green-600' : 
        type === 'error' ? 'bg-gradient-to-r from-red-500 to-red-600' : 
        'bg-gradient-to-r from-blue-500 to-blue-600'
    }`;
    
    toast.innerHTML = `
        <div class="flex-shrink-0">${icons[type] || icons.info}</div>
        <div class="flex-1">${message}</div>
        <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-white hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Slide in animation
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function downloadStudentTemplate() {
    const csv = 'full name,matric number,level\n';
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'student_upload_template.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function toggleFaceRegistration(studentId, currentlyEnabled) {                    
    const url = currentlyEnabled
        ? `/superadmin/students/${studentId}/disable-face-registration`           
        : `/superadmin/students/${studentId}/enable-face-registration`;           
    axios.post(url)
        .then(() => {
            fetchStudents();
            showToast(`Face registration ${currentlyEnabled ? 'disabled' : 'enabled'} successfully`, 'success');           
        })
        .catch(() => {
            showToast('Failed to update face registration status.', 'error');     
        });
}

function toggleStudentStatus(studentId, currentStatus) {
    if (!confirm(`Are you sure you want to ${currentStatus ? 'disable' : 'enable'} this student?`)) {
        return;
    }
    
    const button = document.querySelector(`button[data-student-toggle="${studentId}"]`);
    if (button) button.disabled = true;
    
    axios.post(`/superadmin/students/${studentId}/toggle`)
        .then(response => {
            if (response.data.success) {
                showToast(response.data.message, 'success');
                fetchStudents(); // Reload students list
            } else {
                showToast(response.data.message || 'Failed to toggle student status', 'error');
                if (button) button.disabled = false;
            }
        })
        .catch(error => {
            showToast(error.response?.data?.message || 'Failed to toggle student status', 'error');
            if (button) button.disabled = false;
        });
}

function bulkToggleStudents(action) {
    const selected = Array.from(selectedStudents);
    if (selected.length === 0) {
        showToast('Please select at least one student', 'error');
        return;
    }
    
    if (!confirm(`Are you sure you want to ${action} ${selected.length} student(s)?`)) {
        return;
    }
    
    axios.post('/superadmin/students/bulk-toggle', {
        student_ids: selected,
        action: action
    })
    .then(response => {
        if (response.data.success) {
            showToast(response.data.message, 'success');
            selectedStudents.clear();
            fetchStudents();
        }
    })
    .catch(error => {
        showToast(error.response?.data?.message || 'Failed to toggle students', 'error');
    });
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\students.blade.php ENDPATH**/ ?>