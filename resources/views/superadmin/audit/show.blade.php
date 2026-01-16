@extends('layouts.superadmin')

@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')
@section('page-description', 'Comprehensive activity details for audit log #' . $auditLog->id)

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

<div class="container mx-auto p-4 md:p-6 space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-3xl shadow-2xl p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-white">
                <h1 class="text-3xl font-bold mb-2">Audit Log Details</h1>
                <p class="text-green-100">Comprehensive activity information for log #{{ $auditLog->id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('superadmin.audit.index') }}" 
                   class="px-4 py-2 bg-white bg-opacity-20 backdrop-blur-sm text-white rounded-xl hover:bg-opacity-30 transition">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Logs
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Basic Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Log ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $auditLog->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Action</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($auditLog->action ?? 'N/A') }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Resource Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($auditLog->resource_type ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Resource ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $auditLog->resource_id ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Severity</dt>
                            <dd class="mt-1">
                                @php
                                    $severityColors = [
                                        'critical' => 'bg-red-100 text-red-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                        'low' => 'bg-green-100 text-green-800'
                                    ];
                                    $severityColor = $severityColors[$auditLog->severity] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $severityColor }}">
                                    {{ ucfirst($auditLog->severity ?? 'N/A') }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Timestamp</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</dd>
                            <dd class="text-xs text-gray-500">{{ $auditLog->created_at->diffForHumans() }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->description ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">User Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">User Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($auditLog->user_type ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">User ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $auditLog->user_id ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->department ? $auditLog->department->name : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $auditLog->department_id ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Value Changes Card (if applicable) -->
            @if($auditLog->old_values || $auditLog->new_values)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Value Changes</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Old Values -->
                        <div>
                            <h3 class="text-sm font-semibold text-red-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Previous Values
                            </h3>
                            @if($auditLog->old_values)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <pre class="text-xs text-gray-800 whitespace-pre-wrap overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No previous values</p>
                            @endif
                        </div>
                        <!-- New Values -->
                        <div>
                            <h3 class="text-sm font-semibold text-green-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                New Values
                            </h3>
                            @if($auditLog->new_values)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <pre class="text-xs text-gray-800 whitespace-pre-wrap overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">No new values</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Network & Session Information Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Network & Session Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $auditLog->ip_address ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Session ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono break-all">{{ $auditLog->session_id ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $auditLog->user_agent ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Right Column - Metadata & Actions -->
        <div class="space-y-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="window.print()" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Details
                    </button>
                    <button onclick="copyToClipboard()" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy JSON
                    </button>
                    <a href="{{ route('superadmin.audit.index', ['resource_type' => $auditLog->resource_type, 'resource_id' => $auditLog->resource_id]) }}" 
                       class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium text-center">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Related Logs
                    </a>
                </div>
            </div>

            <!-- Metadata Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Metadata</h2>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</dd>
                            <dd class="text-xs text-gray-500">{{ $auditLog->created_at->diffForHumans() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->updated_at->format('Y-m-d H:i:s') }}</dd>
                            <dd class="text-xs text-gray-500">{{ $auditLog->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Raw JSON Data Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Raw JSON Data</h2>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                        <pre id="jsonData" class="text-xs text-gray-800 whitespace-pre-wrap">{{ json_encode($auditLog->toArray(), JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const jsonData = document.getElementById('jsonData').textContent;
    navigator.clipboard.writeText(jsonData).then(() => {
        alert('JSON data copied to clipboard!');
    }).catch(() => {
        alert('Failed to copy to clipboard');
    });
}
</script>

@push('styles')
<style>
@media print {
    .container {
        max-width: 100%;
    }
    a[href]:after {
        content: "";
    }
    button {
        display: none;
    }
}
</style>
@endpush
@endsection

