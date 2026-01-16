@extends('hod.layouts.app')

@section('title', 'HOD Dashboard')

@section('content')
<!-- Background Elements -->
<div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-green-50 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-50 rounded-full blur-3xl opacity-50 translate-y-1/3 -translate-x-1/4"></div>
</div>

<!-- Welcome Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-green-700 to-emerald-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Decorational Circles -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white opacity-10 rounded-full translate-y-1/3 -translate-x-1/4"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold tracking-wide border border-white/20">
                        {{ \Carbon\Carbon::now()->format('l, jS F Y') }}
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">
                    Welcome back, {{ explode(' ', Auth::guard('hod')->user()->user->name ?? 'HOD')[0] }}! 👋
                </h1>
                <p class="text-green-100 text-lg opacity-90 max-w-2xl">
                    Here's what's happening in your department today. You have <span class="font-bold text-white">{{ $atRiskStudents ?? 0 }} items</span> requiring attention.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('hod.monitoring.students') }}" class="px-6 py-3 bg-white text-green-700 font-bold rounded-xl shadow-lg hover:shadow-xl hover:bg-green-50 transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                    View Students
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Students -->
    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-green-100 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-blue-100/50 rounded-xl group-hover:bg-blue-100 transition-colors">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Active
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Total Students</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1" data-stat="students">{{ $totalStudents ?? 0 }}</h3>
        </div>
    </div>

    <!-- Total Lecturers -->
    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-green-100 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-purple-100/50 rounded-xl group-hover:bg-purple-100 transition-colors">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Total Lecturers</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1" data-stat="lecturers">{{ $totalLecturers ?? 0 }}</h3>
        </div>
    </div>

    <!-- Active Courses -->
    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-green-100 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-orange-100/50 rounded-xl group-hover:bg-orange-100 transition-colors">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="flex items-center text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">
                Ongoing
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Active Courses</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1" data-stat="courses">{{ $activeCourses ?? 0 }}</h3>
        </div>
    </div>

    <!-- Avg Attendance -->
    <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white hover:border-green-100 transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="p-3 bg-emerald-100/50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            @php
                $avgTrend = $averageAttendance >= 75 ? 'text-green-600 bg-green-50' : ($averageAttendance >= 50 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50');
            @endphp
            <span class="flex items-center text-xs font-medium px-2 py-1 rounded-lg {{ $avgTrend }}">
                {{ number_format($averageAttendance ?? 0, 1) }}%
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-medium">Avg Attendance</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1" data-stat="attendance">{{ number_format($averageAttendance ?? 0, 1) }}%</h3>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Quick Actions & At Risk (span 2) -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('hod.management.course-assignment.index') }}" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition-all group">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mb-3 group-hover:bg-green-100 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Course Assignment</span>
            </a>

            <a href="{{ route('hod.monitoring.courses') }}" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition-all group">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Monitor Courses</span>
            </a>

            <a href="{{ route('hod.report.attendance') }}" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition-all group">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mb-3 group-hover:bg-purple-100 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Reports</span>
            </a>

            <a href="{{ route('hod.settings.general') }}" class="flex flex-col items-center justify-center p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-300 transition-all group">
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-3 group-hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 text-center">Settings</span>
            </a>
        </div>

        <!-- At Risk Students Alert -->
        @if(($atRiskStudents ?? 0) > 0)
        <div class="bg-red-50 rounded-2xl p-6 border border-red-100 flex items-start gap-4">
            <div class="p-3 bg-red-100 text-red-600 rounded-full flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-grow">
                <h3 class="text-lg font-bold text-red-800 mb-1">Attention Required</h3>
                <p class="text-red-600 mb-3">
                    <span class="font-bold">{{ $atRiskStudents }} students</span> in your department have fallen below the 75% attendance threshold.
                </p>
                <a href="{{ route('hod.monitoring.students', ['filter' => 'risk']) }}" class="inline-flex items-center text-sm font-semibold text-red-700 hover:text-red-800 hover:underline">
                    View at-risk list <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
        @endif

    </div>

    <!-- Right Column: Recent Activity (span 1) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden h-full">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Recent Activity</h3>
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
            </div>
            
            <div class="p-0 overflow-y-auto max-h-[600px] custom-scrollbar" id="activityFeed">
                @forelse($recentActivities ?? [] as $activity)
                    <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $activity['status'] === 'completed' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-gray-800 text-sm">{{ $activity['course'] }}</h5>
                                <p class="text-xs text-gray-500 mb-1">{{ $activity['lecturer'] }} &bull; {{ $activity['time'] }}</p>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $activity['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $activity['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">No recent activity found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Simple Auto-Refresh for stats
    setInterval(() => {
        fetch('{{ route("hod.api.dashboard-stats") }}')
            .then(res => res.json())
            .then(data => {
                if(data.success && data.data) {
                    const stats = data.data;
                    document.querySelector('[data-stat="students"]').innerText = stats.overview.total_students;
                    document.querySelector('[data-stat="lecturers"]').innerText = stats.overview.total_lecturers;
                    document.querySelector('[data-stat="courses"]').innerText = stats.overview.total_classes;
                    document.querySelector('[data-stat="attendance"]').innerText = stats.overview.average_attendance.toFixed(1) + '%';
                }
            })
            .catch(err => console.error('Stats refresh failed:', err));
    }, 30000); // 30 seconds
</script>
@endpush