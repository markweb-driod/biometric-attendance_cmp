@extends('layouts.superadmin')

@section('title', 'Session Monitoring')
@section('page-title', 'Session Monitoring')
@section('page-description', 'Monitor all active sessions and view session history')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .session-item {
        transition: all 0.2s ease;
    }
    .session-item:hover {
        background-color: #f9fafb;
    }
    .status-badge-active { background-color: #10b981; color: white; }
    .status-badge-ended { background-color: #6b7280; color: white; }
    .status-badge-expired { background-color: #f59e0b; color: white; }
    .status-badge-terminated { background-color: #ef4444; color: white; }
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
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <!-- Active Sessions -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Active Sessions</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900" id="stat-active">{{ $stats['active_sessions'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Admin Sessions -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Admins</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900" id="stat-superadmin">{{ $stats['active_superadmin'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Lecturer Sessions -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Lecturers</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900" id="stat-lecturers">{{ $stats['active_lecturers'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- HOD Sessions -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">HODs</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900" id="stat-hods">{{ $stats['active_hods'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total This Week -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">This Week</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['recent_stats']['total_sessions'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-lg mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('live')" id="tab-live" class="tab-button active flex-1 py-4 px-6 text-center font-medium text-green-600 border-b-2 border-green-600">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Live Sessions
                </button>
                <button onclick="switchTab('history')" id="tab-history" class="tab-button flex-1 py-4 px-6 text-center font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Session History
                </button>
            </nav>
        </div>

        <!-- Live Sessions Tab -->
        <div id="content-live" class="tab-content p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Active Sessions</h3>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-2">
                        <input type="text" id="live-search" placeholder="Search..." class="px-3 py-2 border rounded-lg text-sm">
                        <select id="live-user-type" class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Types</option>
                            <option value="Superadmin">Superadmin</option>
                            <option value="Lecturer">Lecturer</option>
                            <option value="Hod">HOD</option>
                        </select>
                    </div>
                    <button onclick="loadLiveSessions()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="live-sessions-body" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Session History Tab -->
        <div id="content-history" class="tab-content hidden p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Session History</h3>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-2">
                        <input type="date" id="history-date-from" class="px-3 py-2 border rounded-lg text-sm">
                        <input type="date" id="history-date-to" class="px-3 py-2 border rounded-lg text-sm">
                        <select id="history-status" class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="ended">Ended</option>
                            <option value="expired">Expired</option>
                            <option value="terminated">Terminated</option>
                        </select>
                        <select id="history-user-type" class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Types</option>
                            <option value="Superadmin">Superadmin</option>
                            <option value="Lecturer">Lecturer</option>
                            <option value="Hod">HOD</option>
                        </select>
                    </div>
                    <button onclick="loadSessionHistory()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="history-sessions-body" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Session Details Modal -->
<div id="session-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Session Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modal-content">
                    <p class="text-gray-500">Loading session details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let liveRefreshInterval;
let currentTab = 'live';

// Tab switching
function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.remove('text-green-600', 'border-green-600');
        btn.classList.add('text-gray-500', 'border-transparent');
    });
    
    document.getElementById('tab-' + tab).classList.add('active', 'text-green-600', 'border-green-600');
    document.getElementById('tab-' + tab).classList.remove('text-gray-500', 'border-transparent');
    
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    document.getElementById('content-' + tab).classList.remove('hidden');
    
    if (tab === 'live') {
        loadLiveSessions();
        startLiveRefresh();
    } else {
        stopLiveRefresh();
        loadSessionHistory();
    }
}

// Auto-refresh for live sessions
function startLiveRefresh() {
    if (liveRefreshInterval) clearInterval(liveRefreshInterval);
    liveRefreshInterval = setInterval(loadLiveSessions, 30000); // 30 seconds
}

function stopLiveRefresh() {
    if (liveRefreshInterval) clearInterval(liveRefreshInterval);
}

// Load live sessions
async function loadLiveSessions() {
    try {
        const userType = document.getElementById('live-user-type').value;
        const search = document.getElementById('live-search').value;
        
        let url = '{{ route("superadmin.session-monitoring.live") }}?page=1';
        if (userType) url += '&user_type=' + encodeURIComponent(userType);
        if (search) url += '&search=' + encodeURIComponent(search);
        
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        const tbody = document.getElementById('live-sessions-body');
        if (data.sessions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No active sessions</td></tr>';
        } else {
            tbody.innerHTML = data.sessions.map(session => `
                <tr class="session-item">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="font-medium">${escapeHtml(session.user_name)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(session.user_type)} - ${escapeHtml(session.identifier)}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${escapeHtml(session.login_at)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${escapeHtml(session.duration)}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm">${escapeHtml(session.device)}</div>
                        <div class="text-xs text-gray-500">${escapeHtml(session.browser)}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${escapeHtml(session.location)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium space-x-2">
                        <button onclick="viewSession(${session.id})" class="text-blue-600 hover:text-blue-900">View</button>
                        <button onclick="terminateSession(${session.id})" class="text-red-600 hover:text-red-900">Terminate</button>
                    </td>
                </tr>
            `).join('');
        }
        
        // Update stats
        document.getElementById('stat-active').textContent = data.pagination.total;
        
    } catch (error) {
        console.error('Error loading live sessions:', error);
        document.getElementById('live-sessions-body').innerHTML = 
            '<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Error loading sessions</td></tr>';
    }
}

// Load session history
async function loadSessionHistory(page = 1) {
    try {
        const userType = document.getElementById('history-user-type').value;
        const status = document.getElementById('history-status').value;
        const dateFrom = document.getElementById('history-date-from').value;
        const dateTo = document.getElementById('history-date-to').value;
        
        let url = '{{ route("superadmin.session-monitoring.history") }}?page=' + page;
        if (userType) url += '&user_type=' + encodeURIComponent(userType);
        if (status) url += '&status=' + encodeURIComponent(status);
        if (dateFrom) url += '&date_from=' + encodeURIComponent(dateFrom);
        if (dateTo) url += '&date_to=' + encodeURIComponent(dateTo);
        
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        const tbody = document.getElementById('history-sessions-body');
        if (data.sessions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No session history</td></tr>';
        } else {
            tbody.innerHTML = data.sessions.map(session => `
                <tr class="session-item">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="font-medium">${escapeHtml(session.user_name)}</div>
                        <div class="text-sm text-gray-500">${escapeHtml(session.user_type)} - ${escapeHtml(session.identifier)}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${escapeHtml(session.login_at)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${escapeHtml(session.duration)}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded status-badge-${session.status}">
                            ${escapeHtml(session.status.toUpperCase())}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm">${escapeHtml(session.device)}</div>
                        <div class="text-xs text-gray-500">${escapeHtml(session.browser)}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewSession(${session.id})" class="text-blue-600 hover:text-blue-900">View</button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading session history:', error);
        document.getElementById('history-sessions-body').innerHTML = 
            '<tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Error loading sessions</td></tr>';
    }
}

// View session details
async function viewSession(id) {
    try {
        const response = await fetch(`/superadmin/session-monitoring/session/${id}/details`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        const session = data.session;
        
        document.getElementById('modal-content').innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div><strong>User Type:</strong> ${escapeHtml(session.user_type)}</div>
                <div><strong>Full Name:</strong> ${escapeHtml(session.user_name)}</div>
                <div><strong>Identifier:</strong> ${escapeHtml(session.identifier)}</div>
                <div><strong>Department:</strong> ${escapeHtml(session.department || 'N/A')}</div>
                <div><strong>Login At:</strong> ${escapeHtml(session.login_at)}</div>
                <div><strong>Last Activity:</strong> ${escapeHtml(session.last_activity_at || 'N/A')}</div>
                <div><strong>Status:</strong> <span class="px-2 py-1 text-xs font-semibold rounded status-badge-${session.status}">${escapeHtml(session.status.toUpperCase())}</span></div>
                <div><strong>Duration:</strong> ${escapeHtml(session.duration)}</div>
                <div><strong>IP Address:</strong> ${escapeHtml(session.ip_address)}</div>
                <div><strong>Device:</strong> ${escapeHtml(session.device_type)}</div>
                <div><strong>OS:</strong> ${escapeHtml(session.os)}</div>
                <div><strong>Browser:</strong> ${escapeHtml(session.browser)}</div>
                <div><strong>Location:</strong> ${escapeHtml(session.country ? (session.city ? session.city + ', ' : '') + session.country : 'Unknown')}</div>
                ${session.user_agent ? `<div class="col-span-2"><strong>User Agent:</strong> <code class="text-xs bg-gray-100 p-2 rounded block mt-1">${escapeHtml(session.user_agent)}</code></div>` : ''}
            </div>
            ${session.activity_trail && session.activity_trail.length > 0 ? `
                <div class="mt-6">
                    <h4 class="font-semibold mb-3">Activity Trail</h4>
                    <div class="space-y-2">
                        ${session.activity_trail.map(activity => `
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="flex justify-between">
                                    <span class="font-medium">${escapeHtml(activity.action)}</span>
                                    <span class="text-sm text-gray-500">${escapeHtml(activity.timestamp)}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
        `;
        document.getElementById('session-modal').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading session details:', error);
        alert('Error loading session details');
    }
}

// Terminate session
async function terminateSession(id) {
    if (!confirm('Are you sure you want to terminate this session?')) return;
    
    try {
        const response = await fetch(`/superadmin/session-monitoring/session/${id}/terminate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: 'Terminated by administrator' })
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Session terminated successfully');
            loadLiveSessions();
        } else {
            alert('Error terminating session');
        }
    } catch (error) {
        console.error('Error terminating session:', error);
        alert('Error terminating session');
    }
}

// Close modal
function closeModal() {
    document.getElementById('session-modal').classList.add('hidden');
}

// Utility function
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadLiveSessions();
    startLiveRefresh();
});
</script>
@endpush
@endsection

