<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NSUK Biometric Attendance') }}</title>
    <!-- Inter font (locally hosted) -->
    <link href="/fonts/inter/inter.css" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px; /* Offset for fixed navbar */
        }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex flex-col min-h-screen font-sans antialiased text-gray-900">
    <!-- Navbar -->
    <nav class="bg-white border-b border-green-100 shadow-sm fixed top-0 w-full z-50 backdrop-blur-md bg-opacity-90 transition-all duration-300" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between relative">
            <!-- Logo Section -->
            <div class="flex items-center flex-1 min-w-0 mr-2">
                <a href="/" class="flex items-center group min-w-0">
                    <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-10 w-10 sm:h-12 sm:w-12 object-contain rounded-full shadow-md group-hover:scale-105 transition-transform duration-300 mr-2 sm:mr-3 flex-shrink-0">
                    <div class="flex flex-col min-w-0">
                        <span class="text-sm sm:text-xl font-extrabold text-green-700 leading-tight tracking-tight truncate">Department of Computer Science</span>
                        <span class="text-[9px] sm:text-sm text-gray-600 font-medium truncate">Nasarawa State University, Keffi</span>
                    </div>
                </a>
            </div>
            <!-- Hamburger for mobile -->
            <button @click="open = !open" class="sm:hidden ml-auto p-2 rounded-lg border-2 border-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-300" aria-label="Open Menu">
                <svg x-show="!open" class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="open" x-cloak class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <!-- Nav links -->
            <div 
                :class="{'block': open, 'hidden': !open}" 
                class="absolute top-16 left-0 w-full sm:static sm:w-auto bg-white sm:bg-transparent border-b border-green-100 sm:border-0 shadow-lg sm:shadow-none py-4 sm:py-0 px-4 sm:px-0 flex flex-col sm:flex-row items-center gap-3 sm:gap-4 transition-all duration-200 z-40" 
                x-cloak
            >
                <!-- Take Attendance Button -->
                <a href="{{ route('student.attendance-capture') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-green-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Mark Attendance
                </a>

                <!-- Register Face Button (New) -->
                <a href="{{ route('student.register-face.form') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Register Face
                </a>

                <!-- Other Links -->
                <a href="{{ route('about') }}" class="w-full sm:w-auto text-center sm:text-left block px-4 py-2 hover:text-green-700 hover:bg-green-50 sm:hover:bg-transparent font-medium transition-all rounded">About</a>
                <a href="{{ route('contact') }}" class="w-full sm:w-auto text-center sm:text-left block px-4 py-2 hover:text-green-700 hover:bg-green-50 sm:hover:bg-transparent font-medium transition-all rounded">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center w-full px-2 sm:px-4 pb-4 relative">
        <!-- Spacer for Fixed Navbar -->
        <div style="height: 120px;" class="w-full shrink-0 sm:h-32"></div>

        <!-- Modern Mobile-Friendly Background -->
        <div class="fixed inset-0 pointer-events-none select-none -z-10">
            <div class="absolute top-1/3 left-1/2 w-[340px] h-[340px] bg-green-200 rounded-full blur-3xl opacity-40 -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute top-10 left-4 w-32 h-32 bg-blue-100 rounded-full blur-2xl opacity-30"></div>
            <div class="absolute bottom-10 right-4 w-24 h-24 bg-green-100 rounded-full blur-2xl opacity-30"></div>
            <div class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-white rounded-full blur-3xl opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
        </div>
        <div class="w-full flex flex-col items-center mt-2 sm:mt-4">
            <div class="@yield('wrapper_class', 'w-full max-w-lg sm:max-w-xl bg-gradient-to-br from-white via-green-50 to-green-100 rounded-2xl shadow-lg border border-green-100 p-2 sm:p-6 mt-2 sm:mt-4')" style="@yield('wrapper_style', 'backdrop-filter: blur(0.5px);')">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white/90 backdrop-blur-sm border-t border-green-200 py-4 text-center text-sm text-gray-600 font-medium" style="font-family: 'Inter', 'Montserrat', Arial, sans-serif;">
        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="w-6 h-6 bg-[#008000] rounded-full flex items-center justify-center text-white text-xs font-bold">N</div>
            <span class="font-semibold text-[#008000]">Nasarawa State University, Keffi</span>
        </div>
        <div>&copy; {{ date('Y') }} All rights reserved. | Department of Computer Science</div>
        <div class="mt-2 text-xs text-gray-500">
            Developed and Powered by <a href="https://wa.me/2349153390947" target="_blank" class="text-green-600 hover:text-green-800 transition-colors">Cybershrine Technologies</a>
        </div>
    </footer>
    
    <!-- Scroll to Top Button -->
    <button
        x-data="{ show: false }"
        @scroll.window="show = (window.pageYOffset > 200)"
        x-show="show"
        @click="window.scrollTo({top: 0, behavior: 'smooth'})"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-8"
        class="fixed bottom-6 right-4 sm:right-8 p-3 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 z-50 transition-all duration-300"
        aria-label="Scroll to top"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
    @include('components.toast')
    @include('components.spinner')
    <script defer src="/js/vendor/alpine.min.js"></script>
    
    @stack('scripts')
</body>
</html>