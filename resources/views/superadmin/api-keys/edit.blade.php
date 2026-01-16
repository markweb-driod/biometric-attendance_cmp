@extends('layouts.superadmin')

@section('title', 'Edit API Key')
@section('page-title', 'Edit API Key: ' . $apiKey->name)

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
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit API Key</h2>

        <form method="POST" action="{{ route('superadmin.api-keys.update', $apiKey->id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('name', $apiKey->name) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Name</label>
                    <input type="text" name="client_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('client_name', $apiKey->client_name) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Client Contact</label>
                    <input type="text" name="client_contact" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('client_contact', $apiKey->client_contact) }}">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rate Limit (per minute)</label>
                        <input type="number" name="rate_limit_per_minute" min="1" max="1000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('rate_limit_per_minute', $apiKey->rate_limit_per_minute) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rate Limit (per hour)</label>
                        <input type="number" name="rate_limit_per_hour" min="1" max="10000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('rate_limit_per_hour', $apiKey->rate_limit_per_hour) }}">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                    <input type="date" name="expires_at" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ old('expires_at', $apiKey->expires_at?->format('Y-m-d')) }}">
                </div>

                <div class="flex gap-4 mt-6">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Update API Key
                    </button>
                    <a href="{{ route('superadmin.api-keys.show', $apiKey->id) }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

