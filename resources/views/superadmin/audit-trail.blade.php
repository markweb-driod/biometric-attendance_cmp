@extends('layouts.superadmin')

@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')
@section('page-description', 'Monitor system activities and user actions')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .activity-item {
        transition: all 0.2s ease;
    }
    .activity-item:hover {
        background-color: #f9fafb;
    }
    .severity-info { border-left-color: #3b82f6; }
    .severity-warning { border-left-color: #f59e0b; }
    .severity-error { border-left-color: #ef4444; }
    .severity-critical { border-left-color: #dc2626; }
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
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Activities -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Activities</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($stats['total_activities'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today Activities -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Today</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($stats['today_activities'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Week Activities -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">This Week</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($stats['week_activities'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Month Activities -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">This Month</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($stats['month_activities'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- User Logins -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">User Logins</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($stats['user_logins'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- System Changes -->
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">System Changes</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($stats['system_changes'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Activities -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Recent Activities
                    </h2>
                    <div class="flex space-x-2">
                        <button onclick="refreshActivities()" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                        <button onclick="exportActivities()" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
                
                <div class="space-y-3" id="recent-activities">
                    @if(isset($recentActivities) && count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                            <div class="activity-item border-l-4 rounded-lg p-4 bg-gray-50 severity-{{ $activity['severity'] ?? 'info' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                @if(($activity['severity'] ?? 'info') === 'warning') bg-yellow-100 text-yellow-800
                                                @elseif(($activity['severity'] ?? 'info') === 'error') bg-red-100 text-red-800
                                                @elseif(($activity['severity'] ?? 'info') === 'critical') bg-red-200 text-red-900
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ strtoupper($activity['severity'] ?? 'info') }}
                                            </span>
                                            <span class="px-2 py-1 text-xs font-medium rounded bg-gray-200 text-gray-700">
                                                {{ ucfirst(str_replace('_', ' ', $activity['type'] ?? 'unknown')) }}
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $activity['action'] ?? 'Unknown Action' }}</h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ $activity['description'] ?? 'No description' }}</p>
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                {{ $activity['user'] ?? 'Unknown User' }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $activity['timestamp'] ?? 'Unknown time' }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                                {{ $activity['ip_address'] ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm">No recent activities found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Activities Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    User Activities
                </h2>
                
                <div class="space-y-4" id="user-activities">
                    @if(isset($userActivities) && count($userActivities) > 0)
                        @foreach($userActivities as $user)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $user['user'] ?? 'Unknown User' }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if(($user['status'] ?? 'inactive') === 'active') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user['status'] ?? 'inactive') }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <p class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ number_format($user['activities_count'] ?? 0) }} activities
                                    </p>
                                    <p class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Last: {{ $user['last_activity'] ?? 'Never' }}
                                    </p>
                                    <p class="flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        IP: {{ $user['ip_address'] ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <p class="text-sm">No user activities found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshActivities() {
    location.reload();
}

function exportActivities() {
    // Export functionality
    window.location.href = '/superadmin/audit-trail/export';
}
</script>
@endsection


                            </svg>

                            Refresh

                        </button>

                        <button onclick="exportActivities()" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">

                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>

                            </svg>

                            Export

                        </button>

                    </div>

                </div>

                

                <div class="space-y-3" id="recent-activities">

                    @if(isset($recentActivities) && count($recentActivities) > 0)

                        @foreach($recentActivities as $activity)

                            <div class="activity-item border-l-4 rounded-lg p-4 bg-gray-50 severity-{{ $activity['severity'] ?? 'info' }}">

                                <div class="flex items-start justify-between">

                                    <div class="flex-1 min-w-0">

                                        <div class="flex items-center space-x-2 mb-2">

                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 

                                                @if(($activity['severity'] ?? 'info') === 'warning') bg-yellow-100 text-yellow-800

                                                @elseif(($activity['severity'] ?? 'info') === 'error') bg-red-100 text-red-800

                                                @elseif(($activity['severity'] ?? 'info') === 'critical') bg-red-200 text-red-900

                                                @else bg-blue-100 text-blue-800

                                                @endif">

                                                {{ strtoupper($activity['severity'] ?? 'info') }}

                                            </span>

                                            <span class="px-2 py-1 text-xs font-medium rounded bg-gray-200 text-gray-700">

                                                {{ ucfirst(str_replace('_', ' ', $activity['type'] ?? 'unknown')) }}

                                            </span>

                                        </div>

                                        <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ $activity['action'] ?? 'Unknown Action' }}</h3>

                                        <p class="text-sm text-gray-600 mb-2">{{ $activity['description'] ?? 'No description' }}</p>

                                        <div class="flex items-center space-x-4 text-xs text-gray-500">

                                            <span class="flex items-center">

                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>

                                                </svg>

                                                {{ $activity['user'] ?? 'Unknown User' }}

                                            </span>

                                            <span class="flex items-center">

                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>

                                                </svg>

                                                {{ $activity['timestamp'] ?? 'Unknown time' }}

                                            </span>

                                            <span class="flex items-center">

                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>

                                                </svg>

                                                {{ $activity['ip_address'] ?? 'N/A' }}

                                            </span>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <div class="text-center py-12 text-gray-500">

                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>

                            </svg>

                            <p class="text-sm">No recent activities found</p>

                        </div>

                    @endif

                </div>

            </div>

        </div>



        <!-- User Activities Sidebar -->

        <div class="lg:col-span-1">

            <div class="bg-white rounded-xl shadow-lg p-6">

                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">

                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>

                    </svg>

                    User Activities

                </h2>

                

                <div class="space-y-4" id="user-activities">

                    @if(isset($userActivities) && count($userActivities) > 0)

                        @foreach($userActivities as $user)

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">

                                <div class="flex items-center justify-between mb-2">

                                    <h3 class="text-sm font-semibold text-gray-900">{{ $user['user'] ?? 'Unknown User' }}</h3>

                                    <span class="px-2 py-1 text-xs rounded-full 

                                        @if(($user['status'] ?? 'inactive') === 'active') bg-green-100 text-green-800

                                        @else bg-gray-100 text-gray-800

                                        @endif">

                                        {{ ucfirst($user['status'] ?? 'inactive') }}

                                    </span>

                                </div>

                                <div class="text-xs text-gray-600 space-y-1">

                                    <p class="flex items-center">

                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>

                                        </svg>

                                        {{ number_format($user['activities_count'] ?? 0) }} activities

                                    </p>

                                    <p class="flex items-center">

                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>

                                        </svg>

                                        Last: {{ $user['last_activity'] ?? 'Never' }}

                                    </p>

                                    <p class="flex items-center">

                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>

                                        </svg>

                                        IP: {{ $user['ip_address'] ?? 'N/A' }}

                                    </p>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <div class="text-center py-8 text-gray-500">

                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>

                            </svg>

                            <p class="text-sm">No user activities found</p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>



<script>

function refreshActivities() {

    location.reload();

}



function exportActivities() {

    // Export functionality

    window.location.href = '/superadmin/audit-trail/export';

}

</script>

@endsection




