@extends('layouts.superadmin')

@section('title', 'Course Assignment Management')
@section('page-title', 'Course Assignment Management')
@section('page-description', 'Assign courses to lecturers across all departments')

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
    <div class="bg-white shadow-lg border-l-4 border-green-500">
        <div class="max-w-7xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-green-800 break-words">Course Assignment Management</h1>
                    <p class="mt-1 text-xs sm:text-sm text-green-600 font-medium">Assign courses to lecturers across all departments</p>
                </div>
                <button onclick="refreshData()" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-green-300 rounded-lg shadow-sm text-xs sm:text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-all sm:flex-shrink-0">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Department Filter -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-green-200">
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Department</label>
            <select id="departmentFilter" class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Lecturers</p>
                        <p class="text-3xl font-bold text-gray-900" id="statLecturers">-</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Courses</p>
                        <p class="text-3xl font-bold text-gray-900" id="statCourses">-</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Active Assignments</p>
                        <p class="text-3xl font-bold text-gray-900" id="statAssignments">-</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Unassigned Courses</p>
                        <p class="text-3xl font-bold text-gray-900" id="statUnassigned">-</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Assignment Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Lecturers Panel -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden card-hover border border-gray-100">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Select Lecturer</h2>
                        <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative mb-4">
                        <input type="text" id="lecturerSearch" placeholder="Search lecturers..." 
                               class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div id="lecturersList" class="space-y-2 max-h-96 overflow-y-auto smooth-scroll">
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-sm">Loading lecturers...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Panel -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden card-hover border border-gray-100">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Select Courses</h2>
                        <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative mb-4">
                        <input type="text" id="courseSearch" placeholder="Search courses..." 
                               class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div id="coursesList" class="space-y-2 max-h-96 overflow-y-auto smooth-scroll">
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="text-sm">Loading courses...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Panel -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden card-hover border border-gray-100">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Actions</h2>
                        <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Selected Lecturer</p>
                        <p id="selectedLecturer" class="text-base font-semibold text-gray-800">None selected</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Selected Courses</p>
                        <div id="selectedCourses" class="space-y-1 max-h-48 overflow-y-auto smooth-scroll">
                            <p class="text-sm text-gray-400 italic">No courses selected</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t-2 border-gray-200 space-y-3">
                        <button onclick="assignCourses()" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-semibold shadow-md hover:shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Assign Courses
                        </button>
                        <button onclick="unassignCourses()" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition-all font-semibold shadow-md hover:shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Unassign Courses
                        </button>
                        <button onclick="viewLecturerCourses()" 
                                class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-semibold shadow-md hover:shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Assignments
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments Table -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Current Assignments Overview</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lecturer</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Staff ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Assigned Courses</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTable" class="bg-white divide-y divide-gray-200">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- View Assignment Modal -->
        <div id="viewAssignmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
            <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-2xl rounded-2xl bg-white my-8">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white" id="modalTitle">Assignment Details</h3>
                        <button onclick="closeViewModal()" class="text-white hover:text-gray-200 transition p-1.5 hover:bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="viewModalContent" class="p-6 max-h-[70vh] overflow-y-auto">
                    <!-- Content will be loaded here -->
                </div>
                <div class="px-6 pb-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                    <button onclick="closeViewModal()" 
                            class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
        

<!-- Create Course Modal -->
<div id="createCourseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-2xl shadow-2xl rounded-2xl bg-white my-8">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-white">Create New Course</h3>
                <button onclick="closeCreateCourseModal()" class="text-white hover:text-gray-200 transition p-2 hover:bg-white/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="createCourseForm" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Course Code *</label>
                    <input type="text" name="course_code" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                           placeholder="e.g., CSC101">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Course Name *</label>
                    <input type="text" name="course_name" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                           placeholder="e.g., Introduction to Computer Science">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                              placeholder="Course description..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Departments *</label>
                    <select name="department_ids[]" multiple required id="createCourseDepartments"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="">Select Departments</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Academic Level *</label>
                    <select name="academic_level_id" required id="createCourseLevel"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="">Select Level</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Semester *</label>
                    <select name="semester_id" required id="createCourseSemester"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="">Select Semester</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Credit Units *</label>
                    <input type="number" name="credit_units" required min="1" max="10" value="3"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" checked
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="text-sm font-semibold text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeCreateCourseModal()" 
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition font-semibold shadow-md hover:shadow-lg">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Course Details Modal -->
<div id="courseDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-2xl shadow-2xl rounded-2xl bg-white my-8">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Course Assignment Details</h3>
                <button onclick="closeCourseDetailsModal()" class="text-white hover:text-gray-200 transition p-1.5 hover:bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="courseDetailsContent" class="p-6 space-y-4">
            <!-- Course details will be loaded here -->
        </div>
        <div class="px-6 pb-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <button onclick="closeCourseDetailsModal()" 
                    class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let selectedLecturerId = null;
let selectedCourseIds = new Set();
const baseUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.index") : route("superadmin.course-assignment.index") }}';
const apiUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.api.lecturers") : route("superadmin.course-assignment.api.lecturers") }}';
const coursesApiUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.api.courses") : route("superadmin.course-assignment.api.courses") }}';
const assignUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.api.assign") : route("superadmin.course-assignment.api.assign") }}';
const unassignUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.api.unassign") : route("superadmin.course-assignment.api.unassign") }}';
const createCourseUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.api.create-course") : route("superadmin.course-assignment.api.create-course") }}';
const dropdownDataUrl = '{{ auth("hod")->check() ? route("hod.management.course-assignment.api.dropdown-data") : route("superadmin.course-assignment.api.dropdown-data") }}';

document.addEventListener('DOMContentLoaded', function() {
    loadDropdownData();
    loadLecturers();
    loadCourses();
    loadAssignments();

    // Listen for department filter changes
    document.getElementById('departmentFilter')?.addEventListener('change', function() {
        loadLecturers();
        loadCourses();
    });
});

function loadDropdownData() {
    fetch(dropdownDataUrl)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Populate create course form dropdowns
                const deptSelect = document.getElementById('createCourseDepartments');
                const levelSelect = document.getElementById('createCourseLevel');
                const semesterSelect = document.getElementById('createCourseSemester');
                
                if (deptSelect) {
                    deptSelect.innerHTML = '<option value="">Select Departments</option>' +
                        data.departments.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
                }
                
                if (levelSelect) {
                    levelSelect.innerHTML = '<option value="">Select Level</option>' +
                        data.academic_levels.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
                }
                
                if (semesterSelect) {
                    semesterSelect.innerHTML = '<option value="">Select Semester</option>' +
                        data.semesters.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
                }
            }
        })
        .catch(error => {
            console.error('Error loading dropdown data:', error);
            showNotification('Failed to load dropdown data: ' + error.message, 'error');
        });
}

function loadLecturers() {
    const departmentId = document.getElementById('departmentFilter')?.value || '';
    const container = document.getElementById('lecturersList');
    const statElement = document.getElementById('statLecturers');
    
    // Show loading state
    if (container) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400"><svg class="w-12 h-12 mx-auto mb-2 opacity-50 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><p class="text-sm">Loading lecturers...</p></div>';
    }
    
    fetch(`${apiUrl}?department_id=${departmentId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (statElement) {
                    statElement.textContent = data.lecturers.length;
                }
                
                if (container) {
                    if (data.lecturers.length === 0) {
                        container.innerHTML = '<div class="text-center py-8 text-gray-400"><p class="text-sm">No lecturers found</p></div>';
                    } else {
                        container.innerHTML = data.lecturers.map(lecturer => `
                            <div class="lecturer-item p-4 border-2 rounded-lg cursor-pointer transition-all ${selectedLecturerId === lecturer.id ? 'selected bg-blue-50 border-blue-500' : 'border-gray-200 hover:border-blue-300'}" 
                                 onclick="selectLecturer(${lecturer.id}, '${lecturer.name.replace(/'/g, "\\'")}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 text-sm">${lecturer.name}</p>
                                        <p class="text-xs text-gray-500 mt-1">${lecturer.staff_id}</p>
                                    </div>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">${lecturer.assigned_courses_count || 0}</span>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            } else {
                throw new Error(data.error || data.message || 'Failed to load lecturers');
            }
        })
        .catch(error => {
            console.error('Error loading lecturers:', error);
            if (container) {
                container.innerHTML = `<div class="text-center py-8 text-red-400"><p class="text-sm">Error: ${error.message}</p><button onclick="loadLecturers()" class="mt-2 text-blue-600 hover:underline text-xs">Retry</button></div>`;
            }
            if (statElement) {
                statElement.textContent = '-';
            }
            showNotification('Failed to load lecturers: ' + error.message, 'error');
        });
}

function loadCourses() {
    const departmentId = document.getElementById('departmentFilter')?.value || '';
    const container = document.getElementById('coursesList');
    const statCoursesElement = document.getElementById('statCourses');
    const statUnassignedElement = document.getElementById('statUnassigned');
    
    // Show loading state
    if (container) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400"><svg class="w-12 h-12 mx-auto mb-2 opacity-50 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><p class="text-sm">Loading courses...</p></div>';
    }
    
    fetch(`${coursesApiUrl}?department_id=${departmentId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (statCoursesElement) {
                    statCoursesElement.textContent = data.courses.length;
                }
                const unassigned = data.courses.filter(c => (c.assigned_lecturers_count || 0) === 0).length;
                if (statUnassignedElement) {
                    statUnassignedElement.textContent = unassigned;
                }
                
                if (container) {
                    if (data.courses.length === 0) {
                        container.innerHTML = '<div class="text-center py-8 text-gray-400"><p class="text-sm">No courses found. <button onclick="openCreateCourseModal()" class="text-green-600 hover:underline">Create one?</button></p></div>';
                    } else {
                        container.innerHTML = data.courses.map(course => `
                            <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all ${selectedCourseIds.has(course.id) ? 'bg-purple-50 border-purple-500' : 'border-gray-200 hover:border-purple-300'} course-item">
                                <input type="checkbox" value="${course.id}" class="mt-1 mr-3 w-5 h-5 text-purple-600 course-checkbox" 
                                       onchange="toggleCourse(${course.id}, this.checked)" ${selectedCourseIds.has(course.id) ? 'checked' : ''}>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">${course.course_code}</p>
                                    <p class="text-xs text-gray-600 mt-1">${course.course_name}</p>
                                    <div class="flex items-center mt-2 space-x-2">
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium">${course.assigned_lecturers_count || 0} lecturers</span>
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">${(course.departments && course.departments.length ? course.departments.join(', ') : 'N/A')}</span>
                                    </div>
                                </div>
                            </label>
                        `).join('');
                    }
                }
            } else {
                throw new Error(data.error || data.message || 'Failed to load courses');
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            if (container) {
                container.innerHTML = `<div class="text-center py-8 text-red-400"><p class="text-sm">Error: ${error.message}</p><button onclick="loadCourses()" class="mt-2 text-blue-600 hover:underline text-xs">Retry</button></div>`;
            }
            if (statCoursesElement) {
                statCoursesElement.textContent = '-';
            }
            if (statUnassignedElement) {
                statUnassignedElement.textContent = '-';
            }
            showNotification('Failed to load courses: ' + error.message, 'error');
        });
}

function loadAssignments() {
    const tbody = document.getElementById('assignmentsTable');
    const statElement = document.getElementById('statAssignments');
    
    // Show loading state
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-gray-400"><svg class="w-12 h-12 mx-auto mb-2 opacity-50 animate-spin inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><p class="text-sm">Loading assignments...</p></td></tr>';
    }
    
    fetch(apiUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.lecturers) {
                const activeAssignments = data.lecturers.reduce((sum, l) => sum + (l.assigned_courses_count || 0), 0);
                if (statElement) {
                    statElement.textContent = activeAssignments;
                }
                
                if (tbody) {
                    const assignedLecturers = data.lecturers.filter(l => (l.assigned_courses_count || 0) > 0);
                    if (assignedLecturers.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-gray-400"><p class="text-sm">No assignments yet</p></td></tr>';
                    } else {
                        tbody.innerHTML = assignedLecturers.map(lecturer => `
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">${lecturer.name}</div>
                                    <div class="text-sm text-gray-500">${lecturer.email}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">${lecturer.staff_id}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">${lecturer.assigned_courses_count || 0} courses</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="selectLecturer(${lecturer.id}, '${lecturer.name.replace(/'/g, "\\'")}')" 
                                            class="text-blue-600 hover:text-blue-900 font-semibold">View Details</button>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            } else {
                throw new Error(data.error || data.message || 'Failed to load assignments');
            }
        })
        .catch(error => {
            console.error('Error loading assignments:', error);
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-red-400"><p class="text-sm">Error: ${error.message}</p><button onclick="loadAssignments()" class="mt-2 text-blue-600 hover:underline text-xs">Retry</button></td></tr>`;
            }
            if (statElement) {
                statElement.textContent = '-';
            }
            showNotification('Failed to load assignments: ' + error.message, 'error');
        });
}

function selectLecturer(id, name) {
    selectedLecturerId = id;
    document.getElementById('selectedLecturer').textContent = name;
    loadLecturers();
    loadLecturerCourses(id);
}

function loadLecturerCourses(lecturerId) {
    // Optional: Load and highlight already assigned courses
}

function toggleCourse(courseId, checked) {
    if (checked) {
        selectedCourseIds.add(courseId);
    } else {
        selectedCourseIds.delete(courseId);
    }
    updateSelectedCourses();
}

function updateSelectedCourses() {
    const container = document.getElementById('selectedCourses');
    if (!container) return;
    
    if (selectedCourseIds.size === 0) {
        container.innerHTML = '<p class="text-sm text-gray-400 italic">No courses selected</p>';
        return;
    }
    
    const courseIds = Array.from(selectedCourseIds);
    fetch(coursesApiUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const selected = data.courses.filter(c => courseIds.includes(c.id));
                if (selected.length === 0) {
                    container.innerHTML = '<p class="text-sm text-gray-400 italic">No courses selected</p>';
                } else {
                    container.innerHTML = selected.map(course => `
                        <div class="flex items-center justify-between p-2 bg-white border border-gray-200 rounded text-xs">
                            <span class="font-medium text-gray-700">${course.course_code} - ${course.course_name}</span>
                            <button onclick="toggleCourse(${course.id}, false)" class="text-red-500 hover:text-red-700 ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `).join('');
                }
            }
        })
        .catch(error => {
            console.error('Error updating selected courses:', error);
            container.innerHTML = '<p class="text-sm text-red-400 italic">Error loading courses</p>';
        });
}

function assignCourses() {
    if (!selectedLecturerId || selectedCourseIds.size === 0) {
        showNotification('Please select a lecturer and at least one course', 'warning');
        return;
    }

    const submitBtn = event?.target || document.querySelector('button[onclick="assignCourses()"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Assigning...';
    }

    fetch(assignUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            lecturer_id: selectedLecturerId,
            course_ids: Array.from(selectedCourseIds)
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.error || err.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            selectedCourseIds.clear();
            updateSelectedCourses();
            loadLecturers();
            loadCourses();
            loadAssignments();
        } else {
            throw new Error(data.error || data.message || 'Failed to assign courses');
        }
    })
    .catch(error => {
        console.error('Error assigning courses:', error);
        showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Assign Courses';
        }
    });
}

function unassignCourses() {
    if (!selectedLecturerId || selectedCourseIds.size === 0) {
        showNotification('Please select a lecturer and at least one course', 'warning');
        return;
    }

    if (!confirm('Are you sure you want to unassign these courses?')) {
        return;
    }

    const submitBtn = event?.target || document.querySelector('button[onclick="unassignCourses()"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Unassigning...';
    }

    fetch(unassignUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            lecturer_id: selectedLecturerId,
            course_ids: Array.from(selectedCourseIds)
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.error || err.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            selectedCourseIds.clear();
            updateSelectedCourses();
            loadLecturers();
            loadCourses();
            loadAssignments();
        } else {
            throw new Error(data.error || data.message || 'Failed to unassign courses');
        }
    })
    .catch(error => {
        console.error('Error unassigning courses:', error);
        showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Unassign Courses';
        }
    });
}

function viewLecturerCourses() {
    if (!selectedLecturerId) {
        showNotification('Please select a lecturer first', 'warning');
        return;
    }
    
    // Note: This route URL generation is dynamic based on user role (HOD or Superadmin)
    // which is handled by the server-side Blade logic.
    const lecturerCoursesUrl = `{{ auth("hod")->check() ? route("hod.management.course-assignment.api.lecturer.courses", ["lecturerId" => ":id"]) : route("superadmin.course-assignment.api.lecturer.courses", ["lecturerId" => ":id"]) }}`.replace(':id', selectedLecturerId);
    
    const modal = document.getElementById('viewAssignmentModal');
    const modalContent = document.getElementById('viewModalContent');
    
    if (modalContent) {
        modalContent.innerHTML = '<div class="text-center py-8"><svg class="w-12 h-12 mx-auto mb-2 opacity-50 animate-spin inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><p class="text-gray-400">Loading...</p></div>';
    }
    
    if (modal) {
        modal.classList.remove('hidden');
    }
    
    fetch(lecturerCoursesUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const modalTitle = document.getElementById('modalTitle');
                if (modalTitle) {
                    modalTitle.textContent = 'Assigned Courses';
                }
                
                if (modalContent) {
                    if (data.courses.length === 0) {
                        modalContent.innerHTML = '<div class="text-center py-8 text-gray-400"><p>No courses assigned yet</p></div>';
                    } else {
                        modalContent.innerHTML = `
                            <div class="space-y-3">
                                ${data.courses.map((course, idx) => `
                                    <div class="border border-gray-200 rounded-lg hover:border-green-300 hover:shadow-md transition-all p-4 bg-white">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="font-semibold text-base text-gray-900">${course.course_code}</p>
                                                    ${course.is_active ? '<span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Active</span>' : '<span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-medium">Inactive</span>'}
                                                </div>
                                                <p class="text-sm text-gray-700 mb-2 line-clamp-1">${course.course_name}</p>
                                                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">${course.departments && course.departments.length ? course.departments.join(', ') : 'N/A'}</span>
                                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded">${course.academic_level || 'N/A'}</span>
                                                    <span class="px-2 py-0.5 bg-gray-50 text-gray-600 rounded">${course.credit_units || 'N/A'} Units</span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-2">Assigned: ${course.assigned_at ? new Date(course.assigned_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                            </div>
                                            <button onclick="showCourseDetails(${course.id}, this)" 
                                                    data-course='${JSON.stringify(course).replace(/'/g, "\\'")}'
                                                    class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition whitespace-nowrap">
                                                More Details
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    }
                }
            } else {
                throw new Error(data.error || data.message || 'Failed to load courses');
            }
        })
        .catch(error => {
            console.error('Error loading lecturer courses:', error);
            if (modalContent) {
                modalContent.innerHTML = `<div class="text-center py-8 text-red-400"><p>Error: ${error.message}</p><button onclick="viewLecturerCourses()" class="mt-2 text-blue-600 hover:underline text-xs">Retry</button></div>`;
            }
            showNotification('Failed to load courses: ' + error.message, 'error');
        });
}

function openCreateCourseModal() {
    document.getElementById('createCourseModal').classList.remove('hidden');
}

function closeCreateCourseModal() {
    document.getElementById('createCourseModal').classList.add('hidden');
    document.getElementById('createCourseForm').reset();
}

// Create Course Form Submission
const createCourseForm = document.getElementById('createCourseForm');
if (createCourseForm) {
    createCourseForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        // Ensure multi-select departments are sent as an array of integers
        const deptSelectEl = document.getElementById('createCourseDepartments');
        const selectedDepts = Array.from(deptSelectEl?.selectedOptions || []).map(o => parseInt(o.value, 10)).filter(n => !isNaN(n));
        data.department_ids = selectedDepts;
        data.is_active = data.is_active === 'on';
        data.credit_units = parseInt(data.credit_units);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Creating...';
        }

        fetch(createCourseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                showNotification(result.message, 'success');
                closeCreateCourseModal();
                loadCourses();
                loadAssignments();
            } else {
                throw new Error(result.message || result.error || 'Failed to create course');
            }
        })
        .catch(error => {
            console.error('Error creating course:', error);
            showNotification('Error: ' + error.message, 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Course';
            }
        });
    });
}

function closeViewModal() {
    document.getElementById('viewAssignmentModal').classList.add('hidden');
}

function showCourseDetails(courseId, buttonElement) {
    const modal = document.getElementById('courseDetailsModal');
    const content = document.getElementById('courseDetailsContent');
    
    // Get course data from button's data attribute
    const button = buttonElement || document.querySelector(`button[data-course][onclick*="showCourseDetails(${courseId})"]`);
    const courseDataStr = button?.getAttribute('data-course');
    
    if (!courseDataStr) {
        content.innerHTML = '<div class="text-center py-8 text-red-400">Error loading course details</div>';
        modal.classList.remove('hidden');
        return;
    }
    
    let courseData;
    try {
        courseData = JSON.parse(courseDataStr);
    } catch (e) {
        content.innerHTML = '<div class="text-center py-8 text-red-400">Error parsing course data</div>';
        modal.classList.remove('hidden');
        return;
    }
    const assignedDate = courseData.assigned_at ? new Date(courseData.assigned_at) : null;
    const formattedDate = assignedDate ? assignedDate.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) : 'N/A';
    
    content.innerHTML = `
        <div class="space-y-4">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4 border-l-4 border-green-600">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">${courseData.course_code}</h4>
                        <p class="text-sm text-gray-700 mt-1">${courseData.course_name}</p>
                    </div>
                    ${courseData.is_active ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Active</span>' : '<span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">Inactive</span>'}
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white border border-gray-200 rounded-lg p-3">
                    <div class="text-xs text-gray-500 mb-1">Academic Level</div>
                    <div class="text-sm font-semibold text-gray-900">${courseData.academic_level || 'N/A'}</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-3">
                    <div class="text-xs text-gray-500 mb-1">Credit Units</div>
                    <div class="text-sm font-semibold text-gray-900">${courseData.credit_units || 'N/A'}</div>
                </div>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-lg p-3">
                <div class="text-xs text-gray-500 mb-1">Departments</div>
                <div class="flex flex-wrap gap-1.5 mt-1">
                    ${courseData.departments && courseData.departments.length ? 
                        courseData.departments.map(d => `<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">${d}</span>`).join('') 
                        : '<span class="text-sm text-gray-600">N/A</span>'
                    }
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Assignment Information
                </h5>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Assigned Date:</span>
                        <span class="font-medium text-gray-900">${formattedDate}</span>
                    </div>
                    ${courseData.assigned_by_name && courseData.assigned_by_name !== 'N/A' ? `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Assigned By:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="font-medium text-gray-900">${courseData.assigned_by_name}</span>
                            ${courseData.assigned_by_role ? `<span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">${courseData.assigned_by_role}</span>` : ''}
                        </div>
                    </div>
                    ` : ''}
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Assignment Status:</span>
                        <span class="font-medium ${courseData.is_active ? 'text-green-700' : 'text-gray-600'}">${courseData.is_active ? 'Active' : 'Inactive'}</span>
                    </div>
                    ${assignedDate ? `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium text-gray-900">${Math.floor((new Date() - assignedDate) / (1000 * 60 * 60 * 24))} days</span>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeCourseDetailsModal() {
    document.getElementById('courseDetailsModal').classList.add('hidden');
}

function refreshData() {
    loadLecturers();
    loadCourses();
    loadAssignments();
    showNotification('Data refreshed successfully', 'success');
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Search filters
document.getElementById('lecturerSearch')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('#lecturersList .lecturer-item').forEach(div => {
        const text = div.textContent.toLowerCase();
        div.style.display = text.includes(search) ? 'block' : 'none';
    });
});

document.getElementById('courseSearch')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('#coursesList .course-item').forEach(div => {
        const text = div.textContent.toLowerCase();
        div.style.display = text.includes(search) ? 'block' : 'none';
    });
});
</script>
</div>
</div>
@endsection


