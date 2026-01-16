<!-- Navbar (Superadmin) -->
<nav class="bg-white shadow-sm border-b border-gray-200 fixed top-0 z-40 transition-all duration-300 ease-in-out w-full"
     :class="{
         'left-0 right-0': !sidebarOpen,
         'md:left-16 md:right-0': sidebarOpen && collapsed,
         'md:left-64 md:right-0': sidebarOpen && !collapsed
     }">
    <div class="px-2 sm:px-4 md:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 min-w-0 gap-2">
            <!-- Left: mobile toggle + page title -->
            <div class="flex items-center flex-shrink-0 min-w-0">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Page title (with optional breadcrumbs later if needed) -->
                <div class="hidden md:block min-w-0 max-w-[220px] sm:max-w-xs ml-2">
                    <h1 class="text-lg font-semibold text-gray-900 truncate leading-tight"><?php echo $__env->yieldContent('page-title', 'Superadmin Dashboard'); ?></h1>
                    <p class="text-xs text-gray-500 truncate mt-0.5">Welcome back, <?php echo e(auth()->user()->full_name ?? 'Superadmin'); ?></p>
                </div>
            </div>

            <!-- Center: search -->
            <div class="flex-1 max-w-lg mx-2 sm:mx-4 hidden md:block min-w-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           placeholder="Search students, lecturers, classes..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm" />
                </div>
            </div>

            <!-- Right: actions + user menu -->
            <div class="flex items-center space-x-1 sm:space-x-2 md:space-x-4 flex-shrink-0">
                <!-- Quick actions (optional placeholders for parity with HOD) -->
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="/superadmin/students" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Students">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                    </a>
                    <a href="/superadmin/lecturers" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Lecturers">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                    <a href="/superadmin/reports" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition" title="Reports">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </a>
                </div>

                <!-- User menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 min-w-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs sm:text-sm font-semibold"><?php echo e(strtoupper(substr(auth()->user()->full_name ?? 'S', 0, 1))); ?></span>
                        </div>
                        <span class="ml-1 sm:ml-2 text-gray-700 font-medium hidden lg:block truncate max-w-[140px]"><?php echo e(auth()->user()->full_name ?? 'Superadmin'); ?></span>
                        <svg class="ml-0.5 sm:ml-1 h-3 w-3 sm:h-4 sm:w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-44 sm:w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                         style="max-width: calc(100vw - 2rem);">
                        <div class="py-1">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900"><?php echo e(auth()->user()->full_name ?? 'Superadmin'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e(auth()->user()->email ?? 'superadmin@nsuk.edu.ng'); ?></p>
                            </div>
                            <a href="/superadmin/profile"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile
                            </a>
                            <a href="/superadmin/system-settings"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                Settings
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <form action="<?php echo e(route('superadmin.logout')); ?>" method="POST" class="w-full">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>


<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\components\superadmin-navbar.blade.php ENDPATH**/ ?>