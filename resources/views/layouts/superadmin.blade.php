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
    <nav id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-56 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        <div class="flex items-center justify-between h-14 px-4 border-b border-gray-200">
            <a href="/superadmin/dashboard" class="flex items-center gap-2">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" /></svg>
                <span class="text-lg font-bold text-gray-900 tracking-tight">NSUK Biometric</span>
            </a>
            <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <nav class="flex-1 mt-4 px-2 space-y-1">
            <a href="/superadmin/dashboard" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path></svg>Dashboard</a>
            <a href="/superadmin/students" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>Students</a>
            <a href="/superadmin/students/face-registration-management" class="nav-link flex items-center px-3 py-2 text-sm text-green-700 rounded-lg hover:bg-green-50 hover:text-green-700 group font-bold"><svg class="w-5 h-5 mr-2 text-green-500 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Student Face Registration</a>
            <a href="/superadmin/lecturers" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>Lecturers</a>
            <a href="/superadmin/classes" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>Classes</a>
            <a href="/superadmin/attendance" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>Attendance</a>
            <a href="/superadmin/reports" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Reports</a>
            <a href="/superadmin/settings" class="nav-link flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-700 group"><svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>Settings</a>
        </nav>
        <div class="mt-auto p-4 border-t border-gray-200">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center text-white font-bold">SA</div>
                <div>
                    <div class="text-sm font-semibold text-gray-900">Superadmin</div>
                    <div class="text-xs text-gray-500">NSUK</div>
                </div>
            </div>
        </div>
    </nav>
    <div class="flex-1 flex flex-col min-h-screen main-content lg:ml-56">
        <header class="bg-white shadow sticky top-0 z-40">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <button id="openSidebar" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <a href="/superadmin/dashboard" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2" /></svg>
                        <span class="text-2xl font-bold text-gray-900 tracking-tight">NSUK Biometric</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-700 font-medium">Superadmin</span>
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
            openBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
            window.addEventListener('resize', function() { if (window.innerWidth >= 1024) { closeSidebar(); } });
            document.addEventListener('click', function(event) { if (window.innerWidth < 1024) { const isClickInsideSidebar = sidebar.contains(event.target); const isClickOnOpenButton = openBtn.contains(event.target); if (!isClickInsideSidebar && !isClickOnOpenButton && !sidebar.classList.contains('-translate-x-full')) { closeSidebar(); } } });
        });
        function logout() { if (confirm('Are you sure you want to logout?')) { localStorage.removeItem('superadmin'); window.location.href = '/superadmin'; } }
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html> 