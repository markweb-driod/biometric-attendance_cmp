<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - NSUK Biometric Attendance</title>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2" style="font-family: 'Montserrat', sans-serif;">Reset Password</h1>
                    <p class="text-gray-600">Enter your new password</p>
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
                <form method="POST" action="{{ route('password.reset.submit') }}" 
                      class="space-y-6" 
                      x-data="{ 
                          showPassword: false, 
                          showConfirmPassword: false,
                          loading: false,
                          strength: 0,
                          checkStrength(password) {
                              if (!password) { this.strength = 0; return; }
                              let score = 0;
                              if (password.length >= 8) score++;
                              if (password.length >= 12) score++;
                              if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
                              if (/\d/.test(password)) score++;
                              if (/[^a-zA-Z\d]/.test(password)) score++;
                              this.strength = Math.min(score, 5);
                          }
                      }"
                      @submit="loading = true; window.dispatchEvent(new CustomEvent('spinner', { detail: { show: true } }));">
                    @csrf

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            New Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                x-bind:type="showPassword ? 'text' : 'password'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 pr-10"
                                placeholder="Enter new password"
                                required
                                minlength="8"
                                autofocus
                                :disabled="loading"
                                x-on:input="checkStrength($event.target.value)"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                :disabled="loading"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Password must be at least 8 characters long
                        </p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                x-bind:type="showConfirmPassword ? 'text' : 'password'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 pr-10"
                                placeholder="Confirm new password"
                                required
                                minlength="8"
                                :disabled="loading"
                            >
                            <button 
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                :disabled="loading"
                            >
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div 
                                    class="h-full transition-all duration-300"
                                    :class="{
                                        'bg-red-500': strength <= 1 && strength > 0,
                                        'bg-orange-500': strength === 2,
                                        'bg-yellow-500': strength === 3,
                                        'bg-blue-500': strength === 4,
                                        'bg-green-500': strength >= 5
                                    }"
                                    :style="'width: ' + (strength * 20) + '%'"
                                ></div>
                            </div>
                            <span class="text-xs text-gray-500" x-text="strength === 0 ? '' : strength <= 1 ? 'Weak' : strength <= 3 ? 'Medium' : 'Strong'"></span>
                        </div>
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
                        <span x-show="!loading">Reset Password</span>
                        <span x-show="loading" x-cloak>Resetting...</span>
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold text-sm">
                        ← Back to Login
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
