<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NSUK Biometric Attendance') }}</title>
    <!-- Inter font from Google Fonts (modern, highly legible) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            overflow-y: visible !important;
        }
        body, .font-sans {
            font-family: 'Inter', 'Montserrat', Arial, sans-serif !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-100 via-blue-50 to-green-200 min-h-screen flex flex-col text-gray-900 font-sans">
    <!-- Navbar (with logo, department, about/contact) -->
    <nav class="w-full bg-white/80 backdrop-blur-md shadow-lg border-b border-green-200 px-2 sm:px-8 py-2 sm:py-4 rounded-b-2xl z-30" x-data="{ open: false }">
        <div class="flex flex-row items-center justify-between w-full">
            <div class="flex items-center gap-2 sm:gap-4">
                <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-8 w-8 sm:h-12 sm:w-12 object-contain rounded-full shadow mr-2 sm:mr-4">
                <div class="flex flex-col">
                    <span class="text-base sm:text-xl font-extrabold text-green-700 leading-tight tracking-tight">Department of Computer Science</span>
                    <span class="text-[10px] sm:text-sm text-gray-600 -mt-1 font-medium">Nasarawa State University, Keffi</span>
                </div>
            </div>
            <!-- Hamburger for mobile -->
            <button @click="open = !open" class="sm:hidden ml-auto p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-300" aria-label="Open Menu">
                <svg x-show="!open" class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="open" class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <!-- Nav links -->
            <div :class="{'block': open, 'hidden': !open}" class="absolute left-2 right-2 top-14 sm:static sm:flex flex-col sm:flex-row items-center gap-2 sm:gap-10 bg-white/95 sm:bg-transparent shadow-lg sm:shadow-none rounded-xl sm:rounded-none py-2 sm:py-0 mt-2 sm:mt-0 text-center text-base sm:text-lg font-semibold text-gray-800 transition-all duration-200 z-40" x-cloak>
                <a href="#about" class="block px-4 py-2 hover:text-green-700 hover:underline underline-offset-8 transition-all rounded focus:outline-none focus:ring-2 focus:ring-green-300">About</a>
                <a href="#contact" class="block px-4 py-2 hover:text-green-700 hover:underline underline-offset-8 transition-all rounded focus:outline-none focus:ring-2 focus:ring-green-300">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center w-full px-2 sm:px-4 pt-4 pb-4 relative">
        <!-- Modern Mobile-Friendly Background -->
        <div class="fixed inset-0 pointer-events-none select-none -z-10">
            <div class="absolute top-1/3 left-1/2 w-[340px] h-[340px] bg-green-200 rounded-full blur-3xl opacity-40 -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute top-10 left-4 w-32 h-32 bg-blue-100 rounded-full blur-2xl opacity-30"></div>
            <div class="absolute bottom-10 right-4 w-24 h-24 bg-green-100 rounded-full blur-2xl opacity-30"></div>
            <div class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-white rounded-full blur-3xl opacity-10 -translate-x-1/2 -translate-y-1/2"></div>
        </div>
        <div class="w-full flex flex-col items-center mt-2 sm:mt-4">
            <div class="w-full max-w-xs sm:max-w-md bg-gradient-to-br from-white via-green-50 to-green-100 rounded-2xl shadow-lg border border-green-100 p-4 sm:p-6 mt-2 sm:mt-4" style="backdrop-filter: blur(0.5px);">
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
    </footer>
    @include('components.toast')
    @include('components.spinner')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('scripts')
</body>
</html> 