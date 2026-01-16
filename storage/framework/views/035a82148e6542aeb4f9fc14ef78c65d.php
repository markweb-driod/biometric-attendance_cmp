<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 bg-green-600 shadow-xl transform transition-all duration-300 ease-in-out flex flex-col sidebar-content" 
     :class="{ 
         '-translate-x-full': !sidebarOpen, 
         'translate-x-0': sidebarOpen,
         'w-16': collapsed && sidebarOpen,
         'w-64': !collapsed && sidebarOpen
     }">
    <!-- Logo Section -->
    <div class="flex items-center justify-between p-4 border-b border-green-700 flex-shrink-0">
        <div class="flex flex-col items-center justify-center w-full" x-show="!collapsed">
            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow mb-2">
                <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-10 w-10 object-contain">
            </div>
            <div class="text-xs text-white font-semibold text-center">
                HOD Portal<br>
                <span class="text-green-200">Computer Science</span>
            </div>
        </div>
        
        <!-- Collapsed Logo -->
        <div x-show="collapsed" class="flex justify-center w-full">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow">
                <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK" class="h-8 w-8 object-contain">
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-2 pt-4 sidebar-scroll">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="<?php echo e(route('hod.dashboard')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.dashboard') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span x-show="!collapsed">Dashboard</span>
            </a>

            <!-- Student Monitoring -->
            <a href="<?php echo e(route('hod.monitoring.students')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.monitoring.students*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <span x-show="!collapsed">Student Monitoring</span>
            </a>

            <!-- Course Monitoring -->
            <a href="<?php echo e(route('hod.monitoring.courses')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.monitoring.courses*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span x-show="!collapsed">Course Monitoring</span>
            </a>

            <!-- Student Management -->
            <a href="<?php echo e(route('hod.management.students.index')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.management.students*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span x-show="!collapsed">Manage Students</span>
            </a>

            <!-- Lecturer Management -->
            <a href="<?php echo e(route('hod.management.lecturers.index')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.management.lecturers*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span x-show="!collapsed">Manage Lecturers</span>
            </a>

            <!-- Course Assignment -->
            <a href="<?php echo e(route('hod.management.course-assignment.index')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.management.course-assignment*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span x-show="!collapsed">Course Assignment</span>
            </a>

            <!-- Exam Eligibility -->
            <a href="<?php echo e(route('hod.exam.eligibility')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.exam.eligibility') || request()->routeIs('hod.exam.api.eligibility*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="!collapsed">Exam Eligibility</span>
            </a>

            <!-- Audit Logs -->
            <a href="<?php echo e(route('hod.audit.index')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.audit*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="!collapsed">Audit Logs</span>
            </a>

            <!-- Eligibility Configuration -->
            <a href="<?php echo e(route('hod.exam.eligibility.configuration')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.exam.eligibility.configuration') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-show="!collapsed">Eligibility Config</span>
            </a>

            <!-- Profile -->
            <a href="<?php echo e(route('hod.profile')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.profile') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span x-show="!collapsed">Profile</span>
            </a>

            <!-- Settings -->
            <a href="<?php echo e(route('hod.settings')); ?>" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group <?php echo e(request()->routeIs('hod.settings*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700'); ?>" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                <span x-show="!collapsed">Settings</span>
            </a>
        </div>
    </nav>
    
    <!-- Footer User Info - Fixed at Bottom -->
    <div class="mt-auto border-t border-green-700 flex-shrink-0">
        <div class="p-3">
            <div class="flex items-center space-x-2" x-show="!collapsed">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-xs"><?php echo e(substr($hod->user->full_name ?? 'HOD', 0, 2)); ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate"><?php echo e($hod->user->full_name ?? 'HOD'); ?></p>
                    <p class="text-xs text-green-200 truncate">Head of Department</p>
                </div>
            </div>
            <div x-show="collapsed" class="flex justify-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-xs"><?php echo e(substr($hod->user->full_name ?? 'HOD', 0, 2)); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Logout Button -->
        <div class="px-3 pb-3">
            <form action="<?php echo e(route('hod.logout')); ?>" method="POST" class="w-full">
                <?php echo csrf_field(); ?>
                <button type="submit" 
                        class="nav-link w-full flex items-center px-3 py-2 text-base font-medium rounded-lg transition text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500" 
                        :class="collapsed ? 'justify-center' : 'justify-start'"
                        title="Logout">
                    <svg class="w-5 h-5 transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span x-show="!collapsed">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\components\sidebar.blade.php ENDPATH**/ ?>