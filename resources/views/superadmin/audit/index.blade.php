@extends('layouts.superadmin')

@section('title', 'System Audit Logs & Monitoring')
@section('page-title', 'System Audit Logs')
@section('page-description', 'Monitor all system activities with advanced analytics and tracking')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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

<div x-data="systemAuditApp()" class="container mx-auto p-4 md:p-6 space-y-6">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-green-600 to-green-500 rounded-3xl shadow-2xl mb-6">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full -translate-y-48 translate-x-48"></div>
        
        <div class="relative px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-white min-w-0 flex-1">
                    <h1 class="text-3xl sm:text-4xl font-bold mb-2">System Audit Logs & Monitoring</h1>
                    <p class="text-lg sm:text-xl text-green-100">Monitor all system-wide activities across departments, users, and resources</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-shrink-0">
                    <button @click="exportData()" class="px-4 sm:px-6 py-2 sm:py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-xl hover:bg-opacity-30 transition-all duration-300 shadow-lg text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Export Data</span>
                    </button>
                    <button @click="refreshData()" class="px-4 sm:px-6 py-2 sm:py-3 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-xl hover:bg-opacity-30 transition-all duration-300 shadow-lg text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Events</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900" x-text="stats.total || 0"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Critical</p>
                    <p class="text-2xl sm:text-3xl font-bold text-red-600" x-text="stats.critical || 0"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">High</p>
                    <p class="text-2xl sm:text-3xl font-bold text-orange-600" x-text="stats.high || 0"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Medium</p>
                    <p class="text-2xl sm:text-3xl font-bold text-yellow-600" x-text="stats.medium || 0"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Low</p>
                    <p class="text-2xl sm:text-3xl font-bold text-green-600" x-text="stats.low || 0"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Departments</p>
                    <p class="text-2xl sm:text-3xl font-bold text-purple-600" x-text="departmentActivitySummary.length || 0"></p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-indigo-500">
        <h3 class="text-lg font-semibold text-indigo-800 mb-4">Advanced Filters</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select x-model="filters.action" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Actions</option>
                    <template x-for="action in filterOptions.actions" :key="action">
                        <option :value="action" x-text="action.charAt(0).toUpperCase() + action.slice(1)"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Resource Type</label>
                <select x-model="filters.resource_type" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Resources</option>
                    <template x-for="type in filterOptions.resource_types" :key="type">
                        <option :value="type" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User Type</label>
                <select x-model="filters.user_type" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Users</option>
                    <template x-for="type in filterOptions.user_types" :key="type">
                        <option :value="type" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select x-model="filters.department_id" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    <template x-for="dept in filterOptions.departments" :key="dept.id">
                        <option :value="dept.id" x-text="dept.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                <select x-model="filters.severity" @change="applyFilters()" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Severities</option>
                    <template x-for="sev in filterOptions.severities" :key="sev">
                        <option :value="sev" x-text="sev.charAt(0).toUpperCase() + sev.slice(1)"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" x-model="filters.date_from" @change="applyFilters()" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" x-model="filters.date_to" @change="applyFilters()" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()" 
                       placeholder="Search descriptions, IP, etc..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button @click="clearFilters()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Activity Chart -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daily Activity (Last 30 Days)</h3>
            <div class="chart-container">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>

        <!-- Activity by User Type -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity by User Type</h3>
            <div class="chart-container">
                <canvas id="userTypeChart"></canvas>
            </div>
        </div>

        <!-- Activity by Action -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity by Action</h3>
            <div class="chart-container">
                <canvas id="actionChart"></canvas>
            </div>
        </div>

        <!-- Activity by Severity -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Activity by Severity</h3>
            <div class="chart-container">
                <canvas id="severityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Activity Summary -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6" x-show="departmentActivitySummary.length > 0">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Activity Summary</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Activities</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Critical</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">High</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Percentage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="dept in departmentActivitySummary" :key="dept.department_id">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="dept.department ? dept.department.name : 'Unknown'"></td>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="dept.activity_count"></td>
                            <td class="px-4 py-3 text-sm text-red-600">-</td>
                            <td class="px-4 py-3 text-sm text-orange-600">-</td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" :style="'width: ' + (dept.activity_count / stats.total * 100) + '%'"></div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Security Alerts -->
    <div x-show="securityAlerts.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-4 sm:p-6 mb-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-semibold text-red-800">Security Alerts</h3>
                <p class="text-sm text-red-700 mt-1">You have <strong x-text="securityAlerts.length"></strong> high-priority security events requiring attention.</p>
                <button @click="showSecurityAlerts = !showSecurityAlerts" class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                    <span x-text="showSecurityAlerts ? 'Hide' : 'View'"></span> Details
                </button>
                
                <div x-show="showSecurityAlerts" class="mt-4 space-y-2 max-h-96 overflow-y-auto">
                    <template x-for="alert in securityAlerts.slice(0, 10)" :key="alert.id">
                        <div class="bg-white rounded-lg p-3 border border-red-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900" x-text="alert.description"></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span x-text="alert.user_type"></span> - 
                                        <span x-text="new Date(alert.created_at).toLocaleString()"></span>
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800" x-text="alert.severity"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">System Audit Logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date/Time</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Resource</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Department</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Severity</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">IP Address</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="log in auditLogs" :key="log.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900" x-text="new Date(log.created_at).toLocaleString()"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" x-text="log.action ? log.action.charAt(0).toUpperCase() + log.action.slice(1) : 'N/A'"></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                <div x-text="log.resource_type ? log.resource_type.charAt(0).toUpperCase() + log.resource_type.slice(1) : 'N/A'"></div>
                                <div class="text-xs text-gray-500" x-text="log.resource_id ? 'ID: ' + log.resource_id : ''"></div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                <div x-text="log.user_type ? log.user_type.charAt(0).toUpperCase() + log.user_type.slice(1) : 'N/A'"></div>
                                <div class="text-xs text-gray-500" x-text="log.user_id ? 'ID: ' + log.user_id : ''"></div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900" x-text="log.department ? log.department.name : 'N/A'"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-red-100 text-red-800': log.severity === 'critical',
                                          'bg-orange-100 text-orange-800': log.severity === 'high',
                                          'bg-yellow-100 text-yellow-800': log.severity === 'medium',
                                          'bg-green-100 text-green-800': log.severity === 'low'
                                      }"
                                      x-text="log.severity ? log.severity.charAt(0).toUpperCase() + log.severity.slice(1) : 'N/A'"></span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 text-xs sm:text-sm text-gray-900" x-text="log.description || 'N/A'"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500" x-text="log.ip_address || 'N/A'"></td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                                <a :href="'/superadmin/audit-logs/' + log.id" 
                                   class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="auditLogs.length === 0">
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">No audit logs found</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 sm:px-6 py-3 border-t border-gray-200" x-show="pagination">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span x-text="pagination.from"></span> to <span x-text="pagination.to"></span> of <span x-text="pagination.total"></span> results
                </div>
                <div class="flex space-x-2">
                    <button @click="changePage(pagination.current_page - 1)" :disabled="!pagination.prev_page_url" 
                            class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                            class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium" x-text="loadingMessage"></span>
        </div>
    </div>
</div>

@php
$paginationData = ($auditLogs && method_exists($auditLogs, 'currentPage')) ? [
    'current_page' => $auditLogs->currentPage(),
    'last_page' => $auditLogs->lastPage(),
    'from' => $auditLogs->firstItem(),
    'to' => $auditLogs->lastItem(),
    'total' => $auditLogs->total(),
    'prev_page_url' => $auditLogs->previousPageUrl(),
    'next_page_url' => $auditLogs->nextPageUrl()
] : [
    'current_page' => 1,
    'last_page' => 1,
    'from' => 1,
    'to' => 1,
    'total' => 0,
    'prev_page_url' => null,
    'next_page_url' => null
];

$auditLogsItems = ($auditLogs && method_exists($auditLogs, 'items')) ? $auditLogs->items() : [];
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
function systemAuditApp() {
    return {
        filters: @json($filters ?? []),
        auditLogs: @json($auditLogsItems),
        pagination: @json($paginationData),
        stats: @json($auditStats),
        securityAlerts: @json($securityAlerts),
        departmentActivitySummary: @json($departmentActivitySummary),
        activityCharts: @json($activityCharts),
        filterOptions: @json($filterOptions),
        showSecurityAlerts: false,
        loading: false,
        loadingMessage: 'Loading...',
        charts: {},

        init() {
            this.initCharts();
        },

        initCharts() {
            // Daily Activity Chart
            const dailyCtx = document.getElementById('dailyActivityChart');
            if (dailyCtx) {
                const dailyData = @json($activityCharts['daily']);
                this.charts.daily = new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: dailyData.map(d => new Date(d.date).toLocaleDateString()),
                        datasets: [{
                            label: 'Events',
                            data: dailyData.map(d => d.count),
                            borderColor: 'rgb(79, 70, 229)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // User Type Chart
            const userTypeCtx = document.getElementById('userTypeChart');
            if (userTypeCtx) {
                const userTypeData = @json($activityCharts['by_user_type']);
                this.charts.userType = new Chart(userTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: userTypeData.map(d => d.user_type ? d.user_type.replace('App\\Models\\', '') : 'Unknown'),
                        datasets: [{
                            data: userTypeData.map(d => d.count),
                            backgroundColor: [
                                'rgba(79, 70, 229, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Action Chart
            const actionCtx = document.getElementById('actionChart');
            if (actionCtx) {
                const actionData = @json($activityCharts['by_action']);
                this.charts.action = new Chart(actionCtx, {
                    type: 'bar',
                    data: {
                        labels: actionData.map(d => d.action.charAt(0).toUpperCase() + d.action.slice(1)),
                        datasets: [{
                            label: 'Count',
                            data: actionData.map(d => d.count),
                            backgroundColor: 'rgba(79, 70, 229, 0.8)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // Severity Chart
            const severityCtx = document.getElementById('severityChart');
            if (severityCtx) {
                const severityData = @json($activityCharts['by_severity']);
                const severityColors = {
                    'critical': 'rgba(239, 68, 68, 0.8)',
                    'high': 'rgba(245, 158, 11, 0.8)',
                    'medium': 'rgba(234, 179, 8, 0.8)',
                    'low': 'rgba(16, 185, 129, 0.8)'
                };
                this.charts.severity = new Chart(severityCtx, {
                    type: 'bar',
                    data: {
                        labels: severityData.map(d => d.severity.charAt(0).toUpperCase() + d.severity.slice(1)),
                        datasets: [{
                            label: 'Count',
                            data: severityData.map(d => d.count),
                            backgroundColor: severityData.map(d => severityColors[d.severity] || 'rgba(156, 163, 175, 0.8)')
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        },

        applyFilters() {
            this.loading = true;
            this.loadingMessage = 'Applying filters...';
            
            const params = new URLSearchParams();
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key]) {
                    params.append(key, this.filters[key]);
                }
            });
            
            window.location.href = '{{ route("superadmin.audit.index") }}?' + params.toString();
        },

        clearFilters() {
            this.filters = {
                action: '',
                resource_type: '',
                severity: '',
                user_type: '',
                department_id: '',
                date_from: '',
                date_to: '',
                search: ''
            };
            this.applyFilters();
        },

        changePage(page) {
            if (page < 1 || page > this.pagination.last_page) return;
            
            this.loading = true;
            const params = new URLSearchParams(window.location.search);
            params.set('page', page);
            window.location.href = window.location.pathname + '?' + params.toString();
        },

        async refreshData() {
            this.loading = true;
            this.loadingMessage = 'Refreshing data...';
            window.location.reload();
        },

        async exportData() {
            this.loading = true;
            this.loadingMessage = 'Exporting data...';

            try {
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });

                const response = await fetch('{{ route("superadmin.audit.api.export") }}?' + params.toString());
                const data = await response.json();
                
                // Convert to CSV
                const headers = Object.keys(data[0] || {});
                let csv = headers.join(',') + '\n';
                data.forEach(row => {
                    csv += headers.map(header => '"' + (row[header] || '') + '"').join(',') + '\n';
                });

                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'system_audit_logs_' + new Date().toISOString().split('T')[0] + '.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                alert('Error exporting data: ' + error.message);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection

