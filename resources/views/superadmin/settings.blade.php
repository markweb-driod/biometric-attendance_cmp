@extends('layouts.superadmin')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage your profile and all system settings')

@push('styles')
<style>
    .settings-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .settings-header {
        background: linear-gradient(to right, #10b981 0%, #059669 100%);
        color: white;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .settings-content {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease-in-out;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background-color: white;
    }
    
    .form-checkbox {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }
    
    .btn-save {
        background: linear-gradient(to right, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    
    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
    
    .btn-test {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }
    
    .btn-reset {
        background: #ef4444;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }
    
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .status-healthy { background-color: #10b981; }
    .status-warning { background-color: #f59e0b; }
    .status-error { background-color: #ef4444; }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification.success { background-color: #10b981; }
    .notification.error { background-color: #ef4444; }
    .notification.info { background-color: #3b82f6; }
    
    .slider {
        -webkit-appearance: none;
        appearance: none;
        height: 8px;
        border-radius: 4px;
        outline: none;
        opacity: 0.7;
        transition: opacity .2s;
    }

    .slider:hover {
        opacity: 1;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #3b82f6;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #3b82f6;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
        .grid-2, .grid-3 {
            grid-template-columns: 1fr;
        }
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

@if(session('info'))
<div id="flash-info" class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>{{ session('info') }}</span>
    <button onclick="closeFlash('flash-info')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif
<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">
    <!-- Profile & Password Section -->
    <div class="flex flex-col md:flex-row gap-4">
        <!-- Profile -->
        <section class="flex-1 bg-white rounded-2xl shadow-xl border border-green-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-green-800">Profile</h2>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Name</label>
                        <input type="text" class="form-input" value="{{ $user->full_name ?? 'Superadmin' }}">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" value="{{ $user->email ?? 'superadmin@nsuk.edu.ng' }}">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-save">Update Profile</button>
                </div>
            </form>
        </section>
        
        <!-- Change Password -->
        <section class="flex-1 bg-white rounded-2xl shadow-xl border border-blue-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-blue-800">Change Password</h2>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-input">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-input">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-save">Change Password</button>
                </div>
            </form>
        </section>
    </div>

    <!-- Two-Factor Authentication Section -->
    <section class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-green-800">Two-Factor Authentication</h2>
        </div>
        
        @php
            $user = auth('superadmin')->user();
            $has2FA = $user && $user->hasTwoFactorEnabled();
        @endphp
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-semibold text-gray-900">Status</p>
                    <p class="text-sm text-gray-600">
                        @if($has2FA)
                            Two-factor authentication is <span class="text-green-600 font-semibold">enabled</span>
                        @else
                            Two-factor authentication is <span class="text-gray-500 font-semibold">disabled</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center">
                    @if($has2FA)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Inactive
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800 mb-2">
                    <strong>Two-factor authentication</strong> adds an extra layer of security to your account. 
                    After enabling, you'll need to enter a verification code from your authenticator app 
                    (like Google Authenticator or Authy) when logging in.
                </p>
            </div>
            
            <div class="flex gap-3">
                @if($has2FA)
                    <form action="{{ route('superadmin.2fa.disable') }}" method="POST" id="disable-2fa-form" class="flex-1">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Confirm Password to Disable 2FA</label>
                                <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors">
                                Disable Two-Factor Authentication
                            </button>
                        </div>
                    </form>
                @else
                    <a href="{{ route('superadmin.2fa.setup') }}" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-center transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Enable Two-Factor Authentication
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- System Health Status -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">System Health Status</h2>
        </div>
        <div class="settings-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="status-indicator status-healthy"></div>
                    <span class="text-sm font-medium">Database</span>
                    <div class="text-xs text-gray-500" id="db-status">Healthy</div>
                </div>
                <div class="text-center">
                    <div class="status-indicator status-healthy"></div>
                    <span class="text-sm font-medium">Storage</span>
                    <div class="text-xs text-gray-500" id="storage-status">85% Free</div>
                </div>
                <div class="text-center">
                    <div class="status-indicator status-warning"></div>
                    <span class="text-sm font-medium">API Services</span>
                    <div class="text-xs text-gray-500" id="api-status">Configure API</div>
                </div>
                <div class="text-center">
                    <div class="status-indicator status-healthy"></div>
                    <span class="text-sm font-medium">Performance</span>
                    <div class="text-xs text-gray-500" id="perf-status">Normal</div>
                </div>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">General Settings</h2>
        </div>
        <div class="settings-content">
            <form id="general-settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Institution Name</label>
                        <input type="text" name="institution_name" class="form-input" value="{{ $settings['general']['institution_name'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Institution Email</label>
                        <input type="email" name="institution_email" class="form-input" value="{{ $settings['general']['institution_email'] ?? '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Institution Address</label>
                    <textarea name="institution_address" class="form-input" rows="3">{{ $settings['general']['institution_address'] ?? '' }}</textarea>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="institution_phone" class="form-input" value="{{ $settings['general']['institution_phone'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            <option value="Africa/Lagos" {{ ($settings['general']['timezone'] ?? '') == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos</option>
                            <option value="UTC" {{ ($settings['general']['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Format</label>
                        <select name="date_format" class="form-select">
                            <option value="Y-m-d" {{ ($settings['general']['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="d-m-Y" {{ ($settings['general']['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                            <option value="m/d/Y" {{ ($settings['general']['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('general')">Reset</button>
                    <button type="submit" class="btn-save">Save General Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Academic Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Academic Settings</h2>
        </div>
        <div class="settings-content">
            <form id="academic-settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Current Academic Year</label>
                        <input type="text" name="current_academic_year" class="form-input" value="{{ $settings['academic']['current_academic_year'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Semester</label>
                        <select name="current_semester" class="form-select">
                            <option value="First Semester" {{ ($settings['academic']['current_semester'] ?? '') == 'First Semester' ? 'selected' : '' }}>First Semester</option>
                            <option value="Second Semester" {{ ($settings['academic']['current_semester'] ?? '') == 'Second Semester' ? 'selected' : '' }}>Second Semester</option>
                        </select>
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Class Duration (minutes)</label>
                        <input type="number" name="class_duration_minutes" class="form-input" value="{{ $settings['academic']['class_duration_minutes'] ?? 60 }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Attendance Tolerance (minutes)</label>
                        <input type="number" name="attendance_tolerance_minutes" class="form-input" value="{{ $settings['academic']['attendance_tolerance_minutes'] ?? 15 }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Absence Threshold</label>
                        <input type="number" name="max_absence_threshold" class="form-input" value="{{ $settings['academic']['max_absence_threshold'] ?? 10 }}">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('academic')">Reset</button>
                    <button type="submit" class="btn-save">Save Academic Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Face Verification Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Face Verification Settings</h2>
        </div>
        <div class="settings-content">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded text-center font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('superadmin.face-config.update') }}" id="face-config-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">Face Provider</label>
                    <select name="face_provider" id="face_provider_select" class="form-select">
                        <option value="faceplusplus" {{ ($provider ?? '') == 'faceplusplus' ? 'selected' : '' }}>Face++</option>
                        <option value="aws" {{ ($provider ?? '') == 'aws' ? 'selected' : '' }}>AWS Rekognition</option>
                        <option value="azure" {{ ($provider ?? '') == 'azure' ? 'selected' : '' }}>Azure Face API</option>
                    </select>
                </div>
                
                <!-- Face++ Fields -->
                <div class="provider-fields" id="faceplusplus-fields">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Face++ API Key</label>
                            <input type="text" name="faceplusplus_api_key" id="faceplusplus_api_key" class="form-input" value="{{ $apiKey ?? '' }}" placeholder="Face++ API Key">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Face++ API Secret</label>
                            <input type="text" name="faceplusplus_api_secret" id="faceplusplus_api_secret" class="form-input" value="{{ $apiSecret ?? '' }}" placeholder="Face++ API Secret">
                        </div>
                    </div>
                    <button type="button" id="testFacePPBtn" class="btn-test">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Test Connection
                    </button>
                </div>
                
                <!-- AWS & Azure Fields (hidden by default) -->
                <div class="provider-fields hidden" id="aws-fields">
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">AWS Access Key ID</label>
                            <input type="text" name="aws_access_key_id" class="form-input" placeholder="AWS Access Key ID">
                        </div>
                        <div class="form-group">
                            <label class="form-label">AWS Secret Access Key</label>
                            <input type="text" name="aws_secret_access_key" class="form-input" placeholder="AWS Secret Access Key">
                        </div>
                        <div class="form-group">
                            <label class="form-label">AWS Region</label>
                            <input type="text" name="aws_region" class="form-input" placeholder="e.g. us-east-1">
                        </div>
                    </div>
                </div>
                
                <div class="provider-fields hidden" id="azure-fields">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Azure Face API Key</label>
                            <input type="text" name="azure_face_api_key" class="form-input" placeholder="Azure Face API Key">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Azure Endpoint</label>
                            <input type="text" name="azure_face_endpoint" class="form-input" placeholder="https://<region>.api.cognitive.microsoft.com">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="submit" class="btn-save">Save Face Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Biometric Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Biometric Settings</h2>
        </div>
        <div class="settings-content">
            <form id="biometric-settings-form">
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Confidence Threshold (%)</label>
                        <div class="relative">
                            <input type="number" name="face_confidence_threshold" id="confidence_threshold" class="form-input" value="{{ $settings['biometric']['face_confidence_threshold'] ?? 75 }}" min="0" max="100" step="1">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">
                                <span id="threshold_indicator">{{ $settings['biometric']['face_confidence_threshold'] ?? 75 }}%</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="range" id="confidence_slider" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider" min="0" max="100" value="{{ $settings['biometric']['face_confidence_threshold'] ?? 75 }}" style="background: linear-gradient(to right, #ef4444 0%, #f59e0b 50%, #10b981 100%);">
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0% (Very Strict)</span>
                                <span>50% (Balanced)</span>
                                <span>100% (Very Loose)</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Image Quality Threshold (%)</label>
                        <input type="number" name="face_image_quality_threshold" class="form-input" value="{{ $settings['biometric']['face_image_quality_threshold'] ?? 50 }}" min="0" max="100">
                        <div class="mt-1 text-xs text-gray-600">Minimum image quality required for face detection</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Detection Sensitivity</label>
                        <select name="face_detection_sensitivity" class="form-select">
                            <option value="low" {{ ($settings['biometric']['face_detection_sensitivity'] ?? '') == 'low' ? 'selected' : '' }}>Low - Conservative detection</option>
                            <option value="medium" {{ ($settings['biometric']['face_detection_sensitivity'] ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium - Balanced detection</option>
                            <option value="high" {{ ($settings['biometric']['face_detection_sensitivity'] ?? '') == 'high' ? 'selected' : '' }}>High - Aggressive detection</option>
                        </select>
                        <div class="mt-1 text-xs text-gray-600">How sensitive the face detection algorithm should be</div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h3 class="font-bold text-gray-700 mb-4">Browser-Side AI Detection (New)</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Enable AI Detection</label>
                            <div class="flex items-center mt-2">
                                <input type="checkbox" name="enable_browser_face_detection" class="form-checkbox" {{ ($settings['biometric']['enable_browser_face_detection'] ?? true) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600">Use face-api.js for client-side pre-validation</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Allow Loose Alignment</label>
                            <div class="flex items-center mt-2">
                                <input type="checkbox" name="browser_face_allow_loose_alignment" class="form-checkbox" {{ ($settings['biometric']['browser_face_allow_loose_alignment'] ?? true) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600">Allow tilted/off-center faces if detected</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">AI Confidence Threshold (0.1 - 0.9)</label>
                        <input type="number" name="browser_face_confidence_threshold" class="form-input" value="{{ $settings['biometric']['browser_face_confidence_threshold'] ?? 0.5 }}" min="0.1" max="0.9" step="0.05">
                        <div class="mt-1 text-xs text-gray-600">Lower = Easier detection, Higher = Stricter checks. Default: 0.5</div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-test" onclick="testConfidenceThreshold()">Test Threshold</button>
                    <button type="button" class="btn-test" onclick="testFaceAPI()">Test API</button>
                    <button type="button" class="btn-reset" onclick="resetSettings('biometric')">Reset</button>
                    <button type="submit" class="btn-save">Save Biometric Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Face Registration Control -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Bulk Face Registration Control</h2>
        </div>
        <div class="settings-content">
            <div class="flex items-center gap-4 mb-4">
                <span id="faceRegStatusBadge" class="inline-flex items-center px-4 py-2 rounded-full font-semibold text-white bg-gray-400">Loading status...</span>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <button id="enableAllFaceRegBtn" type="button" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Enable Face Registration for All Students</button>
                <button id="disableAllFaceRegBtn" type="button" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">Disable Face Registration for All Students</button>
            </div>
            <!-- Confirmation Modal -->
            <div id="faceRegConfirmModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full flex flex-col items-center">
                    <h3 class="text-xl font-bold text-green-800 mb-4" id="faceRegConfirmTitle">Confirm Action</h3>
                    <p class="text-gray-700 mb-6 text-center" id="faceRegConfirmMsg">Are you sure?</p>
                    <div class="flex gap-4 w-full justify-center">
                        <button id="faceRegConfirmCancel" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">Cancel</button>
                        <button id="faceRegConfirmOk" class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Security Settings</h2>
        </div>
        <div class="settings-content">
            <form id="security-settings-form">
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Password Min Length</label>
                        <input type="number" name="password_min_length" class="form-input" value="{{ $settings['security']['password_min_length'] ?? 8 }}" min="6" max="20">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout_minutes" class="form-input" value="{{ $settings['security']['session_timeout_minutes'] ?? 120 }}" min="30" max="480">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Login Attempts</label>
                        <input type="number" name="max_login_attempts" class="form-input" value="{{ $settings['security']['max_login_attempts'] ?? 5 }}" min="3" max="10">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Lockout Duration (minutes)</label>
                        <input type="number" name="lockout_duration_minutes" class="form-input" value="{{ $settings['security']['lockout_duration_minutes'] ?? 15 }}" min="5" max="60">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Require 2FA</label>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="require_2fa" class="form-checkbox" {{ ($settings['security']['require_2fa'] ?? false) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-600">Enable two-factor authentication</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('security')">Reset</button>
                    <button type="submit" class="btn-save">Save Security Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Notification Settings</h2>
        </div>
        <div class="settings-content">
            <form id="notification-settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-input" value="{{ $settings['notifications']['smtp_host'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" name="smtp_port" class="form-input" value="{{ $settings['notifications']['smtp_port'] ?? 587 }}">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="smtp_username" class="form-input" value="{{ $settings['notifications']['smtp_username'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="smtp_password" class="form-input" value="{{ $settings['notifications']['smtp_password'] ?? '' }}">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">SMTP Encryption</label>
                        <select name="smtp_encryption" class="form-select">
                            <option value="tls" {{ ($settings['notifications']['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['notifications']['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Email</label>
                        <input type="email" name="from_email" class="form-input" value="{{ $settings['notifications']['from_email'] ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Name</label>
                        <input type="text" name="from_name" class="form-input" value="{{ $settings['notifications']['from_name'] ?? 'NSUK Attendance System' }}">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-test" onclick="testEmail()">Test Email</button>
                    <button type="button" class="btn-reset" onclick="resetSettings('notifications')">Reset</button>
                    <button type="submit" class="btn-save">Save Notification Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Performance Settings</h2>
        </div>
        <div class="settings-content">
            <form id="performance-settings-form">
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Cache Duration (minutes)</label>
                        <input type="number" name="cache_duration_minutes" class="form-input" value="{{ $settings['performance']['cache_duration_minutes'] ?? 60 }}" min="5" max="1440">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Upload Size (MB)</label>
                        <input type="number" name="max_upload_size_mb" class="form-input" value="{{ $settings['performance']['max_upload_size_mb'] ?? 10 }}" min="1" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Image Quality (%)</label>
                        <input type="number" name="image_compression_quality" class="form-input" value="{{ $settings['performance']['image_compression_quality'] ?? 80 }}" min="10" max="100">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('performance')">Reset</button>
                    <button type="submit" class="btn-save">Save Performance Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container"></div>
@endsection

@push('scripts')
<script>
// Global notification function
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    container.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // General settings
    const generalForm = document.getElementById('general-settings-form');
    if (generalForm) {
        generalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings('general', new FormData(this));
        });
    }
    
    // Academic settings
    const academicForm = document.getElementById('academic-settings-form');
    if (academicForm) {
        academicForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings('academic', new FormData(this));
        });
    }
    
    // Biometric settings
    const biometricForm = document.getElementById('biometric-settings-form');
    if (biometricForm) {
        biometricForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings('biometric', new FormData(this));
        });
    }
    
    // Security settings
    const securityForm = document.getElementById('security-settings-form');
    if (securityForm) {
        securityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings('security', new FormData(this));
        });
    }
    
    // Notification settings
    const notificationForm = document.getElementById('notification-settings-form');
    if (notificationForm) {
        notificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings('notifications', new FormData(this));
        });
    }
    
    // Performance settings
    const performanceForm = document.getElementById('performance-settings-form');
    if (performanceForm) {
        performanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveSettings('performance', new FormData(this));
        });
    }

    // Confidence threshold slider sync
    const confidenceInput = document.getElementById('confidence_threshold');
    const confidenceSlider = document.getElementById('confidence_slider');
    const thresholdIndicator = document.getElementById('threshold_indicator');
    
    if (confidenceInput && confidenceSlider && thresholdIndicator) {
        confidenceSlider.addEventListener('input', function() {
            confidenceInput.value = this.value;
            thresholdIndicator.textContent = this.value + '%';
        });
        
        confidenceInput.addEventListener('input', function() {
            confidenceSlider.value = this.value;
            thresholdIndicator.textContent = this.value + '%';
        });
    }

    // Provider switching
    const providerSelect = document.getElementById('face_provider_select');
    if (providerSelect) {
        providerSelect.addEventListener('change', function() {
            const selectedProvider = this.value;
            const allFields = document.querySelectorAll('.provider-fields');
            allFields.forEach(field => field.classList.add('hidden'));
            
            if (selectedProvider === 'faceplusplus') {
                document.getElementById('faceplusplus-fields').classList.remove('hidden');
            } else if (selectedProvider === 'aws') {
                document.getElementById('aws-fields').classList.remove('hidden');
            } else if (selectedProvider === 'azure') {
                document.getElementById('azure-fields').classList.remove('hidden');
            }
        });
        providerSelect.dispatchEvent(new Event('change'));
    }

    // Face++ Test Connection
    const testFacePPBtn = document.getElementById('testFacePPBtn');
    if (testFacePPBtn) {
        testFacePPBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const apiKey = document.getElementById('faceplusplus_api_key').value;
            const apiSecret = document.getElementById('faceplusplus_api_secret').value;
            
            if (!apiKey || !apiSecret) {
                showNotification('Please enter both API Key and API Secret', 'error');
                return;
            }
            
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin">⏳</span> Testing...';
            
            axios.post('/superadmin/settings/test-facepp', {
                api_key: apiKey,
                api_secret: apiSecret
            })
            .then(response => {
                if (response.data.success) {
                    showNotification(response.data.message || 'Connection successful!', 'success');
                } else {
                    showNotification(response.data.message || 'Connection failed', 'error');
                }
            })
            .catch(error => {
                showNotification('Failed to test connection: ' + (error.response?.data?.message || error.message), 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    }

    // Face Registration Status & Controls
    function updateFaceRegStatusBadge(status) {
        const badge = document.getElementById('faceRegStatusBadge');
        if (!badge) return;
        
        if (status === 'all_enabled') {
            badge.textContent = 'Face Registration: Enabled for All';
            badge.className = 'inline-flex items-center px-4 py-2 rounded-full font-semibold text-white bg-green-600';
        } else if (status === 'all_disabled') {
            badge.textContent = 'Face Registration: Disabled for All';
            badge.className = 'inline-flex items-center px-4 py-2 rounded-full font-semibold text-white bg-red-600';
        } else {
            badge.textContent = 'Face Registration: Partially Enabled';
            badge.className = 'inline-flex items-center px-4 py-2 rounded-full font-semibold text-white bg-yellow-500';
        }
    }

    function fetchFaceRegStatus() {
        axios.get('/superadmin/students/face-registration-status')
            .then(res => updateFaceRegStatusBadge(res.data.status))
            .catch(() => updateFaceRegStatusBadge('partial'));
    }
    
    fetchFaceRegStatus();

    // Face Registration Modal
    let faceRegModalAction = null;
    const modal = document.getElementById('faceRegConfirmModal');
    const modalTitle = document.getElementById('faceRegConfirmTitle');
    const modalMsg = document.getElementById('faceRegConfirmMsg');
    const modalOk = document.getElementById('faceRegConfirmOk');
    const modalCancel = document.getElementById('faceRegConfirmCancel');

    if (modal && modalOk && modalCancel) {
        function showFaceRegModal(action, title, msg) {
            faceRegModalAction = action;
            modalTitle.textContent = title;
            modalMsg.textContent = msg;
            modal.classList.remove('hidden');
        }
        
        function hideFaceRegModal() {
            modal.classList.add('hidden');
            faceRegModalAction = null;
        }
        
        modalCancel.onclick = hideFaceRegModal;
        modalOk.onclick = function() {
            if (faceRegModalAction) faceRegModalAction();
            hideFaceRegModal();
        };
        modal.onclick = function(e) { if (e.target === modal) hideFaceRegModal(); };

        const enableBtn = document.getElementById('enableAllFaceRegBtn');
        const disableBtn = document.getElementById('disableAllFaceRegBtn');

        if (enableBtn) {
            enableBtn.onclick = function(e) {
                e.preventDefault();
                showFaceRegModal(
                    function() {
                        axios.post('/superadmin/students/enable-face-registration-all')
                            .then(() => {
                                showNotification('Face registration enabled for all students', 'success');
                                fetchFaceRegStatus();
                            })
                            .catch(() => showNotification('Failed to enable face registration', 'error'));
                    },
                    'Enable Face Registration',
                    'Are you sure you want to enable face registration for ALL students?'
                );
            };
        }

        if (disableBtn) {
            disableBtn.onclick = function(e) {
                e.preventDefault();
                showFaceRegModal(
                    function() {
                        axios.post('/superadmin/students/disable-face-registration-all')
                            .then(() => {
                                showNotification('Face registration disabled for all students', 'success');
                                fetchFaceRegStatus();
                            })
                            .catch(() => showNotification('Failed to disable face registration', 'error'));
                    },
                    'Disable Face Registration',
                    'Are you sure you want to disable face registration for ALL students?'
                );
            };
        }
    }
});

// Save settings function
function saveSettings(category, formData) {
    const settings = {};
    
    for (let [key, value] of formData.entries()) {
        if (formData.getAll(key).length > 1) {
            if (!settings[key]) settings[key] = [];
            settings[key].push(value);
        } else {
            settings[key] = value;
        }
    }
    
    Object.keys(settings).forEach(key => {
        if (settings[key] === 'on') {
            settings[key] = true;
        }
    });
    
    fetch('/superadmin/system-settings/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            category: category,
            settings: settings
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving settings: ' + error.message, 'error');
    });
}

// Reset settings function
function resetSettings(category) {
    if (confirm(`Are you sure you want to reset ${category} settings to default values?`)) {
        fetch('/superadmin/system-settings/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ category: category })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error resetting settings: ' + error.message, 'error');
        });
    }
}

// Test Face API
function testFaceAPI() {
    const form = document.getElementById('biometric-settings-form');
    if (!form) return;
    
    const formData = new FormData(form);
    const apiKey = formData.get('faceplusplus_api_key');
    const apiSecret = formData.get('faceplusplus_api_secret');
    
    if (!apiKey || !apiSecret) {
        showNotification('Please enter both API Key and API Secret', 'error');
        return;
    }
    
    showNotification('Testing Face++ API connection...', 'info');
    
    fetch('/superadmin/system-settings/test-face-api', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            api_key: apiKey,
            api_secret: apiSecret
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing API: ' + error.message, 'error');
    });
}

// Test email configuration
function testEmail() {
    const form = document.getElementById('notification-settings-form');
    if (!form) return;
    
    const formData = new FormData(form);
    const testEmail = prompt('Enter email address to send test email to:');
    if (!testEmail) return;
    
    const emailData = {
        smtp_host: formData.get('smtp_host'),
        smtp_port: formData.get('smtp_port'),
        smtp_username: formData.get('smtp_username'),
        smtp_password: formData.get('smtp_password'),
        smtp_encryption: formData.get('smtp_encryption'),
        from_email: formData.get('from_email'),
        test_email: testEmail
    };
    
    showNotification('Testing email configuration...', 'info');
    
    fetch('/superadmin/system-settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(emailData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing email: ' + error.message, 'error');
    });
}

// Test confidence threshold
function testConfidenceThreshold() {
    const threshold = document.getElementById('confidence_threshold')?.value;
    
    if (!threshold || threshold < 0 || threshold > 100) {
        showNotification('Please enter a valid confidence threshold (0-100)', 'error');
        return;
    }
    
    showNotification('Testing confidence threshold...', 'info');
    
    fetch('/superadmin/system-settings/test-confidence-threshold', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            threshold: parseInt(threshold)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing threshold: ' + error.message, 'error');
    });
}
</script>
@endpush
