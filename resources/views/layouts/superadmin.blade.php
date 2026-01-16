<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Superadmin Portal') - NSUK Biometric Attendance</title>
    <meta name="description" content="Superadmin portal for managing all students, lecturers, classes, and attendance">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <!-- Local fonts -->
    <link href="/fonts/montserrat/montserrat.css" rel="stylesheet">
    <link href="/fonts/material-icons/material-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @include('components.modals')
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .sidebar-transition { transition: all 0.3s ease-in-out; }
        .nav-link { transition: all 0.2s ease-in-out; }
        .nav-link:hover { transform: translateX(2px); }
        .dropdown-menu { transition: all 0.2s ease-in-out; }
        .notification-badge { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        /* Hide scrollbar for sidebar */
        .sidebar-scroll {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        .sidebar-scroll::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        /* Hide section headers when collapsed */
        .collapsed-hidden { transition: opacity 0.2s; }
        @media (max-width: 1024px) { .sidebar { width: 200px; } .main-content { margin-left: 200px; } }
        @media (max-width: 768px) { .sidebar { width: 100%; transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } .main-content { margin-left: 0; } }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased" style="font-family: 'Montserrat', sans-serif;" 
      x-data="{ 
          sidebarOpen: false, 
          collapsed: false 
      }"
      x-init="
          sidebarOpen = window.innerWidth >= 768;
          collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
          
          // Handle window resize
          window.addEventListener('resize', () => {
              if (window.innerWidth < 768) {
                  sidebarOpen = false;
              } else {
                  sidebarOpen = true;
              }
          });
      ">
    <!-- Sidebar -->
    @include('components.superadmin-sidebar')
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 md:hidden"
         x-cloak>
    </div>

    <!-- Main content -->
    <div class="pt-16 transition-all duration-300 ease-in-out" 
         :class="{ 
             'ml-0': !sidebarOpen,
             'md:ml-16': sidebarOpen && collapsed, 
             'md:ml-64': sidebarOpen && !collapsed 
         }">
        @include('components.superadmin-navbar')
        <main class="py-6 px-4 sm:px-6 lg:px-8 min-h-screen">
            @yield('content')
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Account dropdown functionality
            const accountDropdownToggle = document.getElementById('accountDropdownToggle');
            const accountDropdownMenu = document.getElementById('accountDropdownMenu');
            
            if (accountDropdownToggle && accountDropdownMenu) {
                accountDropdownToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    accountDropdownMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!accountDropdownToggle.contains(event.target) && !accountDropdownMenu.contains(event.target)) {
                        accountDropdownMenu.classList.add('hidden');
                    }
                });
                
                // Close dropdown on escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        accountDropdownMenu.classList.add('hidden');
                    }
                });
            }
        });

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
    @yield('scripts')
</body>
</html>