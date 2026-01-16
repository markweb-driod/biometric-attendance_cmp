@extends('layouts.lecturer')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage your account and preferences')

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

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 border-b pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Settings</h1>
            <p class="text-base text-gray-500 mt-1">Manage your account and preferences</p>
        </div>
        <button onclick="saveSettings()" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-base font-semibold rounded-xl shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Save Changes
        </button>
    </div>
    <!-- Settings Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Settings -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Profile Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" value="John" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" value="Doe" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" value="john.doe@nsuk.edu.ng" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" value="+234 801 234 5678" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-base font-medium text-gray-700 mb-2">Department</label>
                        <select class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                            <option value="cs" selected>Computer Science Department, Nasarawa State University, Keffi</option>
                            <option value="math">Mathematics</option>
                            <option value="physics">Physics</option>
                            <option value="engineering">Engineering</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Security Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Security</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" placeholder="Enter current password" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" placeholder="Enter new password" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-base font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" placeholder="Confirm new password" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                </div>
            </div>
            
            <!-- Two-Factor Authentication Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Two-Factor Authentication</h3>
                @php
                    $user = auth('lecturer')->user();
                    $has2FA = $user && $user->user && $user->user->hasTwoFactorEnabled();
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
                        <div>
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
                        <p class="text-sm text-blue-800">
                            <strong>Two-factor authentication</strong> adds an extra layer of security. 
                            You'll need a verification code from your authenticator app (Google Authenticator, Authy, etc.) when logging in.
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        @if($has2FA)
                            <form action="{{ route('lecturer.2fa.disable') }}" method="POST" class="flex-1">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-base font-medium text-gray-700 mb-2">Confirm Password to Disable</label>
                                        <input type="password" name="password" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password" required>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold transition-colors">
                                        Disable Two-Factor Authentication
                                    </button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('lecturer.2fa.setup') }}" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-center transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                Enable Two-Factor Authentication
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Notifications Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Notifications</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-base font-medium text-gray-900">Email Notifications</p>
                            <p class="text-xs text-gray-500">Receive attendance reports via email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-base font-medium text-gray-900">SMS Notifications</p>
                            <p class="text-xs text-gray-500">Receive urgent alerts via SMS</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-base font-medium text-gray-900">Low Attendance Alerts</p>
                            <p class="text-xs text-gray-500">Get notified when attendance drops</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            <!-- Attendance Settings Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Attendance Settings</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Late Threshold (minutes)</label>
                        <input type="number" value="15" min="0" max="60" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700 mb-2">Low Attendance Threshold (%)</label>
                        <input type="number" value="75" min="0" max="100" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-base font-medium text-gray-700 mb-2">Default Attendance Method</label>
                        <select class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                            <option value="biometric" selected>Biometric</option>
                            <option value="manual">Manual Entry</option>
                            <option value="qr">QR Code</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Profile Card -->
            <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl shadow-lg border border-blue-200 p-8 text-center">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                    <span class="text-3xl font-bold text-blue-600">JD</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Dr. John Doe</h3>
                <p class="text-base text-gray-700">Computer Science Department, Nasarawa State University, Keffi</p>
                <p class="text-xs text-gray-500 mt-1">Lecturer ID: LEC001</p>
                <div class="mt-6 pt-6 border-t border-blue-200 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-xs text-gray-500">Classes</div>
                        <div class="text-lg font-bold text-blue-900">8</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Students</div>
                        <div class="text-lg font-bold text-blue-900">247</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Avg. Attendance</div>
                        <div class="text-lg font-bold text-blue-900">87%</div>
                    </div>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button onclick="exportData()" class="w-full flex items-center px-4 py-2 text-base text-blue-700 font-semibold rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Data
                    </button>
                    <button onclick="backupSettings()" class="w-full flex items-center px-4 py-2 text-base text-purple-700 font-semibold rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Backup Settings
                    </button>
                    <button onclick="resetSettings()" class="w-full flex items-center px-4 py-2 text-base text-red-700 font-semibold rounded-lg bg-red-50 hover:bg-red-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset to Default
                    </button>
                </div>
            </div>
            <!-- System Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
                <div class="space-y-2 text-base">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Version</span>
                        <span class="text-gray-900">2.1.0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Updated</span>
                        <span class="text-gray-900">Oct 15, 2024</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Database</span>
                        <span class="text-gray-900">MySQL 8.0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">PHP Version</span>
                        <span class="text-gray-900">8.1.0</span>
                    </div>
                </div>
            </div>
            <!-- Account Actions -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Actions</h3>
                <div class="space-y-3">
                    <button onclick="deactivateAccount()" class="w-full flex items-center px-4 py-2 text-base text-yellow-700 font-semibold rounded-lg bg-yellow-50 hover:bg-yellow-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Deactivate Account
                    </button>
                    <button onclick="deleteAccount()" class="w-full flex items-center px-4 py-2 text-base text-red-700 font-semibold rounded-lg bg-red-50 hover:bg-red-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function showToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}

function showSpinner(show = true) {
    window.dispatchEvent(new CustomEvent('spinner', { detail: { show } }));
}

function saveSettings() {
    showSpinner(true);
    // Save settings logic
    setTimeout(() => {
        showSpinner(false);
        showToast('Settings saved successfully!');
    }, 1000);
}
function exportData() {
    window.location.href = '/lecturer/settings/export';
}
function backupSettings() {
    window.location.href = '/lecturer/settings/backup';
}
function resetSettings() {
    Confirmations.reset('all settings', () => {
        showSpinner(true);
        // Reset settings logic
        setTimeout(() => {
            showSpinner(false);
            showToast('Settings reset to default successfully');
        }, 1000);
    });
}

function deactivateAccount() {
    Confirmations.deactivate('your account', () => {
        showSpinner(true);
        // Deactivate account logic
        setTimeout(() => {
            showSpinner(false);
            showToast('Account deactivated successfully');
        }, 1000);
    });
}

function deleteAccount() {
    Confirmations.custom(
        'Delete Account',
        'Are you sure you want to permanently delete your account? This action cannot be undone and all your data will be lost.',
        'Delete Account',
        'bg-red-600 hover:bg-red-700',
        () => {
            showSpinner(true);
            // Delete account logic
            setTimeout(() => {
                showSpinner(false);
                showToast('Account deleted successfully');
                // Redirect to login or home page
                setTimeout(() => {
                    window.location.href = '/lecturer';
                }, 2000);
            }, 1000);
        }
    );
}
</script>
@endsection 