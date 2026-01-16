@extends('layouts.superadmin')

@section('title', 'Academic Structure')
@section('page-title', 'Academic Structure')
@section('page-description', 'Manage departments, courses, academic levels, and classrooms')

@push('styles')
<style>
    .tab-button {
        transition: all 0.3s ease;
    }
    .tab-button.active {
        border-bottom: 3px solid;
        border-color: #10b981;
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

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

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6" x-data="{ activeTab: 'departments' }" x-init="setTimeout(() => { if (activeTab === 'classrooms') { loadClassrooms(); window.classroomsTabInitialized = true; } }, 500)">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center stat-card">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['departments'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Departments</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center stat-card">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['courses'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Courses</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center stat-card">
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['academic_levels'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Academic Levels</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-4 flex flex-col items-center stat-card">
            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['classrooms'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Classrooms</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Management Panel -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'departments'" 
                                :class="activeTab === 'departments' ? 'tab-button active text-green-600 border-green-600' : 'tab-button text-gray-500 hover:text-gray-700'"
                                class="px-6 py-3 font-medium text-sm border-b-3 transition">
                            Departments
                        </button>
                        <button @click="activeTab = 'courses'" 
                                :class="activeTab === 'courses' ? 'tab-button active text-blue-600 border-blue-600' : 'tab-button text-gray-500 hover:text-gray-700'"
                                class="px-6 py-3 font-medium text-sm border-b-3 transition">
                            Courses
                        </button>
                        <button @click="activeTab = 'levels'" 
                                :class="activeTab === 'levels' ? 'tab-button active text-purple-600 border-purple-600' : 'tab-button text-gray-500 hover:text-gray-700'"
                                class="px-6 py-3 font-medium text-sm border-b-3 transition">
                            Academic Levels
                        </button>
                        <button @click="activeTab = 'classrooms'; if (!window.classroomsTabInitialized) { setTimeout(() => loadClassrooms(), 200); window.classroomsTabInitialized = true; }" 
                                :class="activeTab === 'classrooms' ? 'tab-button active text-orange-600 border-orange-600' : 'tab-button text-gray-500 hover:text-gray-700'"
                                class="px-6 py-3 font-medium text-sm border-b-3 transition">
                            Classrooms
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Departments Tab -->
                    <div x-show="activeTab === 'departments'" 
                         x-init="loadDepartments()"
                         x-cloak>
                        <div id="departments-section"></div>
                    </div>

                    <!-- Courses Tab -->
                    <div x-show="activeTab === 'courses'" 
                         x-init="loadCourses()"
                         x-cloak>
                        <div id="courses-section"></div>
                    </div>

                    <!-- Academic Levels Tab -->
                    <div x-show="activeTab === 'levels'" 
                         x-init="loadAcademicLevels()"
                         x-cloak>
                        <div id="academic-levels-section"></div>
                    </div>

                    <!-- Classrooms Tab -->
                    <div x-show="activeTab === 'classrooms'" 
                         x-data="{ loaded: false }"
                         x-init="$watch('activeTab', value => { if (value === 'classrooms' && !loaded) { loadClassrooms(); loaded = true; } })"
                         x-cloak>
                        <div id="classrooms-section">
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm">Loading classrooms...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Recent Activity -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Recent Activity
                </h3>
                <div class="space-y-3" id="recent-activity">
                    @if(isset($recentActivity) && count($recentActivity) > 0)
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if($activity['icon'] === 'building')
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'book')
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    @elseif($activity['icon'] === 'chalkboard')
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Academic Structure Management App
(function() {
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        loadDropdownData();
    });

    // Load dropdown data for forms
    function loadDropdownData() {
        axios.get('/superadmin/academic-structure/dropdown-data')
            .then(response => {
                window.academicDropdownData = response.data.data;
            })
            .catch(error => {
                console.error('Error loading dropdown data:', error);
            });
    }

    // Departments Management
    function loadDepartments(search = '', status = '') {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        
        axios.get(`/superadmin/departments?${params}`)
            .then(response => {
                renderDepartmentsTable(response.data);
            })
            .catch(error => {
                console.error('Error loading departments:', error);
                showToast('Error loading departments', 'error');
            });
    }

    function renderDepartmentsTable(data) {
        const section = document.getElementById('departments-section');
        if (!section) return;
        
        section.innerHTML = `
            <div class="mb-4 flex justify-between items-center">
                <input type="text" id="dept-search" placeholder="Search departments..." 
                       class="px-4 py-2 border rounded-lg w-64" 
                       onkeyup="loadDepartments(this.value, document.getElementById('dept-status').value)">
                <select id="dept-status" class="px-4 py-2 border rounded-lg"
                        onchange="loadDepartments(document.getElementById('dept-search').value, this.value)">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button onclick="openDepartmentModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Add Department
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="departments-table-body">
                        ${(data.data || []).map(dept => `
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">${dept.name}</td>
                                <td class="px-4 py-3 whitespace-nowrap">${dept.code}</td>
                                <td class="px-4 py-3">${dept.description || '-'}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full ${dept.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${dept.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button onclick="editDepartment(${dept.id})" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                    <button onclick="deleteDepartment(${dept.id})" class="text-red-600 hover:text-red-800">Delete</button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                ${renderPagination(data)}
            </div>
        `;
    }

    // Similar functions for Courses, Academic Levels, and Classrooms
    function loadCourses(search = '', department = '', level = '', status = '') {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (department) params.append('department_id', department);
        if (level) params.append('academic_level_id', level);
        if (status) params.append('status', status);
        
        axios.get(`/superadmin/courses?${params}`)
            .then(response => {
                renderCoursesTable(response.data);
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                showToast('Error loading courses', 'error');
            });
    }

    function renderCoursesTable(data) {
        const section = document.getElementById('courses-section');
        if (!section) return;
        
        section.innerHTML = `
            <div class="mb-4">
                <button onclick="openCourseModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add Course
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="courses-table-body">
                        ${(data.data || []).map(course => `
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">${course.course_code}</td>
                                <td class="px-4 py-3">${course.course_name}</td>
                                <td class="px-4 py-3">${course.department?.name || '-'}</td>
                                <td class="px-4 py-3">${course.academic_level?.name || '-'}</td>
                                <td class="px-4 py-3">${course.credit_units}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button onclick="editCourse(${course.id})" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                    <button onclick="deleteCourse(${course.id})" class="text-red-600 hover:text-red-800">Delete</button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                ${renderPagination(data)}
            </div>
        `;
    }

    function loadAcademicLevels(search = '', status = '') {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        
        axios.get(`/superadmin/academic-levels?${params}`)
            .then(response => {
                renderAcademicLevelsTable(response.data);
            })
            .catch(error => {
                console.error('Error loading academic levels:', error);
                showToast('Error loading academic levels', 'error');
            });
    }

    function renderAcademicLevelsTable(data) {
        const section = document.getElementById('academic-levels-section');
        if (!section) return;
        
        section.innerHTML = `
            <div class="mb-4">
                <button onclick="openAcademicLevelModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Add Academic Level
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="academic-levels-table-body">
                        ${(data.data || []).map(level => `
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">${level.level_number}</td>
                                <td class="px-4 py-3">${level.name}</td>
                                <td class="px-4 py-3">${level.description || '-'}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full ${level.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${level.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button onclick="editAcademicLevel(${level.id})" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                    <button onclick="deleteAcademicLevel(${level.id})" class="text-red-600 hover:text-red-800">Delete</button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                ${renderPagination(data)}
            </div>
        `;
    }

    function loadClassrooms(search = '', course = '', lecturer = '', status = '') {
        const section = document.getElementById('classrooms-section');
        if (section) {
            section.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 animate-spin text-gray-300" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm">Loading classrooms...</p>
                </div>
            `;
        }
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (course) params.append('course_id', course);
        if (lecturer) params.append('lecturer_id', lecturer);
        if (status) params.append('status', status);
        
        axios.get(`/superadmin/classrooms?${params}`)
            .then(response => {
                if (response.data && response.data.success !== false) {
                    renderClassroomsTable(response.data);
                } else {
                    throw new Error(response.data?.message || 'Failed to load classrooms');
                }
            })
            .catch(error => {
                console.error('Error loading classrooms:', error);
                const section = document.getElementById('classrooms-section');
                if (section) {
                    section.innerHTML = `
                        <div class="text-center py-8">
                            <div class="text-red-400 mb-4">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-red-600 font-medium mb-2">Failed to load classrooms</p>
                            <p class="text-sm text-gray-500 mb-4">${error.response?.data?.message || error.message || 'An error occurred'}</p>
                            <button onclick="loadClassrooms()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                Retry
                            </button>
                        </div>
                    `;
                }
                showToast('Error loading classrooms: ' + (error.response?.data?.message || error.message), 'error');
            });
    }

    function renderClassroomsTable(data) {
        const section = document.getElementById('classrooms-section');
        if (!section) return;
        
        if (!data || !data.data || data.data.length === 0) {
            section.innerHTML = `
                <div class="mb-4 flex justify-between items-center">
                    <div class="flex gap-3 flex-1">
                        <input type="text" id="classroom-search" placeholder="Search classrooms..." 
                               class="px-4 py-2 border rounded-lg flex-1" 
                               onkeyup="loadClassrooms(this.value, document.getElementById('classroom-course').value, document.getElementById('classroom-lecturer').value, document.getElementById('classroom-status').value)">
                        <select id="classroom-course" class="px-4 py-2 border rounded-lg"
                                onchange="loadClassrooms(document.getElementById('classroom-search').value, this.value, document.getElementById('classroom-lecturer').value, document.getElementById('classroom-status').value)">
                            <option value="">All Courses</option>
                        </select>
                        <select id="classroom-status" class="px-4 py-2 border rounded-lg"
                                onchange="loadClassrooms(document.getElementById('classroom-search').value, document.getElementById('classroom-course').value, document.getElementById('classroom-lecturer').value, this.value)">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button onclick="openClassroomModal()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        Add Classroom
                    </button>
                </div>
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="text-lg font-medium">No classrooms found</p>
                    <p class="text-sm mt-1">Get started by creating a new classroom</p>
                </div>
            `;
            return;
        }
        
        section.innerHTML = `
            <div class="mb-4 flex justify-between items-center">
                <div class="flex gap-3 flex-1">
                    <input type="text" id="classroom-search" placeholder="Search classrooms..." 
                           class="px-4 py-2 border rounded-lg flex-1" 
                           onkeyup="loadClassrooms(this.value, document.getElementById('classroom-course').value, document.getElementById('classroom-lecturer').value, document.getElementById('classroom-status').value)">
                    <select id="classroom-course" class="px-4 py-2 border rounded-lg"
                            onchange="loadClassrooms(document.getElementById('classroom-search').value, this.value, document.getElementById('classroom-lecturer').value, document.getElementById('classroom-status').value)">
                        <option value="">All Courses</option>
                    </select>
                    <select id="classroom-status" class="px-4 py-2 border rounded-lg"
                            onchange="loadClassrooms(document.getElementById('classroom-search').value, document.getElementById('classroom-course').value, document.getElementById('classroom-lecturer').value, this.value)">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button onclick="openClassroomModal()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Add Classroom
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lecturer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PIN</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="classrooms-table-body">
                        ${(data.data || []).map(classroom => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">${classroom.class_name}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">${classroom.course?.course_name || '-'}</div>
                                    <div class="text-xs text-gray-500">${classroom.course?.course_code || ''}</div>
                                </td>
                                <td class="px-4 py-3">${classroom.lecturer?.user?.full_name || '-'}</td>
                                <td class="px-4 py-3 font-mono text-sm">${classroom.pin}</td>
                                <td class="px-4 py-3 text-sm">${classroom.schedule || '-'}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full ${classroom.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${classroom.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button onclick="viewClassroom(${classroom.id})" class="text-indigo-600 hover:text-indigo-800 mr-2" title="View Details">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button onclick="editClassroom(${classroom.id})" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                                    <button onclick="deleteClassroom(${classroom.id})" class="text-red-600 hover:text-red-800">Delete</button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                ${renderPagination(data)}
            </div>
        `;
    }

    function renderPagination(data) {
        if (!data.links || data.links.length <= 3) return '';
        
        return `
            <div class="mt-4 flex justify-center">
                <nav class="flex space-x-2">
                    ${data.links.map((link, index) => {
                        if (index === 0) {
                            return `<a href="${link.url || '#'}" onclick="event.preventDefault(); loadPage('${link.url}')" 
                                    class="px-3 py-2 border rounded ${link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                                    Previous
                                </a>`;
                        } else if (index === data.links.length - 1) {
                            return `<a href="${link.url || '#'}" onclick="event.preventDefault(); loadPage('${link.url}')" 
                                    class="px-3 py-2 border rounded ${link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                                    Next
                                </a>`;
                        } else {
                            return `<a href="${link.url || '#'}" onclick="event.preventDefault(); loadPage('${link.url}')" 
                                    class="px-3 py-2 border rounded ${link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                                    ${link.label}
                                </a>`;
                        }
                    }).join('')}
                </nav>
            </div>
        `;
    }

    // Global functions for buttons and Alpine
    window.loadDepartments = loadDepartments;
    window.loadCourses = loadCourses;
    window.loadAcademicLevels = loadAcademicLevels;
    window.loadClassrooms = loadClassrooms;

    // Modal and CRUD functions (placeholders - implement as needed)
    window.openDepartmentModal = function() { console.log('Open department modal'); };
    window.editDepartment = function(id) { console.log('Edit department', id); };
    window.deleteDepartment = function(id) {
        if (confirm('Are you sure you want to delete this department?')) {
            axios.delete(`/superadmin/departments/${id}`)
                .then(() => {
                    showToast('Department deleted successfully', 'success');
                    loadDepartments();
                })
                .catch(error => {
                    showToast(error.response?.data?.message || 'Error deleting department', 'error');
                });
        }
    };

    window.openCourseModal = function() { console.log('Open course modal'); };
    window.editCourse = function(id) { console.log('Edit course', id); };
    window.deleteCourse = function(id) {
        if (confirm('Are you sure you want to delete this course?')) {
            axios.delete(`/superadmin/courses/${id}`)
                .then(() => {
                    showToast('Course deleted successfully', 'success');
                    loadCourses();
                })
                .catch(error => {
                    showToast(error.response?.data?.message || 'Error deleting course', 'error');
                });
        }
    };

    window.openAcademicLevelModal = function() { console.log('Open academic level modal'); };
    window.editAcademicLevel = function(id) { console.log('Edit academic level', id); };
    window.deleteAcademicLevel = function(id) {
        if (confirm('Are you sure you want to delete this academic level?')) {
            axios.delete(`/superadmin/academic-levels/${id}`)
                .then(() => {
                    showToast('Academic level deleted successfully', 'success');
                    loadAcademicLevels();
                })
                .catch(error => {
                    showToast(error.response?.data?.message || 'Error deleting academic level', 'error');
                });
        }
    };

    window.viewClassroom = function(id) {
        window.location.href = `/superadmin/classes/${id}`;
    };
    window.openClassroomModal = function() { console.log('Open classroom modal'); };
    window.editClassroom = function(id) { console.log('Edit classroom', id); };
    window.deleteClassroom = function(id) {
        if (confirm('Are you sure you want to delete this classroom?')) {
            axios.delete(`/superadmin/classrooms/${id}`)
                .then(() => {
                    showToast('Classroom deleted successfully', 'success');
                    loadClassrooms();
                })
                .catch(error => {
                    showToast(error.response?.data?.message || 'Error deleting classroom', 'error');
                });
        }
    };

    // Toast notification helper
    function showToast(message, type = 'info') {
        // Use your existing toast system or console for now
        console.log(`[${type.toUpperCase()}] ${message}`);
        if (window.showToast) {
            window.showToast(message, type);
        }
    }
})();
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection

