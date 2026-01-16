@extends('hod.layouts.app')

@section('title', 'Course & Staff Monitoring')

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
                    <h1 class="text-2xl sm:text-3xl font-bold text-green-800 break-words" style="font-family: 'Montserrat', sans-serif;">Course & Staff Monitoring</h1>
                    <p class="mt-1 text-xs sm:text-sm text-green-600 font-medium">Monitor lecturer performance and course attendance trends</p>
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

    <!-- Filters -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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
                    <label for="academic_year" class="block text-sm font-semibold text-gray-700">Academic Year</label>
                    <select id="academic_year" name="academic_year" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Years</option>
                        @foreach($filterOptions['academic_years'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['academic_year'] == $value ? 'selected' : '' }}>{{ $label }}</option>
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

        <!-- Performance Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Total Courses</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ $performanceAnalysis['total_courses'] ?? 0 }}</dd>
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
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Average Performance</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ number_format($performanceAnalysis['average_performance'] ?? 0, 1) }}%</dd>
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
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Top Performers</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ count($performanceAnalysis['top_performers'] ?? []) }}</dd>
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
                                <dt class="text-sm font-semibold text-gray-600 truncate" style="font-family: 'Montserrat', sans-serif;">Low Performers</dt>
                                <dd class="text-lg font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ count($performanceAnalysis['low_performers'] ?? []) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chart -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6 border-l-4 border-green-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-green-800" style="font-family: 'Montserrat', sans-serif;">Course Performance Trends</h3>
                <div class="flex space-x-2">
                    <button id="toggleChartType" class="inline-flex items-center px-3 py-1 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="Switch chart view between line and bar">
                        Switch View
                    </button>
                    <a href="{{ route('hod.monitoring.courses.chart', 'performance') }}?{{ http_build_query($filters) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="View chart in fullscreen">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Maximize
                    </a>
                </div>
            </div>
            <div class="h-96" role="img" aria-label="Course Performance Trends Chart">
                <canvas id="coursePerformanceChart"></canvas>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Lecturer Performance Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Lecturer Performance</h3>
                    <a href="{{ route('hod.monitoring.courses.chart', 'lecturer') }}?{{ http_build_query($filters) }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-green-300 rounded-lg text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="View lecturer performance chart in fullscreen">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </a>
                </div>
                <div class="h-80" role="img" aria-label="Lecturer Performance Chart">
                    <canvas id="lecturerPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Performance Distribution Chart -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Performance Distribution</h3>
                    <a href="{{ route('hod.monitoring.courses.chart', 'distribution') }}?{{ http_build_query($filters) }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-green-300 rounded-lg text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200" aria-label="View performance distribution chart in fullscreen">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </a>
                </div>
                <div class="h-80" role="img" Nobody="Performance Distribution Chart">
                    <canvas id="performanceDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Course Performance Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Course Performance Details</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lecturer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Punctuality</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($coursePerformance as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $course['course_code'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $course['course_name'] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $course['lecturer_name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $course['staff_id'] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $course['average_attendance_rate'] }}%</div>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $course['average_attendance_rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $course['conducted_sessions'] }}/{{ $course['total_sessions'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $course['punctuality_score'] }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($course['performance_rating'] === 'Excellent') bg-green-100 text-green-800
                                    @elseif($course['performance_rating'] === 'Good') bg-blue-100 text-blue-800
                                    @elseif($course['performance_rating'] === 'Average') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $course['performance_rating'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts with error handling
    let courseChart, lecturerChart, distributionChart;
    
    try {
        // Parse chart data
        const courseChartData = {!! json_encode($courseChartData) !!};
        const lecturerChartData = {!! json_encode($lecturerChartData) !!};
        const distributionChartData = {!! json_encode($distributionChartData) !!};
        
        // Initialize charts only if canvas elements exist
        const courseCanvas = document.getElementById('coursePerformanceChart');
        const lecturerCanvas = document.getElementById('lecturerPerformanceChart');
        const distributionCanvas = document.getElementById('performanceDistributionChart');
        
        if (courseCanvas) {
            courseChart = new Chart(courseCanvas, courseChartData);
        }
        
        if (lecturerCanvas) {
            lecturerChart = new Chart(lecturerCanvas, lecturerChartData);
        }
        
        if (distributionCanvas) {
            distributionChart = new Chart(distributionCanvas, distributionChartData);
        }
    } catch (error) {
        console.error('Error initializing charts:', error);
        alert('There was an error loading the charts. Please refresh the page.');
    }

    // Filter form handling
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.href = '{{ route("hod.monitoring.courses") }}?' + params.toString();
    });

    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('filterForm').reset();
        window.location.href = '{{ route("hod.monitoring.courses") }}';
    });

    // Refresh data
    document.getElementById('refreshData').addEventListener('click', function() {
        location.reload();
    });

    // Export data
    document.getElementById('exportData').addEventListener('click', function() {
        // Implement export functionality
        alert('Export functionality will be implemented');
    });

    // Toggle chart type (line/bar)
    const toggleChartBtn = document.getElementById('toggleChartType');
    if (toggleChartBtn && courseChart) {
        toggleChartBtn.addEventListener('click', function() {
            const currentType = courseChart.config.type;
            const newType = currentType === 'line' ? 'bar' : 'line';
            
            // Update chart type
            courseChart.config.type = newType;
            courseChart.update();
            
            // Update button text
            this.textContent = newType === 'line' ? 'Switch to Bar View' : 'Switch to Line View';
        });
    }
});

// Fullscreen chart functionality
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
</script>

@endsection






