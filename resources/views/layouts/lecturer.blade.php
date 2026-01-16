<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lecturer Portal') - NSUK Biometric Attendance</title>
    <meta name="description" content="Lecturer portal for managing classes, attendance, and student records">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Global Modals -->
    @include('components.modals')
    
    <!-- Additional Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .nav-link {
            transition: all 0.2s ease-in-out;
        }
        
        .nav-link:hover {
            transform: translateX(2px);
        }
        
        .dropdown-menu {
            transition: all 0.2s ease-in-out;
        }
        
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Responsive breakpoints */
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            .main-content {
                margin-left: 240px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    @include('components.toast')
    @if(session('success'))
        <script>window.addEventListener('DOMContentLoaded',function(){window.dispatchEvent(new CustomEvent('toast',{detail:{message:@json(session('success')),type:'success'}}));});</script>
    @endif
    @if(session('error'))
        <script>window.addEventListener('DOMContentLoaded',function(){window.dispatchEvent(new CustomEvent('toast',{detail:{message:@json(session('error')),type:'error'}}));});</script>
    @endif
    @if(session('info'))
        <script>window.addEventListener('DOMContentLoaded',function(){window.dispatchEvent(new CustomEvent('toast',{detail:{message:@json(session('info')),type:'info'}}));});</script>
    @endif
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-60 bg-green-600 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Close button for mobile -->
        <button id="closeSidebar" class="absolute top-3 right-3 p-2 rounded-full bg-white text-green-700 shadow-lg lg:hidden focus:outline-none focus:ring-2 focus:ring-green-500" aria-label="Close sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <!-- Logo Section -->
        <div class="flex flex-col items-center justify-center py-6 border-b border-green-700 flex-shrink-0">
            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center overflow-hidden shadow">
                <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-12 w-12 object-contain">
            </div>
            <div class="mt-2 text-sm text-white font-semibold text-center leading-tight px-2">
                Computer Science Dept<br>Biometric System
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1">
            <!-- Dashboard -->
            <a href="/lecturer/dashboard" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span>Dashboard</span>
            </a>
            <!-- My Courses -->
            <a href="{{ route('lecturer.courses.index') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group {{ request()->routeIs('lecturer.courses*') ? 'bg-green-800' : '' }}">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>My Courses</span>
            </a>
            <!-- Classes -->
            <a href="/lecturer/classes" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Manage Classes</span>
            </a>
            <!-- Attendance -->
            <a href="/lecturer/attendance" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>View Attendance</span>
            </a>
            <!-- Reports -->
            <a href="/lecturer/reports" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Generate Reports</span>
            </a>
            <!-- Grading -->
            <a href="{{ route('lecturer.grading.index') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group {{ request()->routeIs('lecturer.grading*') ? 'bg-green-800' : '' }}">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span>Attendance Grading</span>
            </a>
            <!-- Venues -->
            <a href="{{ route('lecturer.venues.index') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group {{ request()->routeIs('lecturer.venues*') ? 'bg-green-800' : '' }}">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Manage Venues</span>
            </a>
            <!-- Settings -->
            <a href="/lecturer/settings" class="nav-link flex items-center px-3 py-2 text-base font-medium text-white rounded-lg hover:bg-green-700 hover:text-white transition group">
                <svg class="w-5 h-5 mr-3 text-white group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Settings</span>
            </a>

            <hr class="my-4 border-green-300">
            
            <div class="px-1">
                <p class="text-sm font-bold text-white uppercase tracking-wider mb-2 px-2">Quick Actions</p>
                <div class="space-y-1">
                    <a href="#" onclick="event.preventDefault(); document.querySelector('[onclick*=\'openModal\']').click();" class="flex items-center text-white text-sm py-2 px-2 hover:bg-green-700 rounded-lg transition">
                        <svg class="w-4 h-4 mr-2 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Start Attendance
                    </a>
                    <a href="/lecturer/reports" class="flex items-center text-white text-sm py-2 px-2 hover:bg-green-700 rounded-lg transition">
                        <svg class="w-4 h-4 mr-2 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Data & Analytics
                    </a>
                </div>
            </div>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-green-700 flex-shrink-0 bg-green-600">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center shadow-sm">
                    <span class="text-white font-semibold text-xs" id="userInitials">JD</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate" id="userName">Dr. John Doe</p>
                    <p class="text-xs text-green-100 truncate" id="userDepartment">Computer Science</p>
                </div>
                <form method="POST" action="{{ route('lecturer.logout') }}">
                    @csrf
                    <button type="submit" class="text-green-100 hover:text-white p-1 rounded hover:bg-green-700 transition" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:pl-60 min-h-screen flex flex-col">
        <!-- Top Navigation Bar -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-20 px-3 sm:px-4 lg:px-6">
                <!-- Left side -->
                <div class="flex items-center">
                    <!-- Green accent bar -->
                    <div class="h-12 w-1.5 bg-green-600 rounded-r-lg mr-4"></div>
                    <!-- Mobile menu button -->
                    <button id="openSidebar" class="lg:hidden p-1.5 rounded-md text-gray-600 hover:text-green-700 hover:bg-green-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <!-- Page title -->
                    <div class="ml-2 lg:ml-0">
                        <h1 class="text-base sm:text-lg lg:text-xl font-bold text-gray-800 leading-tight">@yield('page-title', 'Dashboard')</h1>
                        <!-- Removed duplicate welcome greeting from header -->
                    </div>
                </div>
                <!-- Right side -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" placeholder="Search..." class="block w-32 sm:w-40 pl-6 pr-2 py-1.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 text-gray-700 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-xs">
                        </div>
                    </div>
                    <!-- Notifications -->
                    <button class="relative p-1.5 text-gray-600 hover:text-green-700 hover:bg-green-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M9 11h.01M9 8h.01M9 5h.01M9 2h.01"></path>
                        </svg>
                        <span class="absolute top-0.5 right-0.5 block h-1.5 w-1.5 rounded-full bg-red-400 notification-badge"></span>
                    </button>
                    <!-- Help -->
                    <button class="p-1.5 text-gray-600 hover:text-green-700 hover:bg-green-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <!-- Logout -->
                    <button onclick="logout()" class="p-1.5 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="flex-1">
            @yield('content')
        </main>
        <footer class="w-full bg-white border-t border-gray-200 py-4 px-4 text-center text-xs text-gray-500 mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} Computer Science Department, Nasarawa State University, Keffi. All rights reserved.</span>
                <span>Powered by NSUK Biometric Attendance System</span>
            </div>
        </footer>
    </div>

    <!-- Overlay for mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden hidden"></div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load user data from localStorage
            const lecturerData = JSON.parse(localStorage.getItem('lecturer') || '{}');
            
            if (lecturerData.name) {
                document.getElementById('userName').textContent = lecturerData.name;
                document.getElementById('userDepartment').textContent = lecturerData.department || 'Department';
                
                // Set user initials
                const initials = lecturerData.name.split(' ').map(n => n[0]).join('').toUpperCase();
                document.getElementById('userInitials').textContent = initials;
            }

            // Sidebar toggle for mobile
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openBtn = document.getElementById('openSidebar');
            const closeBtn = document.getElementById('closeSidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            openBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            // Close sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnOpenButton = openBtn.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnOpenButton && !sidebar.classList.contains('-translate-x-full')) {
                        closeSidebar();
                    }
                }
            });
        });

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                localStorage.removeItem('lecturer');
                window.location.href = '/lecturer';
            }
        }

        // Global flash message close function
        window.closeFlash = function(id) {
            const flash = document.getElementById(id);
            if (flash) {
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                flash.style.transition = 'opacity 0.3s, transform 0.3s';
                setTimeout(() => flash.remove(), 300);
            }
        };

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            ['flash-success', 'flash-error', 'flash-info', 'flash-warning'].forEach(id => {
                const flash = document.getElementById(id);
                if (flash) {
                    flash.style.opacity = '0';
                    flash.style.transform = 'translateY(-10px)';
                    flash.style.transition = 'opacity 0.3s, transform 0.3s';
                    setTimeout(() => flash.remove(), 300);
                }
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html> 