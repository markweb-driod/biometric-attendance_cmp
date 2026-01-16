<!-- Navbar -->
<nav class="bg-white shadow-sm border-b border-gray-200 fixed top-0 z-40 transition-all duration-300 ease-in-out w-full" 
     :class="{ 
         'left-0 right-0': !sidebarOpen,
         'md:left-16 md:right-0': sidebarOpen && collapsed, 
         'md:left-64 md:right-0': sidebarOpen && !collapsed 
     }">
    <div class="px-2 sm:px-4 md:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 min-w-0 gap-2">
            <!-- Left side -->
            <div class="flex items-center flex-shrink-0 min-w-0">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Page title with breadcrumbs (only show on desktop when sidebar is visible) -->
                <div class="hidden md:block min-w-0 max-w-[200px] sm:max-w-xs">
                    <?php
                        $pageTitle = 'Dashboard';
                        $breadcrumbs = [];
                        
                        if (request()->routeIs('hod.dashboard')) {
                            $pageTitle = 'Dashboard';
                            $breadcrumbs = ['Dashboard'];
                        } elseif (request()->routeIs('hod.monitoring.students*')) {
                            $pageTitle = 'Student Monitoring';
                            $breadcrumbs = ['Monitoring', 'Student Monitoring'];
                        } elseif (request()->routeIs('hod.monitoring.courses*')) {
                            $pageTitle = 'Course Monitoring';
                            $breadcrumbs = ['Monitoring', 'Course Monitoring'];
                        } elseif (request()->routeIs('hod.management.students*')) {
                            $pageTitle = 'Student Management';
                            $breadcrumbs = ['Management', 'Students'];
                        } elseif (request()->routeIs('hod.management.lecturers*')) {
                            $pageTitle = 'Lecturer Management';
                            $breadcrumbs = ['Management', 'Lecturers'];
                        } elseif (request()->routeIs('hod.exam.eligibility*')) {
                            $pageTitle = 'Exam Eligibility';
                            $breadcrumbs = ['Exam', 'Eligibility'];
                        } elseif (request()->routeIs('hod.audit*')) {
                            $pageTitle = 'Audit Logs';
                            $breadcrumbs = ['Audit', 'Logs'];
                        } elseif (request()->routeIs('hod.profile')) {
                            $pageTitle = 'Profile';
                            $breadcrumbs = ['Profile'];
                        } elseif (request()->routeIs('hod.settings*')) {
                            $pageTitle = 'Settings';
                            $breadcrumbs = ['Settings'];
                        }
                    ?>
                    <?php if(!empty($breadcrumbs)): ?>
                    <div class="flex items-center space-x-1 text-xs text-gray-500 mb-0.5 truncate">
                        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="truncate"><?php echo e($crumb); ?></span>
                            <?php if(!$loop->last): ?>
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                    <h1 class="text-lg font-semibold text-gray-900 truncate leading-tight"><?php echo e($pageTitle); ?></h1>
                    <p class="text-xs text-gray-500 truncate mt-0.5">Welcome back, <?php echo e($hod->user->full_name ?? 'HOD'); ?></p>
                </div>
            </div>

            <!-- Center - Search -->
            <div class="flex-1 max-w-lg mx-2 sm:mx-4 hidden md:block min-w-0" x-data="{ 
                searchQuery: '',
                showResults: false,
                results: [],
                loading: false
            }">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.300ms="
                               if (searchQuery.length > 2) {
                                   loading = true;
                                   fetch('<?php echo e(route('hod.search')); ?>?q=' + encodeURIComponent(searchQuery))
                                       .then(response => response.json())
                                       .then(data => {
                                           results = data.results || [];
                                           showResults = true;
                                           loading = false;
                                       })
                                       .catch(() => {
                                           loading = false;
                                           showResults = false;
                                       });
                               } else {
                                   showResults = false;
                                   results = [];
                               }
                           "
                           @focus="if (results.length > 0) showResults = true"
                           @blur="setTimeout(() => showResults = false, 200)"
                           placeholder="Search students, courses, lecturers..." 
                           class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="showResults && results.length > 0" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 max-h-96 overflow-auto">
                        <div class="py-1">
                            <template x-for="(result, index) in results" :key="index">
                                <a :href="result.url" 
                                   class="flex items-center px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                             :class="{
                                                 'bg-blue-100 text-blue-600': result.type === 'student',
                                                 'bg-green-100 text-green-600': result.type === 'course',
                                                 'bg-purple-100 text-purple-600': result.type === 'lecturer'
                                             }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="result.type === 'student'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="result.type === 'course'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="result.type === 'lecturer'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="result.title"></p>
                                        <p class="text-xs text-gray-500 truncate" x-text="result.subtitle"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-1 sm:space-x-2 md:space-x-4 flex-shrink-0">
                <!-- Quick Actions -->
                <div class="hidden lg:flex items-center space-x-2">
                    <a href="<?php echo e(route('hod.monitoring.students')); ?>" 
                       class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition" 
                       title="Student Monitoring">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </a>
                    <a href="<?php echo e(route('hod.exam.eligibility')); ?>" 
                       class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition"
                       title="Exam Eligibility">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                    <a href="<?php echo e(route('hod.audit.index')); ?>" 
                       class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition"
                       title="Audit Logs">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Notifications -->
                <div class="relative" 
                     x-data="{
                         open: false,
                         notifications: [],
                         unreadCount: 0,
                         loading: false,
                         notificationsUrl: '<?php echo e(route('hod.api.notifications')); ?>',
                         readUrl: '<?php echo e(route('hod.api.notifications.read')); ?>',
                         readAllUrl: '<?php echo e(route('hod.api.notifications.read-all')); ?>',
                         loadNotifications() {
                             this.loading = true;
                             fetch(this.notificationsUrl)
                                 .then(response => response.json())
                                 .then(data => {
                                     this.notifications = data.notifications || [];
                                     this.unreadCount = data.unread_count || 0;
                                     this.loading = false;
                                 })
                                 .catch(() => {
                                     this.loading = false;
                                 });
                         },
                         markAsRead(notificationId) {
                             const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
                             if (!token) return;
                             fetch(this.readUrl, {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': token
                                 },
                                 body: JSON.stringify({ id: notificationId })
                             })
                             .then(() => {
                                 this.loadNotifications();
                             })
                             .catch(() => {
                                 console.error('Failed to mark notification as read');
                             });
                         },
                         markAllAsRead() {
                             const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
                             if (!token) return;
                             fetch(this.readAllUrl, {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': token
                                 }
                             })
                             .then(() => {
                                 this.loadNotifications();
                             })
                             .catch(() => {
                                 console.error('Failed to mark all as read');
                             });
                         }
                     }"
                     x-init="loadNotifications(); setInterval(() => loadNotifications(), 30000)"
                     x-cloak>
                    <button @click="open = !open; if(open && notifications.length === 0) loadNotifications()" 
                            class="p-1.5 sm:p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 relative">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span x-show="unreadCount > 0" 
                              class="absolute top-0 right-0 block h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center ring-2 ring-white" 
                              x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </button>
                    
                    <!-- Notifications dropdown -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                                <button @click="markAllAsRead(); notifications.forEach(n => n.read = true); unreadCount = 0" 
                                        class="text-xs text-green-600 hover:text-green-700 font-medium" 
                                        x-show="unreadCount > 0">
                                    Mark all as read
                                </button>
                            </div>
                                
                            <div x-show="loading" class="py-4 text-center">
                                <svg class="animate-spin h-5 w-5 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                            
                            <div x-show="!loading && notifications.length === 0" class="py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No notifications</p>
                                </div>
                                
                            <div x-show="!loading && notifications.length > 0" class="space-y-2 max-h-96 overflow-y-auto">
                                <template x-for="(notification, index) in notifications" :key="index">
                                    <div @click="markAsRead(notification.id); notification.read = true; unreadCount = Math.max(0, unreadCount - 1)" 
                                         class="flex items-start space-x-3 p-3 rounded-lg cursor-pointer transition"
                                         :class="{
                                             'bg-red-50': notification.type === 'alert',
                                             'bg-yellow-50': notification.type === 'warning',
                                             'bg-green-50': notification.type === 'info',
                                             'bg-gray-50': notification.read
                                         }">
                                    <div class="flex-shrink-0">
                                            <svg class="h-5 w-5" 
                                                 :class="{
                                                     'text-red-400': notification.type === 'alert',
                                                     'text-yellow-400': notification.type === 'warning',
                                                     'text-green-400': notification.type === 'info',
                                                     'text-gray-400': notification.read
                                                 }"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" 
                                                      x-show="notification.type === 'alert'"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" 
                                                      x-show="notification.type === 'warning'"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" 
                                                      x-show="notification.type === 'info'"></path>
                                        </svg>
                                    </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                            <p class="text-sm text-gray-500" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="notification.time_ago"></p>
                                        </div>
                                        <span x-show="!notification.read" class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-200" x-show="!loading && notifications.length > 0">
                                <a href="<?php echo e(route('hod.audit.index')); ?>" class="text-sm text-green-600 hover:text-green-500 font-medium block text-center">View all notifications</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 min-w-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-xs sm:text-sm font-semibold"><?php echo e(substr($hod->user->full_name ?? 'HOD', 0, 1)); ?></span>
                        </div>
                        <span class="ml-1 sm:ml-2 text-gray-700 font-medium hidden lg:block truncate max-w-[120px]"><?php echo e($hod->user->full_name ?? 'HOD'); ?></span>
                        <svg class="ml-0.5 sm:ml-1 h-3 w-3 sm:h-4 sm:w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- User dropdown -->
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
                                <p class="text-sm font-medium text-gray-900"><?php echo e($hod->user->full_name ?? 'HOD'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($hod->user->email ?? 'hod@nsuk.edu.ng'); ?></p>
                            </div>
                            
                            <a href="<?php echo e(route('hod.profile')); ?>" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile
                            </a>
                            
                            <a href="<?php echo e(route('hod.dashboard')); ?>" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                            </a>
                            
                            <div class="border-t border-gray-100"></div>
                            
                            <form action="<?php echo e(route('hod.logout')); ?>" method="POST" class="w-full">
                                <?php echo csrf_field(); ?>
                                <button type="submit" 
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
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
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\components\navbar.blade.php ENDPATH**/ ?>