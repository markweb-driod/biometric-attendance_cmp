<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - NSUK Biometric Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/fonts/montserrat/montserrat.css" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-green-100 min-h-screen flex flex-col">
    <!-- Toast Notification Component -->
    <x-toast />
    
    <!-- Loading Spinner -->
    <x-spinner />
    
    <!-- Navbar -->
    <nav class="w-full bg-white/80 backdrop-blur-md shadow-lg border-b border-green-200 px-2 sm:px-8 py-2 sm:py-4">
        <div class="flex flex-row items-center justify-between w-full">
            <div class="flex items-center gap-2 sm:gap-4">
                <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-8 w-8 sm:h-12 sm:w-12 object-contain rounded-full shadow mr-2 sm:mr-4">
                <div class="flex flex-col">
                    <span class="text-base sm:text-xl font-extrabold text-green-700 leading-tight tracking-tight">Department of Computer Science</span>
                    <span class="text-[10px] sm:text-sm text-gray-600 -mt-1 font-medium">Nasarawa State University, Keffi</span>
                </div>
            </div>
            <a href="{{ route('login') }}" class="text-green-700 hover:text-green-600 font-semibold">Back to Login</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-green-200">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2" style="font-family: 'Montserrat', sans-serif;">Verify OTP</h1>
                    <p class="text-gray-600">Enter the 6-digit OTP sent to your {{ $user_type == 'superadmin' ? 'email' : 'email or phone' }}</p>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <strong>Error:</strong>
                        </div>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.verify-otp.submit') }}" 
                      class="space-y-6"
                      x-data="{ loading: false }"
                      @submit="loading = true; window.dispatchEvent(new CustomEvent('spinner', { detail: { show: true } }));">
                    @csrf

                    <!-- OTP Input -->
                    <div>
                        <label for="otp" class="block text-sm font-semibold text-gray-700 mb-2">
                            6-Digit OTP Code
                        </label>
                        <input 
                            type="text" 
                            id="otp" 
                            name="otp" 
                            value="{{ old('otp') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-center text-2xl tracking-widest font-mono"
                            placeholder="000000"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autofocus
                            autocomplete="off"
                            :disabled="loading"
                            x-on:input="value = value.replace(/[^0-9]/g, '')"
                        >
                        <p class="mt-2 text-xs text-gray-500">
                            Enter the 6-digit code sent to your registered contact
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                    >
                        <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!loading">Verify OTP</span>
                        <span x-show="loading" x-cloak>Verifying...</span>
                    </button>
                </form>

                <!-- Resend OTP -->
                <form method="POST" action="{{ route('password.resend-otp') }}" 
                      x-data="{ loading: false }"
                      @submit="loading = true; window.dispatchEvent(new CustomEvent('spinner', { detail: { show: true } }));">
                    @csrf
                    <button 
                        type="submit" 
                        :disabled="loading"
                        class="w-full text-green-600 hover:text-green-700 font-semibold text-sm py-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                    >
                        <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!loading">Didn't receive OTP? Resend</span>
                        <span x-show="loading" x-cloak>Resending...</span>
                    </button>
                </form>

                <!-- Back to Forgot Password -->
                <div class="mt-6 text-center">
                    <a href="{{ route('password.forgot') }}" class="text-green-600 hover:text-green-700 font-semibold text-sm">
                        ← Back to Forgot Password
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus and auto-submit when 6 digits are entered
            const otpInput = document.getElementById('otp');
            const form = otpInput.closest('form');
            
            if (otpInput && form) {
                otpInput.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Auto-submit when 6 digits are entered
                    if (this.value.length === 6) {
                        // Small delay for better UX
                        setTimeout(() => {
                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn && !submitBtn.disabled) {
                                form.submit();
                            }
                        }, 300);
                    }
                });
            }

            // Show toast notifications from session
            @if(session('flash_message'))
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { 
                        message: '{{ session('flash_message') }}', 
                        type: '{{ session('flash_type', 'success') }}' 
                    } 
                }));
            @elseif(session('success'))
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { 
                        message: '{{ session('success') }}', 
                        type: 'success' 
                    } 
                }));
            @endif

            @if($errors->any())
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { 
                        message: '{{ $errors->first() }}', 
                        type: 'error' 
                    } 
                }));
            @endif

            // Hide spinner on page load
            window.dispatchEvent(new CustomEvent('spinner', { detail: { show: false } }));
        });
    </script>
</body>
</html>
