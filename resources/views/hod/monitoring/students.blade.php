@extends('hod.layouts.app')

@section('title', 'Student Attendance Monitoring')

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
                    <h1 class="text-2xl sm:text-3xl font-bold text-green-800 break-words" style="font-family: 'Montserrat', sans-serif;">Student Attendance Monitoring</h1>
                    <p class="mt-1 text-xs sm:text-sm text-green-600 font-medium">Monitor student attendance patterns and performance trends</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:space-x-3 sm:flex-shrink-0">
                    <button id="refreshData" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-green-300 rounded-lg shadow-sm text-xs sm:text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh Data</span>
                    </button>
                    <button id="exportData" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Export Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Search Bar -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-green-200">
            <h3 class="text-lg font-semibold text-green-800 mb-4" style="font-family: 'Montserrat', sans-serif;">Search Students</h3>
            <form id="searchForm" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Search by student name or matric number..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                </div>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
                @if(isset($filters['search']) && !empty($filters['search']))
                <button type="button" 
                        id="clearSearch" 
                        class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear
                </button>
                @endif
            </form>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-l-4 border-green-200">
            <h3 class="text-lg font-semibold text-green-800 mb-4" style="font-family: 'Montserrat', sans-serif;">Filters</h3>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="academic_level" class="block text-sm font-semibold text-gray-700">Academic Level</label>
                    <select id="academic_level" name="academic_level" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Levels</option>
                        @foreach($filterOptions['academic_levels'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['academic_level'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="semester" class="block text-sm font-semibold text-gray-700">Semester</label>
                    <select id="semester" name="semester" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Semesters</option>
                        @foreach($filterOptions['semesters'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['semester'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="performance_filter" class="block text-sm font-semibold text-gray-700">Performance Filter</label>
                    <select id="performance_filter" name="performance_filter" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @foreach($filterOptions['performance_filters'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['performance_filter'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="risk_level" class="block text-sm font-semibold text-gray-700">Risk Level</label>
                    <select id="risk_level" name="risk_level" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @foreach($filterOptions['risk_levels'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['risk_level'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="per_page" class="block text-sm font-semibold text-gray-700">Results Per Page</label>
                    <select id="per_page" name="per_page" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="10" {{ $filters['per_page'] == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $filters['per_page'] == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $filters['per_page'] == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $filters['per_page'] == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $filters['per_page'] == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort_by" class="block text-sm font-semibold text-gray-700">Sort By</label>
                    <select id="sort_by" name="sort_by" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="average_attendance_rate" {{ $filters['sort_by'] == 'average_attendance_rate' ? 'selected' : '' }}>Attendance Rate</option>
                        <option value="student_name" {{ $filters['sort_by'] == 'student_name' ? 'selected' : '' }}>Student Name</option>
                        <option value="matric_number" {{ $filters['sort_by'] == 'matric_number' ? 'selected' : '' }}>Matric Number</option>
                        <option value="academic_level" {{ $filters['sort_by'] == 'academic_level' ? 'selected' : '' }}>Academic Level</option>
                        <option value="courses_enrolled" {{ $filters['sort_by'] == 'courses_enrolled' ? 'selected' : '' }}>Courses Enrolled</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-gray-700">Sort Order</label>
                    <select id="sort_order" name="sort_order" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="desc" {{ $filters['sort_order'] == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ $filters['sort_order'] == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                
                <div class="lg:col-span-4 flex justify-end space-x-3">
                    <button type="button" id="clearFilters" class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        Clear Filters
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Total Students</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ $attendanceAnalysis['total_students'] ?? 0 }}</dd>
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
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Average Attendance</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ number_format($attendanceAnalysis['average_attendance'] ?? 0, 1) }}%</dd>
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
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">At Risk Students</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ count($attendanceAnalysis['at_risk_students'] ?? []) }}</dd>
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
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Critical Risk</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ $attendanceAnalysis['risk_analysis']['high_risk'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6 border-l-4 border-green-300">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4">
                <h3 class="text-lg font-semibold text-green-800" style="font-family: 'Montserrat', sans-serif;">Course Attendance</h3>
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Time Period Filter -->
                    <div class="flex items-center gap-2">
                        <label for="timePeriodFilter" class="text-sm font-medium text-gray-700 whitespace-nowrap">Time Period:</label>
                        <select id="timePeriodFilter" name="time_period" class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-sm bg-white">
                            <option value="all" {{ ($filters['time_period'] ?? 'all') == 'all' ? 'selected' : '' }}>All Time</option>
                            <option value="semester" {{ ($filters['time_period'] ?? 'all') == 'semester' ? 'selected' : '' }}>Current Semester</option>
                            <option value="live" {{ ($filters['time_period'] ?? 'all') == 'live' ? 'selected' : '' }}>Live View (Today)</option>
                        </select>
                    </div>
                    <!-- Week Filter (shown when semester or all time is selected) -->
                    <div class="flex items-center gap-2" id="weekFilterContainer">
                        <label for="weekFilter" class="text-sm font-medium text-gray-700 whitespace-nowrap">Week:</label>
                        <select id="weekFilter" name="week" class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-sm bg-white">
                            <option value="">All Weeks</option>
                            @for($i = 1; $i <= 14; $i++)
                                <option value="{{ $i }}" {{ ($filters['week'] ?? '') == $i ? 'selected' : '' }}>Week {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                <div class="flex space-x-2">
                        <a href="{{ route('hod.monitoring.students.chart', 'attendance') }}?{{ http_build_query($filters) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="View chart in fullscreen">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Maximize
                    </a>
                </div>
            </div>
            </div>
            <div id="chartLoading" class="hidden flex items-center justify-center h-96">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                    <p class="mt-2 text-sm text-gray-600">Loading chart data...</p>
                </div>
            </div>
            <div class="h-96" role="img" aria-label="Course Attendance Chart" id="chartContainer">
                <canvas id="studentAttendanceChart"></canvas>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Student Performance Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                    <h3 class="text-lg font-medium text-gray-900">Student Performance</h3>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label for="performanceViewMode" class="text-xs font-medium text-gray-700 whitespace-nowrap">View:</label>
                            <select id="performanceViewMode" name="performance_view_mode" class="px-2 py-1 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 text-xs bg-white">
                                <option value="aggregated" {{ ($filters['performance_view_mode'] ?? 'aggregated') == 'aggregated' ? 'selected' : '' }}>Aggregated</option>
                                <option value="individual" {{ ($filters['performance_view_mode'] ?? 'aggregated') == 'individual' ? 'selected' : '' }}>Individual</option>
                            </select>
                        </div>
                    <a href="{{ route('hod.monitoring.students.chart', 'performance') }}?{{ http_build_query($filters) }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-green-300 rounded-lg text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="View student performance chart in fullscreen">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </a>
                </div>
                </div>
                <div id="performanceChartLoading" class="hidden flex items-center justify-center h-80">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
                        <p class="mt-2 text-xs text-gray-600">Loading chart data...</p>
                    </div>
                </div>
                <div class="h-80" role="img" aria-label="Student Performance Chart" id="performanceChartContainer">
                    <canvas id="studentPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Risk Analysis Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Risk Analysis</h3>
                    <a href="{{ route('hod.monitoring.students.chart', 'risk') }}?{{ http_build_query($filters) }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-green-300 rounded-lg text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="View risk analysis chart in fullscreen">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </a>
                </div>
                <div class="h-80" role="img" aria-label="Risk Analysis Chart">
                    <canvas id="riskAnalysisChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Student Performance Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Student Performance Details</h3>
                @if($pagination)
                <div class="text-sm text-gray-500">
                    Showing {{ $pagination->firstItem() ?? 0 }} to {{ $pagination->lastItem() ?? 0 }} of {{ $pagination->total() }} results
                </div>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('student_name')">
                                <div class="flex items-center space-x-1">
                                    <span>Student</span>
                                    @if($filters['sort_by'] == 'student_name')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('matric_number')">
                                <div class="flex items-center space-x-1">
                                    <span>Matric Number</span>
                                    @if($filters['sort_by'] == 'matric_number')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('academic_level')">
                                <div class="flex items-center space-x-1">
                                    <span>Level</span>
                                    @if($filters['sort_by'] == 'academic_level')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('average_attendance_rate')">
                                <div class="flex items-center space-x-1">
                                    <span>Attendance Rate</span>
                                    @if($filters['sort_by'] == 'average_attendance_rate')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('courses_enrolled')">
                                <div class="flex items-center space-x-1">
                                    <span>Courses</span>
                                    @if($filters['sort_by'] == 'courses_enrolled')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($studentMetrics as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student['student_name'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student['matric_number'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $student['academic_level'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $student['average_attendance_rate'] }}%</div>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $student['average_attendance_rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $student['courses_enrolled'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($student['risk_level'] === 'Low Risk') bg-green-100 text-green-800
                                    @elseif($student['risk_level'] === 'Medium Risk') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $student['risk_level'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($student['performance_rating'] === 'Excellent') bg-green-100 text-green-800
                                    @elseif($student['performance_rating'] === 'Good') bg-blue-100 text-blue-800
                                    @elseif($student['performance_rating'] === 'Average') bg-yellow-100 text-yellow-800
                                    @elseif($student['performance_rating'] === 'Poor') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $student['performance_rating'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($pagination && $pagination->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($pagination->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $pagination->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($pagination->hasMorePages())
                        <a href="{{ $pagination->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
                
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $pagination->firstItem() ?? 0 }}</span>
                            to
                            <span class="font-medium">{{ $pagination->lastItem() ?? 0 }}</span>
                            of
                            <span class="font-medium">{{ $pagination->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <!-- Previous Page Link -->
                            @if($pagination->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $pagination->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endif

                            <!-- Pagination Elements -->
                            @foreach($pagination->getUrlRange(1, $pagination->lastPage()) as $page => $url)
                                @if($page == $pagination->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 border border-green-500 bg-green-50 text-sm font-medium text-green-600">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <!-- Next Page Link -->
                            @if($pagination->hasMorePages())
                                <a href="{{ $pagination->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts - Chart.js expects configuration object with type, data, options
    const studentChartConfig = @json($studentChartData);
    const studentPerformanceChartConfig = @json($studentPerformanceChartData);
    const riskChartConfig = @json($riskChartData);
    
    let studentChart = null;
    let studentPerformanceChart = null;
    let riskChart = null;
    
    // Initialize charts
    const studentChartCanvas = document.getElementById('studentAttendanceChart');
    const studentPerformanceChartCanvas = document.getElementById('studentPerformanceChart');
    const riskChartCanvas = document.getElementById('riskAnalysisChart');
    
    if (studentChartCanvas && studentChartConfig) {
        studentChart = new Chart(studentChartCanvas, studentChartConfig);
    }
    if (studentPerformanceChartCanvas && studentPerformanceChartConfig) {
        studentPerformanceChart = new Chart(studentPerformanceChartCanvas, studentPerformanceChartConfig);
    }
    if (riskChartCanvas && riskChartConfig) {
        riskChart = new Chart(riskChartCanvas, riskChartConfig);
    }

    // Debounce function for filter changes
    let filterTimeout;
    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(filterTimeout);
                func(...args);
            };
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(later, wait);
        };
    }

    // Update course attendance chart based on time period filter
    function updateCourseAttendanceChart() {
        const timePeriod = document.getElementById('timePeriodFilter')?.value || 'all';
        const week = document.getElementById('weekFilter')?.value || '';
        const loadingDiv = document.getElementById('chartLoading');
        const chartContainer = document.getElementById('chartContainer');
        
        // Show/hide week filter based on time period
        const weekFilterContainer = document.getElementById('weekFilterContainer');
        if (weekFilterContainer) {
            if (timePeriod === 'live') {
                weekFilterContainer.style.display = 'none';
            } else {
                weekFilterContainer.style.display = 'flex';
            }
        }
        
        if (loadingDiv) loadingDiv.classList.remove('hidden');
        if (chartContainer) chartContainer.classList.add('hidden');
        
        // Get current filters
        const filterForm = document.getElementById('filterForm');
        const formData = new FormData(filterForm);
        formData.append('time_period', timePeriod);
        if (week) {
            formData.append('week', week);
        }
        
        fetch('{{ route("hod.monitoring.api.students.chart-data") }}?' + new URLSearchParams(formData))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.chartData && studentChart) {
                    // Destroy existing chart and create new one to ensure it's a bar chart
                    studentChart.destroy();
                    
                    // Create new bar chart
                    const ctx = document.getElementById('studentAttendanceChart').getContext('2d');
                    studentChart = new Chart(ctx, data.chartData);
                    
                    if (loadingDiv) loadingDiv.classList.add('hidden');
                    if (chartContainer) chartContainer.classList.remove('hidden');
                } else {
                    throw new Error('Failed to load chart data');
                }
            })
            .catch(error => {
                console.error('Error updating chart:', error);
                if (loadingDiv) loadingDiv.classList.add('hidden');
                if (chartContainer) chartContainer.classList.remove('hidden');
                alert('Failed to load chart data. Please try again.');
            });
    }

    // Update student performance chart based on view mode
    function updateStudentPerformanceChart() {
        const viewMode = document.getElementById('performanceViewMode')?.value || 'aggregated';
        const loadingDiv = document.getElementById('performanceChartLoading');
        const chartContainer = document.getElementById('performanceChartContainer');
        
        if (loadingDiv) loadingDiv.classList.remove('hidden');
        if (chartContainer) chartContainer.classList.add('hidden');
        
        // Get current filters
        const filterForm = document.getElementById('filterForm');
        const formData = new FormData(filterForm);
        formData.append('performance_view_mode', viewMode);
        
        fetch('{{ route("hod.monitoring.api.students.metrics") }}?' + new URLSearchParams(formData))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.chartData && studentPerformanceChart) {
                    // Update chart with new data
                    studentPerformanceChart.data = data.chartData.data;
                    studentPerformanceChart.options = data.chartData.options;
                    studentPerformanceChart.update('active');
                    
                    if (loadingDiv) loadingDiv.classList.add('hidden');
                    if (chartContainer) chartContainer.classList.remove('hidden');
                } else {
                    throw new Error('Failed to load chart data');
                }
            })
            .catch(error => {
                console.error('Error updating performance chart:', error);
                if (loadingDiv) loadingDiv.classList.add('hidden');
                if (chartContainer) chartContainer.classList.remove('hidden');
                alert('Failed to load chart data. Please try again.');
            });
    }

    // Add event listeners for filter changes
    const timePeriodFilter = document.getElementById('timePeriodFilter');
    if (timePeriodFilter) {
        timePeriodFilter.addEventListener('change', function() {
            // Reset week filter when switching to live view
            const weekFilter = document.getElementById('weekFilter');
            if (weekFilter && timePeriodFilter.value === 'live') {
                weekFilter.value = '';
            }
            updateCourseAttendanceChart();
        });
    }

    const weekFilter = document.getElementById('weekFilter');
    if (weekFilter) {
        weekFilter.addEventListener('change', debounce(updateCourseAttendanceChart, 500));
    }
    
    // Initially hide week filter if live view is selected
    if (timePeriodFilter && timePeriodFilter.value === 'live') {
        const weekFilterContainer = document.getElementById('weekFilterContainer');
        if (weekFilterContainer) {
            weekFilterContainer.style.display = 'none';
        }
    }

    const performanceViewMode = document.getElementById('performanceViewMode');
    if (performanceViewMode) {
        performanceViewMode.addEventListener('change', debounce(updateStudentPerformanceChart, 500));
    }

    // Search form handling
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
            
            // Add current time period, week, and view mode filters
            if (timePeriodFilter) {
                params.append('time_period', timePeriodFilter.value);
            }
            if (weekFilter) {
                params.append('week', weekFilter.value);
            }
            if (performanceViewMode) {
                params.append('performance_view_mode', performanceViewMode.value);
            }
            
        window.location.href = '{{ route("hod.monitoring.students") }}?' + params.toString();
    });
    }

    // Clear search
    const clearSearchBtn = document.getElementById('clearSearch');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            const searchInput = document.getElementById('search');
            if (searchInput) searchInput.value = '';
            window.location.href = '{{ route("hod.monitoring.students") }}';
        });
    }

    // Filter form handling
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
            const searchInput = document.getElementById('search');
            if (searchInput && searchInput.value) {
                formData.append('search', searchInput.value);
            }
            
            // Add time period, week, and view mode filters
            if (timePeriodFilter) {
                formData.append('time_period', timePeriodFilter.value);
            }
            if (weekFilter) {
                formData.append('week', weekFilter.value);
            }
            if (performanceViewMode) {
                formData.append('performance_view_mode', performanceViewMode.value);
            }
            
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route("hod.monitoring.students") }}?' + params.toString();
    });
    }

    // Clear filters
    const clearFiltersBtn = document.getElementById('clearFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (filterForm) filterForm.reset();
            const searchInput = document.getElementById('search');
            if (searchInput) searchInput.value = '';
            if (timePeriodFilter) timePeriodFilter.value = 'all';
            const weekFilter = document.getElementById('weekFilter');
            if (weekFilter) weekFilter.value = '';
            if (performanceViewMode) performanceViewMode.value = 'aggregated';
        window.location.href = '{{ route("hod.monitoring.students") }}';
    });
    }

    // Refresh data
    const refreshDataBtn = document.getElementById('refreshData');
    if (refreshDataBtn) {
        refreshDataBtn.addEventListener('click', function() {
        location.reload();
    });
    }

    // Export data
    const exportDataBtn = document.getElementById('exportData');
    if (exportDataBtn) {
        exportDataBtn.addEventListener('click', function() {
            // Get current filters for export
            const filterForm = document.getElementById('filterForm');
            const formData = new FormData(filterForm);
            if (timePeriodFilter) {
                formData.append('time_period', timePeriodFilter.value);
            }
            
            // Trigger export via route
            const params = new URLSearchParams(formData);
            window.open('{{ route("hod.monitoring.api.students.export") }}?' + params.toString(), '_blank');
        });
    }
});

// Fullscreen chart functionality (shared between pages)
let fullscreenChartInstance = null;

function openFullscreenChart(chartId, chartTitle) {
    const chartElement = document.getElementById(chartId);
    if (!chartElement) return;
    
    // Find the chart instance
    const chart = Chart.getChart(chartId);
    if (!chart) return;
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'fullscreenChartModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-labelledby', 'fullscreenChartTitle');
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-2xl w-full h-full max-w-7xl max-h-[95vh] flex flex-col">
            <div class="flex flex-col p-6 h-full">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="fullscreenChartTitle" class="text-2xl font-bold text-gray-900">${chartTitle}</h2>
                    <button onclick="closeFullscreenChart()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200" aria-label="Close fullscreen chart" id="closeFullscreenBtn">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close (ESC)
                    </button>
                </div>
                <div class="flex-1" role="img" aria-label="${chartTitle}" style="position: relative; height: calc(100% - 80px);">
                    <canvas id="fullscreenChartCanvas" style="max-height: 100%;"></canvas>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Clone the chart configuration and render in fullscreen
    setTimeout(() => {
        const canvas = document.getElementById('fullscreenChartCanvas');
        const config = JSON.parse(JSON.stringify(chart.config));
        
        // Update config for fullscreen display
        if (config.options) {
            config.options.responsive = true;
            // Keep maintainAspectRatio from original config
        }
        
        fullscreenChartInstance = new Chart(canvas, config);
        
        // Focus on close button for accessibility
        document.getElementById('closeFullscreenBtn').focus();
        
        // Add keyboard listener for ESC key
        document.addEventListener('keydown', handleEscapeKey);
    }, 100);
}

function closeFullscreenChart() {
    const modal = document.getElementById('fullscreenChartModal');
    if (modal) {
        if (fullscreenChartInstance) {
            fullscreenChartInstance.destroy();
            fullscreenChartInstance = null;
        }
        modal.remove();
        document.body.style.overflow = '';
        document.removeEventListener('keydown', handleEscapeKey);
    }
}

function handleEscapeKey(event) {
    if (event.key === 'Escape' || event.keyCode === 27) {
        closeFullscreenChart();
    }
}

// Sort table function
function sortTable(column) {
    const currentSort = '{{ $filters["sort_by"] ?? "" }}';
    const currentOrder = '{{ $filters["sort_order"] ?? "desc" }}';
    
    let newOrder = 'asc';
    if (currentSort === column && currentOrder === 'asc') {
        newOrder = 'desc';
    }
    
    const url = new URL(window.location);
    url.searchParams.set('sort_by', column);
    url.searchParams.set('sort_order', newOrder);
    window.location.href = url.toString();
}
</script>
@endsection






