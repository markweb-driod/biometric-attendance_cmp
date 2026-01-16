@extends('layouts.superadmin')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('page-description', 'View and export attendance analytics for all classes and levels')

@push('styles')
<style>
    .kpi-card {
        min-height: 120px;
    }
    
    .kpi-top-class {
        font-size: 0.875rem;
        line-height: 1.25rem;
        max-width: 100%;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    
    @media (max-width: 640px) {
        .kpi-top-class {
            font-size: 0.75rem;
            line-height: 1rem;
        }
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .loading-spinner {
        border: 3px solid #f3f4f6;
        border-top: 3px solid #10b981;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-gradient-to-r from-green-500 to-green-600 rounded-lg px-6 py-4 shadow-lg mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Reports & Analytics</h2>
            <p class="text-white text-sm opacity-90">View, filter, and export attendance analytics for all classes and levels</p>
        </div>
        <div class="flex gap-2 mt-3 md:mt-0">
            <button onclick="exportToCsv()" id="export-btn" class="px-4 py-2 bg-white text-green-600 border border-white rounded-lg font-medium hover:bg-green-50 hover:text-green-700 transition shadow flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </button>
            <button onclick="refreshReports()" id="refresh-btn" class="px-4 py-2 bg-green-700 text-white rounded-lg font-medium hover:bg-green-800 transition shadow flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-4 flex flex-col items-center kpi-card border border-green-200">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-2 shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="text-xs text-gray-600 font-medium mb-1">Attendance Rate</div>
            <div class="text-3xl font-extrabold text-green-900" id="kpi-attendance-rate">-</div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-4 flex flex-col items-center kpi-card border border-blue-200">
            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2 shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="text-xs text-gray-600 font-medium mb-1">Total Sessions</div>
            <div class="text-3xl font-extrabold text-blue-900" id="kpi-total-sessions">-</div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-4 flex flex-col items-center kpi-card border border-red-200">
            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mb-2 shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="text-xs text-gray-600 font-medium mb-1">Absentees</div>
            <div class="text-3xl font-extrabold text-red-900" id="kpi-absentees">-</div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-4 flex flex-col items-center kpi-card border border-purple-200">
            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mb-2 shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <div class="text-xs text-gray-600 font-medium mb-1">Top Class</div>
            <div class="kpi-top-class font-semibold text-purple-900 text-center px-1" id="kpi-top-class" title="">-</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form id="filter-form" class="bg-white rounded-xl shadow-lg flex flex-col md:flex-row gap-3 items-end px-4 py-4 mb-4">
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
            <input type="date" id="date_from" name="date_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
            <input type="date" id="date_to" name="date_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
            <select id="department_id" name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Academic Level</label>
            <select id="academic_level_id" name="academic_level_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                <option value="">All Levels</option>
                @foreach($academicLevels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Classroom</label>
            <select id="classroom_id" name="classroom_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                <option value="">All Classes</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}">{{ $classroom->class_name }} ({{ $classroom->course->course_code ?? 'N/A' }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                <option value="">All Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="denied">Denied</option>
            </select>
        </div>
        <div class="flex-1 min-w-0">
            <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
            <input type="text" id="search" name="search" placeholder="Search students..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        </div>
        <div>
            <button type="button" onclick="applyFilters()" class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow">Apply Filters</button>
        </div>
        <div>
            <button type="button" onclick="resetFilters()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">Reset</button>
        </div>
    </form>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-lg p-4 mb-4">
        <div id="table-loading" class="text-center py-8 hidden">
            <div class="loading-spinner mx-auto mb-3"></div>
            <p class="text-gray-500">Loading reports data...</p>
        </div>
        <div id="table-container" class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Matric Number</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Class</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Course</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Level</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Department</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Date & Time</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Method</th>
                    </tr>
                </thead>
                <tbody id="reports-table-body" class="bg-white divide-y divide-gray-100">
                    <tr>
                        <td colspan="9" class="text-center text-gray-400 py-8">Loading data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="pagination-container" class="mt-4 flex justify-center items-center gap-2"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
let currentPage = 1;
let isLoading = false;

function updateKPIs(kpis) {
    if (!kpis) return;
    
    document.getElementById('kpi-attendance-rate').textContent = (kpis.attendance_rate ?? 0).toFixed(1) + '%';
    document.getElementById('kpi-total-sessions').textContent = kpis.total_sessions ?? 0;
    document.getElementById('kpi-absentees').textContent = kpis.absentees ?? 0;
    
    const topClassElement = document.getElementById('kpi-top-class');
    const topClassName = kpis.top_class ?? 'N/A';
    topClassElement.textContent = topClassName;
    topClassElement.title = topClassName;
}

function getFilters() {
    return {
        date_from: document.getElementById('date_from').value || '',
        date_to: document.getElementById('date_to').value || '',
        department_id: document.getElementById('department_id').value || '',
        academic_level_id: document.getElementById('academic_level_id').value || '',
        classroom_id: document.getElementById('classroom_id').value || '',
        status: document.getElementById('status').value || '',
        search: document.getElementById('search').value || '',
        page: currentPage,
    };
}

function fetchReportsData() {
    if (isLoading) return;
    isLoading = true;
    
    const tableLoading = document.getElementById('table-loading');
    const tableContainer = document.getElementById('table-container');
    const tableBody = document.getElementById('reports-table-body');
    
    tableLoading.classList.remove('hidden');
    tableContainer.classList.add('hidden');
    
    const filters = getFilters();
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key]) params.append(key, filters[key]);
    });
    
    axios.get(`/superadmin/reports/data?${params}`)
        .then(response => {
            if (response.data && response.data.success) {
                // Update KPIs
                if (response.data.kpis) {
                    updateKPIs(response.data.kpis);
                }
                
                // Update table data
                if (response.data.table_data) {
                    updateTableData(response.data.table_data);
                }
                
                // Update pagination
                if (response.data.pagination) {
                    updatePagination(response.data.pagination);
                }
            }
            isLoading = false;
            tableLoading.classList.add('hidden');
            tableContainer.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading reports:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-8">
                        <div class="text-red-500 mb-2">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-red-600 font-medium">Failed to load reports</p>
                        <p class="text-sm text-gray-500 mt-1">${error.response?.data?.message || error.message || 'An error occurred'}</p>
                    </td>
                </tr>
            `;
            isLoading = false;
            tableLoading.classList.add('hidden');
            tableContainer.classList.remove('hidden');
        });
}

function updateTableData(tableData) {
    const tableBody = document.getElementById('reports-table-body');
    if (!tableBody || !tableData || tableData.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-gray-400 py-8">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">No attendance records found</p>
                    <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = tableData.map(row => {
        const statusBadge = row.status === 'present' 
            ? '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 font-medium">Present</span>'
            : row.status === 'late'
            ? '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 font-medium">Late</span>'
            : row.status === 'denied'
            ? '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 font-medium">Denied</span>'
            : '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 font-medium">Absent</span>';
        
        return `
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="font-medium text-gray-900">${row.student_name}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-gray-700 font-mono text-xs">${row.matric_number}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-gray-900">${row.class_name}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm text-gray-700">${row.course_code}</div>
                    <div class="text-xs text-gray-500">${row.course_name}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-gray-700">${row.academic_level}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-gray-700">${row.department}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm text-gray-700">${row.captured_at}</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    ${statusBadge}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-1 rounded text-xs ${row.method === 'Biometric' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}">${row.method}</span>
                </td>
            </tr>
        `;
    }).join('');
}

function updatePagination(pagination) {
    const paginationContainer = document.getElementById('pagination-container');
    if (!pagination || pagination.total === 0) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHTML = '<div class="flex items-center gap-2">';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `
            <button onclick="goToPage(${pagination.current_page - 1})" 
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Previous
            </button>
        `;
    }
    
    // Page info
    paginationHTML += `
        <span class="px-3 py-2 text-sm text-gray-700">
            Page ${pagination.current_page} of ${pagination.last_page} 
            (${pagination.total} total records)
        </span>
    `;
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHTML += `
            <button onclick="goToPage(${pagination.current_page + 1})" 
                    class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Next
            </button>
        `;
    }
    
    paginationHTML += '</div>';
    paginationContainer.innerHTML = paginationHTML;
}

function goToPage(page) {
    currentPage = page;
    fetchReportsData();
}

function applyFilters() {
    currentPage = 1;
    fetchReportsData();
}

function resetFilters() {
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    document.getElementById('department_id').value = '';
    document.getElementById('academic_level_id').value = '';
    document.getElementById('classroom_id').value = '';
    document.getElementById('status').value = '';
    document.getElementById('search').value = '';
    currentPage = 1;
    fetchReportsData();
}

function refreshReports() {
    const refreshBtn = document.getElementById('refresh-btn');
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = `
        <div class="loading-spinner" style="width: 16px; height: 16px; border-width: 2px;"></div>
        Refreshing...
    `;
    
    fetchReportsData();
    
    setTimeout(() => {
        refreshBtn.disabled = false;
        refreshBtn.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh
        `;
    }, 1000);
}

function exportToCsv() {
    const exportBtn = document.getElementById('export-btn');
    exportBtn.disabled = true;
    exportBtn.innerHTML = `
        <div class="loading-spinner" style="width: 16px; height: 16px; border-width: 2px;"></div>
        Exporting...
    `;
    
    const filters = getFilters();
    const params = new URLSearchParams();
    Object.keys(filters).forEach(key => {
        if (filters[key] && key !== 'page') params.append(key, filters[key]);
    });
    
    window.location.href = `/superadmin/reports/export?${params}`;
    
    setTimeout(() => {
        exportBtn.disabled = false;
        exportBtn.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export CSV
        `;
    }, 2000);
}

// Set default date range (last 30 days)
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    document.getElementById('date_from').value = thirtyDaysAgo.toISOString().split('T')[0];
    document.getElementById('date_to').value = today.toISOString().split('T')[0];
    
    // Initial load
    fetchReportsData();
    
    // Add Enter key listener for search
    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
});
</script>
@endsection

    }
});
</script>
@endsection

