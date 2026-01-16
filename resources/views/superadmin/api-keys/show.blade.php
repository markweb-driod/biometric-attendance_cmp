@extends('layouts.superadmin')

@section('title', 'API Key Details')
@section('page-title', 'API Key: ' . $apiKey->name)

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

<div class="w-full px-2 py-10 space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Key Overview</h2>
                    <span class="px-2 py-1 text-xs rounded-full {{ $apiKey->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $apiKey->isActive() ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                @if(session('api_key_plain') && session('api_secret_plain'))
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-sm text-yellow-900 font-semibold mb-2">Copy now — shown only once</p>
                        <div class="text-sm text-gray-800"><span class="font-medium">Key:</span> {{ session('api_key_plain') }}</div>
                        <div class="text-sm text-gray-800"><span class="font-medium">Secret:</span> {{ session('api_secret_plain') }}</div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Name</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Client</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->client_name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Created</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Last Used</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->last_used_at ? $apiKey->last_used_at->format('Y-m-d H:i') : 'Never' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Rate Limit (min)</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->rate_limit_per_minute }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Rate Limit (hour)</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->rate_limit_per_hour }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Expires</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : 'No expiry' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Created By</div>
                        <div class="font-medium text-gray-900">{{ $apiKey->creator?->name ?? '—' }}</div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('superadmin.api-keys.edit', $apiKey->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Edit</a>
                    <form method="POST" action="{{ route('superadmin.api-keys.toggle-status', $apiKey->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-black transition">{{ $apiKey->is_active ? 'Deactivate' : 'Activate' }}</button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.api-keys.regenerate', $apiKey->id) }}" onsubmit="return confirm('Regenerate key and secret? Old credentials will stop working.');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">Regenerate</button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.api-keys.destroy', $apiKey->id) }}" onsubmit="return confirm('Delete this API key? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Delete</button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Usage Statistics</h3>
                    <a href="{{ route('superadmin.api-keys.logs', $apiKey->id) }}" class="text-green-700 hover:text-green-800">View Logs</a>
                </div>
                @if(!empty($usageStats))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500">Requests (24h)</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $usageStats['requests_24h'] ?? 0 }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500">Error Rate (24h)</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $usageStats['error_rate_24h'] ?? 0 }}%</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500">Avg Latency</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $usageStats['avg_latency_ms'] ?? 0 }} ms</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-xs text-gray-500">Peak RPM</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $usageStats['peak_rpm'] ?? 0 }}</div>
                    </div>
                </div>
                @else
                <div class="text-gray-500">No usage stats available.</div>
                @endif

                @if(!empty($recentLogs) && $recentLogs->count())
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Recent Requests</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Endpoint</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Latency</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->endpoint }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $log->is_success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $log->is_success ? 'Success' : 'Error' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->response_code }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->latency_ms }} ms</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Key Details</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-500">Client Contact:</span> <span class="text-gray-800">{{ $apiKey->client_contact ?? '—' }}</span></div>
                    <div><span class="text-gray-500">ID:</span> <span class="text-gray-800">{{ $apiKey->id }}</span></div>
                    <div><span class="text-gray-500">Status:</span> <span class="text-gray-800">{{ $apiKey->isActive() ? 'Active' : 'Inactive' }}</span></div>
                    <div><span class="text-gray-500">Expires:</span> <span class="text-gray-800">{{ $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : 'No expiry' }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Documentation</h3>
                <p class="text-sm text-gray-700">See how to use this key, headers, and rate limits.</p>
                <a href="{{ route('superadmin.api-keys.documentation') }}" class="mt-3 inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Open Docs</a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.superadmin')

@section('title', 'API Key Details')
@section('page-title', 'API Key: ' . $apiKey->name)

@section('content')
<div class="w-full px-2 py-10 space-y-6">
    @if(session('api_key_plain'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="font-semibold text-yellow-800 mb-2">⚠️ IMPORTANT: Save these credentials now. They will not be shown again!</p>
            <div class="bg-white p-3 rounded border">
                <p class="text-sm text-gray-600">API Key:</p>
                <code class="block mt-1 text-sm bg-gray-100 p-2 rounded">{{ session('api_key_plain') }}</code>
                @if(session('api_secret_plain'))
                <p class="text-sm text-gray-600 mt-3">Secret:</p>
                <code class="block mt-1 text-sm bg-gray-100 p-2 rounded">{{ session('api_secret_plain') }}</code>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
            <h3 class="text-xl font-bold mb-4">Key Information</h3>
            <dl class="space-y-2">
                <dt class="font-semibold">Name:</dt>
                <dd>{{ $apiKey->name }}</dd>
                <dt class="font-semibold">Status:</dt>
                <dd>
                    <span class="px-2 py-1 text-xs rounded-full {{ $apiKey->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $apiKey->isActive() ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
                <dt class="font-semibold">Client:</dt>
                <dd>{{ $apiKey->client_name ?? 'N/A' }}</dd>
                <dt class="font-semibold">Created:</dt>
                <dd>{{ $apiKey->created_at->format('Y-m-d H:i:s') }}</dd>
                <dt class="font-semibold">Last Used:</dt>
                <dd>{{ $apiKey->last_used_at ? $apiKey->last_used_at->format('Y-m-d H:i:s') : 'Never' }}</dd>
            </dl>

            <div class="mt-6 space-x-2">
                <a href="{{ route('superadmin.api-keys.edit', $apiKey->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit</a>
                <form action="{{ route('superadmin.api-keys.toggle-status', $apiKey->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        {{ $apiKey->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form action="{{ route('superadmin.api-keys.regenerate', $apiKey->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Regenerate</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
            <h3 class="text-xl font-bold mb-4">Usage Statistics</h3>
            @if(isset($usageStats))
            <dl class="space-y-2">
                <dt class="font-semibold">Total Requests:</dt>
                <dd>{{ $usageStats['total']['total'] ?? 0 }}</dd>
                <dt class="font-semibold">Success Rate:</dt>
                <dd>{{ $usageStats['total']['success_rate'] ?? 0 }}%</dd>
                <dt class="font-semibold">Today:</dt>
                <dd>{{ $usageStats['today']['total'] ?? 0 }} requests</dd>
                <dt class="font-semibold">This Week:</dt>
                <dd>{{ $usageStats['this_week']['total'] ?? 0 }} requests</dd>
                <dt class="font-semibold">Avg Response Time:</dt>
                <dd>{{ $usageStats['performance']['avg_response_time_ms'] ?? 0 }}ms</dd>
            </dl>
            @else
            <p class="text-gray-500">No usage statistics available</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h3 class="text-xl font-bold mb-4">Recent Logs</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Time</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Endpoint</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Response Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentLogs as $log)
                    <tr>
                        <td class="px-4 py-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2"><code class="text-xs">{{ $log->endpoint }}</code></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $log->response_status >= 200 && $log->response_status < 300 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $log->response_status ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $log->response_time_ms ?? 'N/A' }}ms</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">No logs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

