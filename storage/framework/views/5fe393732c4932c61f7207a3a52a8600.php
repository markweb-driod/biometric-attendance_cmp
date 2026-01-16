<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'HOD Portal'); ?> - <?php echo e(config('app.name', 'Biometric Attendance')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Global Modals -->
    <?php echo $__env->make('components.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Logout Function -->
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                // For now, redirect to login page
                window.location.href = '/login';
            }
        }
    </script>
    
    <!-- Custom CSS for Enhanced Dashboard -->
    <style>
        /* Custom animations and enhancements */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5); }
            50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.8); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .glow-animation {
            animation: glow 2s ease-in-out infinite;
        }
        
        /* Enhanced hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Gradient text effect */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom scrollbar - Gray theme */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 5px;
            border: 1px solid #f3f4f6;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        ::-webkit-scrollbar-thumb:active {
            background: #6b7280;
        }
        
        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db #f3f4f6;
        }
        
        /* Scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Firefox sidebar scrollbar */
        .sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) rgba(0, 0, 0, 0.1);
        }
        
        /* Loading skeleton animation */
        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: calc(200px + 100%) 0; }
        }
        
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: shimmer 1.5s infinite;
        }
        
        /* Sidebar responsiveness */
        @media (max-width: 767px) {
            .sidebar-mobile {
                transform: translateX(-100%);
            }
            .sidebar-mobile.open {
                transform: translateX(0);
            }
        }
        
        /* Smooth transitions for all components */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Ensure proper z-index layering */
        .sidebar-overlay {
            z-index: 40;
        }
        
        .sidebar-content {
            z-index: 50;
        }
    </style>
    
</head>
<body class="font-sans antialiased bg-gray-50" style="font-family: 'Montserrat', sans-serif;" 
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
    <!-- Navbar -->
    <?php echo $__env->make('hod.components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
        <!-- Sidebar -->
    <?php echo $__env->make('hod.components.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Mobile Menu Button -->
    <?php echo $__env->make('hod.components.mobile-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
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
        <main class="py-6 px-4 sm:px-6 lg:px-8 min-h-screen">
            <!-- Debug info (remove in production) -->
            <div class="mb-4 p-2 bg-yellow-100 border border-yellow-300 rounded text-xs" x-show="false">
                <strong>Debug Info:</strong><br>
                Sidebar Open: <span x-text="sidebarOpen"></span><br>
                Collapsed: <span x-text="collapsed"></span><br>
                Screen Width: <span x-text="window.innerWidth"></span>
            </div>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    
    <script>
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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\layouts\app.blade.php ENDPATH**/ ?>