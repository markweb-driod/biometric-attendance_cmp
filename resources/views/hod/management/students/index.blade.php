@extends('hod.layouts.app')

@section('title', 'Student Management')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span>{{ session('success') }}</span>
    <button onclick="closeFlash('flash-success')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

@if(session('error'))
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>{{ session('error') }}</span>
    <button onclick="closeFlash('flash-error')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-lg border-l-4 border-blue-500">
        <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-800 break-words" style="font-family: 'Montserrat', sans-serif;">Student Management</h1>
                    <p class="mt-1 text-xs sm:text-sm text-blue-600 font-medium">Manage students in your department</p>
                </div>
                <div class="flex flex-shrink-0">
                    <button onclick="downloadTemplate()" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-blue-300 rounded-lg shadow-sm text-xs sm:text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Download Template</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div id="statistics" class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <!-- Stats will be loaded here -->
        </div>

        <!-- Upload Section -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Bulk Upload Students</h3>
            <form id="uploadForm" enctype="multipart/form-data" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select File (CSV/Excel)</label>
                    <input type="file" id="uploadFile" name="file" accept=".csv,.xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload
                </button>
            </form>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Search & Filters</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="col-span-2">
                    <input type="text" id="searchInput" placeholder="Search by name or matric number..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <select id="levelFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border-l-4 border-blue-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Matric Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Full Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Face Registration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTable" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="bg-blue-50 px-6 py-4">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-8 border w-full max-w-3xl shadow-xl rounded-lg bg-white my-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-900">Student Details</h3>
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
            <button id="viewDetailsBtn" onclick="goToAdvancedDetails()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
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
                <h3 class="text-lg font-semibold text-gray-900">Edit Student</h3>
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
                    <input type="text" id="editFullName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matric Number</label>
                    <input type="text" id="editMatricNumber" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="editEmail" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" id="editPhone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Level</label>
                    <select id="editLevel" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="editIsActive" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active Status</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="editFaceRegistration" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Face Registration Enabled</span>
                    </label>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Save</button>
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
const perPage = 25;

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadStudents();
    
    // Search input
    document.getElementById('searchInput').addEventListener('input', debounce(() => {
        currentPage = 1;
        loadStudents();
    }, 500));
    
    // Filters
    document.getElementById('levelFilter').addEventListener('change', () => {
        currentPage = 1;
        loadStudents();
    });
    
    document.getElementById('statusFilter').addEventListener('change', () => {
        currentPage = 1;
        loadStudents();
    });
    
    // Upload form
    document.getElementById('uploadForm').addEventListener('submit', handleUpload);
    
    // Edit form
    document.getElementById('editForm').addEventListener('submit', handleUpdate);
});

function loadStatistics() {
    fetch('{{ route("hod.management.students.api.statistics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('statistics').innerHTML = `
                    <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-blue-600">Total Students</p>
                        <p class="text-2xl font-bold text-blue-800">${data.data.total}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                        <p class="text-sm text-green-600">Active</p>
                        <p class="text-2xl font-bold text-green-800">${data.data.active}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                        <p class="text-sm text-red-600">Inactive</p>
                        <p class="text-2xl font-bold text-red-800">${data.data.inactive}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                        <p class="text-sm text-purple-600">Face Registered</p>
                        <p class="text-2xl font-bold text-purple-800">${data.data.face_registered}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                        <p class="text-sm text-yellow-600">Not Registered</p>
                        <p class="text-2xl font-bold text-yellow-800">${data.data.not_face_registered}</p>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error loading statistics:', error));
}

function loadStudents() {
    const params = new URLSearchParams({
        page: currentPage,
        per_page: perPage,
        search: document.getElementById('searchInput').value,
        level_id: document.getElementById('levelFilter').value,
        status: document.getElementById('statusFilter').value,
    });
    
    fetch(`{{ route("hod.management.students.api.list") }}?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderStudentsTable(data.data.data);
                renderPagination(data.data);
            }
        });
}

function renderStudentsTable(students) {
    const tbody = document.getElementById('studentsTable');
    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No students found</td></tr>';
        return;
    }
    
    tbody.innerHTML = students.map(student => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${student.matric_number}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${student.full_name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${student.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${student.academic_level}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${student.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${student.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${student.face_registration_enabled ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">
                    ${student.face_registration_enabled ? 'Enabled' : 'Disabled'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center space-x-2">
                    <button onclick="viewStudent(${student.id})" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition text-xs font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View
                    </button>
                    <button onclick="openEditModal(${student.id})" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-md hover:bg-green-100 transition text-xs font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                    <button onclick="deleteStudent(${student.id})" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition text-xs font-medium">
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
    
    let html = '<div class="flex justify-between items-center">';
    html += `<div class="text-sm text-gray-700">Showing ${data.from} to ${data.to} of ${data.total} results</div>`;
    html += '<div class="flex space-x-2">';
    
    // Previous button
    if (data.prev_page_url) {
        html += `<button onclick="changePage(${data.current_page - 1})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Previous</button>`;
    }
    
    // Next button
    if (data.next_page_url) {
        html += `<button onclick="changePage(${data.current_page + 1})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Next</button>`;
    }
    
    html += '</div></div>';
    pagination.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadStudents();
}

function handleUpload(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('{{ route("hod.management.students.api.upload") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Upload completed! Created: ${data.created}, Skipped: ${data.skipped}`);
            loadStudents();
            loadStatistics();
            e.target.reset();
        } else {
            alert('Upload failed: ' + data.message);
        }
    });
}

function downloadTemplate() {
    window.location.href = '{{ route("hod.management.students.api.template") }}';
}

let currentStudentId = null;

function viewStudent(id) {
    currentStudentId = id;
    fetch(`{{ route("hod.management.students.api.show", ":id") }}`.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.data;
                const storageBase = '{{ asset("storage") }}';
                const imageUrl = student.reference_image_path 
                    ? `${storageBase}/${student.reference_image_path}` 
                    : null;
                
                const content = `
                    <!-- Student Image Section -->
                    <div class="flex flex-col items-center mb-6 pb-6 border-b border-gray-200">
                        <div class="relative mb-4">
                            ${imageUrl 
                                ? `<img src="${imageUrl}" alt="${student.full_name}" class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-lg">`
                                : `<div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center border-4 border-blue-200 shadow-lg">
                                    <span class="text-3xl font-bold text-white">${student.full_name ? student.full_name.substring(0, 2).toUpperCase() : 'N/A'}</span>
                                </div>`
                            }
                            ${student.face_registration_enabled 
                                ? `<div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-2 border-4 border-white">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>`
                                : ''
                            }
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-1">${student.full_name}</h4>
                        <p class="text-base text-gray-600 font-mono">${student.matric_number}</p>
                    </div>

                    <!-- Student Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h5 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">Personal Information</h5>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Matric Number</span>
                                    <span class="text-sm font-mono text-gray-900 font-semibold">${student.matric_number}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Full Name</span>
                                    <span class="text-sm text-gray-900 font-semibold text-right max-w-xs">${student.full_name}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Email</span>
                                    <span class="text-sm text-gray-900 text-right max-w-xs break-words">${student.email}</span>
                                </div>
                                <div class="flex justify-between items-start py-3">
                                    <span class="text-sm font-medium text-gray-500">Phone</span>
                                    <span class="text-sm text-gray-900">${student.phone || 'Not provided'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-lg font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-200">Academic Information</h5>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Academic Level</span>
                                    <span class="text-sm text-gray-900 font-semibold">${student.academic_level}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Department</span>
                                    <span class="text-sm text-gray-900 font-semibold">${student.department}</span>
                                </div>
                                <div class="flex justify-between items-start py-3 border-b border-gray-200 last:border-b-0">
                                    <span class="text-sm font-medium text-gray-500">Status</span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${student.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${student.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                                <div class="flex justify-between items-start py-3">
                                    <span class="text-sm font-medium text-gray-500">Face Registration</span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${student.face_registration_enabled ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">
                                        ${student.face_registration_enabled ? 'Enabled' : 'Disabled'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${imageUrl ? `
                    <!-- Image Preview Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 mb-4">Registered Image</h5>
                        <div class="bg-gray-50 rounded-lg p-4 flex justify-center">
                            <img src="${imageUrl}" alt="${student.full_name} - Registered Image" class="max-w-full h-auto rounded-lg shadow-md border border-gray-200" style="max-height: 300px;">
                        </div>
                    </div>
                    ` : ''}
                `;
                document.getElementById('viewModalContent').innerHTML = content;
                document.getElementById('viewModal').classList.remove('hidden');
            } else {
                alert('Failed to load student details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load student details');
        });
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    currentStudentId = null;
}

function goToAdvancedDetails() {
    if (currentStudentId) {
        window.location.href = `{{ route("hod.management.students.show", ":id") }}`.replace(':id', currentStudentId);
    }
}

function openEditModal(id) {
    currentStudentId = id;
    fetch(`{{ route("hod.management.students.api.show", ":id") }}`.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.data;
                document.getElementById('editId').value = student.id;
                document.getElementById('editFullName').value = student.full_name;
                document.getElementById('editMatricNumber').value = student.matric_number;
                document.getElementById('editEmail').value = student.email;
                document.getElementById('editPhone').value = student.phone || '';
                document.getElementById('editLevel').value = student.academic_level_id || '';
                document.getElementById('editIsActive').checked = student.is_active;
                document.getElementById('editFaceRegistration').checked = student.face_registration_enabled;
                document.getElementById('editModal').classList.remove('hidden');
            } else {
                alert('Failed to load student data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load student data');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentStudentId = null;
}

function handleUpdate(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    const formData = {
        matric_number: document.getElementById('editMatricNumber').value,
        full_name: document.getElementById('editFullName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        academic_level_id: document.getElementById('editLevel').value,
        is_active: document.getElementById('editIsActive').checked,
        face_registration_enabled: document.getElementById('editFaceRegistration').checked,
    };
    
    fetch(`{{ route("hod.management.students.api.update", ":id") }}`.replace(':id', id), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Student updated successfully');
            closeEditModal();
            loadStudents();
            loadStatistics();
        } else {
            alert('Update failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Update failed');
    });
}

function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        fetch(`{{ route("hod.management.students.api.delete", ":id") }}`.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student deleted successfully');
                loadStudents();
                loadStatistics();
            } else {
                alert('Delete failed: ' + data.message);
            }
        });
    }
}

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
</script>
@endsection

