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
                Superadmin Portal<br>
                <span class="text-green-200">NSUK Biometric</span>
            </div>
        </div>
        
        <!-- Collapsed Logo -->
        <div x-show="collapsed" class="flex justify-center w-full">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow">
                <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK" class="h-8 w-8 object-contain">
            </div>
        </div>
        
        <!-- Collapse Toggle Button -->
        <button @click="collapsed = !collapsed; localStorage.setItem('sidebarCollapsed', collapsed)" 
                class="absolute top-4 right-4 lg:relative lg:top-0 lg:right-0 text-white hover:text-gray-200 transition-colors p-1 rounded z-10"
                title="Toggle Sidebar"
                x-show="sidebarOpen">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!collapsed">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="collapsed" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-2 pt-4 sidebar-scroll">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="/superadmin/dashboard" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/dashboard') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span x-show="!collapsed">Dashboard</span>
            </a>

            <!-- Section: User Management -->
            <div class="mt-4 mb-2 px-3" x-show="!collapsed">
                <p class="text-xs uppercase tracking-wider text-green-200 font-semibold">User Management</p>
            </div>

            <!-- Students -->
            <a href="/superadmin/students" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/students') && !request()->is('superadmin/students/*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <span x-show="!collapsed">Students</span>
            </a>

            <!-- Face Registration -->
            <a href="/superadmin/students/face-registration-management" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/students/face-registration-management') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="!collapsed">Face Registration</span>
            </a>

            <!-- Lecturers -->
            <a href="/superadmin/lecturers" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/lecturers') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span x-show="!collapsed">Lecturers</span>
            </a>

            

            <!-- Section: Academic -->
            <div class="mt-4 mb-2 px-3" x-show="!collapsed">
                <p class="text-xs uppercase tracking-wider text-green-200 font-semibold">Academic</p>
            </div>

            <!-- Classes -->
            <a href="/superadmin/classes" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/classes') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span x-show="!collapsed">Classes</span>
            </a>

            <!-- Course Assignment -->
            <a href="{{ route('superadmin.course-assignment.index') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/course-assignment*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span x-show="!collapsed">Course Assignment</span>
            </a>

            <!-- Academic Structure -->
            <a href="/superadmin/academic-structure" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/academic-structure*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span x-show="!collapsed">Academic Structure</span>
            </a>

            <!-- Attendance -->
            <a href="/superadmin/attendance" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/attendance') && !request()->is('superadmin/attendance-audit*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span x-show="!collapsed">Attendance</span>
            </a>

            <!-- Attendance Audit -->
            <a href="{{ route('superadmin.attendance.audit') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/attendance-audit*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="!collapsed">Attendance Audit</span>
            </a>

            <!-- Section: System -->
            <div class="mt-4 mb-2 px-3" x-show="!collapsed">
                <p class="text-xs uppercase tracking-wider text-green-200 font-semibold">System</p>
            </div>

            <!-- Reporting -->
            <a href="{{ route('superadmin.reporting') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/reporting*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="!collapsed">Reporting</span>
            </a>

            <!-- Reports -->
            <a href="/superadmin/reports" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/reports') && !request()->is('superadmin/reporting*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span x-show="!collapsed">Reports</span>
            </a>

            <!-- Audit Trail -->
            <a href="{{ route('superadmin.audit-trail') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/audit-trail*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span x-show="!collapsed">Audit Trail</span>
            </a>

            <!-- Audit Logs -->
            <a href="/superadmin/audit-logs" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/audit-logs*') && !request()->is('superadmin/audit-trail*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span x-show="!collapsed">Audit Logs</span>
            </a>

            <!-- Session Monitoring -->
            <a href="{{ route('superadmin.session-monitoring') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/session-monitoring*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="!collapsed">Session Monitoring</span>
            </a>

            <!-- Exam Eligibility API Management -->
            <a href="{{ route('superadmin.api-keys.index') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/api-keys*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span x-show="!collapsed">Exam Eligibility API</span>
            </a>

            <!-- System Settings -->
            <a href="{{ route('superadmin.settings') }}" class="nav-link flex items-center px-3 py-2 text-base font-medium rounded-lg transition group {{ request()->is('superadmin/settings*') ? 'bg-green-700 text-white shadow-md' : 'text-white hover:bg-green-700' }}" :class="collapsed ? 'justify-center' : 'justify-start'">
                <svg class="w-5 h-5 text-white transition" :class="collapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-show="!collapsed">System Settings</span>
            </a>

        </div>
    </nav>
    
    <!-- Footer User Info - Fixed at Bottom -->
    <div class="mt-auto border-t border-green-700 flex-shrink-0">
        <div class="p-3">
            <div class="flex items-center space-x-2" x-show="!collapsed">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-xs">SA</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">Superadmin</p>
                    <p class="text-xs text-green-200 truncate">NSUK System</p>
                </div>
            </div>
            <div x-show="collapsed" class="flex justify-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-xs">SA</span>
                </div>
            </div>
        </div>
        
        <!-- Logout Button -->
        <div class="px-3 pb-3">
            <form action="{{ route('superadmin.logout') }}" method="POST" class="w-full">
                @csrf
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

