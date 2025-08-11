<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Superadmin Portal') - NSUK Biometric Attendance</title>
    <meta name="description" content="Superadmin portal for managing all students, lecturers, classes, and attendance">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .sidebar-transition { transition: all 0.3s ease-in-out; }
        .nav-link { transition: all 0.2s ease-in-out; }
        .nav-link:hover { transform: translateX(2px); }
        .dropdown-menu { transition: all 0.2s ease-in-out; }
        .notification-badge { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        @media (max-width: 1024px) { .sidebar { width: 200px; } .main-content { margin-left: 200px; } }
        @media (max-width: 768px) { .sidebar { width: 100%; transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } .main-content { margin-left: 0; } }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-60 bg-green-700 text-white shadow-lg flex flex-col">
        <div class="flex items-center justify-center h-20 px-4 border-b border-green-800 bg-green-800">
            <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="Logo" class="w-12 h-12 rounded-full mr-3">
            <div>
                <div class="text-lg font-bold leading-tight">Computer Science Dept</div>
                <div class="text-xs font-semibold tracking-wide">Biometric System</div>
            </div>
        </div>
        <div class="mt-6 space-y-1 flex-1 px-2">
            <a href="/superadmin/dashboard" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/dashboard')) bg-green-800 @endif">
                <span class="material-icons mr-3">dashboard</span> Dashboard
            </a>
            <a href="/superadmin/students" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/students')) bg-green-800 @endif">
                <span class="material-icons mr-3">people</span> Students
            </a>
            <a href="/superadmin/students/face-registration-management" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/students/face-registration-management')) bg-green-800 @endif">
                <span class="material-icons mr-3">face</span> Face Registration
            </a>
            <a href="/superadmin/lecturers" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/lecturers')) bg-green-800 @endif">
                <span class="material-icons mr-3">person</span> Lecturers
            </a>
            <a href="/superadmin/classes" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/classes')) bg-green-800 @endif">
                <span class="material-icons mr-3">class</span> Classes
            </a>
            <a href="/superadmin/attendance" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/attendance')) bg-green-800 @endif">
                <span class="material-icons mr-3">assignment</span> Attendance
            </a>
            <hr class="my-3 border-green-600 opacity-30">
            <a href="/superadmin/reports" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/reports')) bg-green-800 @endif">
                <span class="material-icons mr-3">bar_chart</span> Reports
            </a>
            <a href="/superadmin/settings" class="nav-link flex items-center px-3 py-2 text-base font-semibold rounded-lg hover:bg-green-600 hover:text-white transition @if(request()->is('superadmin/settings')) bg-green-800 @endif">
                <span class="material-icons mr-3">settings</span> Settings
            </a>
        </div>
        <div class="mt-auto p-4 border-t border-green-800 bg-green-800">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center text-white font-bold">SA</div>
                <div>
                    <div class="text-base font-semibold">Superadmin</div>
                    <div class="text-xs text-green-100">NSUK</div>
                </div>
            </div>
        </div>
    </nav>
    <div class="flex-1 flex flex-col min-h-screen main-content lg:ml-60">
        <header class="bg-white shadow sticky top-0 z-40">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button id="openSidebar" class="lg:hidden p-2 rounded-md text-green-700 hover:text-green-900 hover:bg-green-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <span class="text-2xl font-bold text-green-800 tracking-tight">@yield('page-title', 'Superadmin Dashboard')</span>
                </div>
                <div class="flex items-center gap-4">
                    <input type="text" placeholder="Search..." class="px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base bg-gray-50" />
                    <div class="relative group">
                        <button class="flex items-center gap-1 px-3 py-1 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="hidden sm:inline">Account</span>
                        </button>
                        <div class="absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-lg shadow-lg py-2 opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-opacity z-50">
                            <a href="/superadmin/profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="/superadmin/logout" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="pt-4 flex-1">
            @yield('content')
        </main>
    </div>
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden hidden"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openBtn = document.getElementById('openSidebar');
            const closeBtn = document.getElementById('closeSidebar');
            function openSidebar() { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
            function closeSidebar() { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
            if(openBtn) openBtn.addEventListener('click', openSidebar);
            if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
            window.addEventListener('resize', function() { if (window.innerWidth >= 1024) { closeSidebar(); } });
            document.addEventListener('click', function(event) { if (window.innerWidth < 1024) { const isClickInsideSidebar = sidebar.contains(event.target); const isClickOnOpenButton = openBtn && openBtn.contains(event.target); if (!isClickInsideSidebar && !isClickOnOpenButton && !sidebar.classList.contains('-translate-x-full')) { closeSidebar(); } } });
        });
        function logout() { if (confirm('Are you sure you want to logout?')) { localStorage.removeItem('superadmin'); window.location.href = '/superadmin'; } }
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html> 