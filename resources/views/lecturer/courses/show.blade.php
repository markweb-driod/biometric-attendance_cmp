@extends('layouts.lecturer')

@section('title', $course->course_name)
@section('page-title', $course->course_code . ' - ' . $course->course_name)
@section('page-description', 'Course details and classroom management')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span>{{ session('success') }}</span>
    <button onclick="closeFlash('flash-success')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

@if(session('error'))
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>{{ session('error') }}</span>
    <button onclick="closeFlash('flash-error')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('lecturer.courses.index') }}" class="text-green-600 hover:text-green-800 mb-2 inline-flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Courses
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $course->course_code }} - {{ $course->course_name }}</h1>
        <p class="text-gray-500 mt-1">{{ $course->description ?? 'No description available' }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 mb-1">Total Classrooms</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_classrooms'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 mb-1">Active Classrooms</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_classrooms'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 mb-1">Total Students</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-orange-500">
            <p class="text-sm text-gray-600 mb-1">Total Attendances</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_attendances'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Course Info -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Course Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Course Code</p>
                <p class="text-lg font-medium text-gray-900">{{ $course->course_code }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Course Name</p>
                <p class="text-lg font-medium text-gray-900">{{ $course->course_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Department(s)</p>
                <p class="text-lg font-medium text-gray-900">{{ ($course->departments && $course->departments->count()) ? $course->departments->pluck('name')->join(', ') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Academic Level</p>
                <p class="text-lg font-medium text-gray-900">{{ $course->academicLevel->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Credit Units</p>
                <p class="text-lg font-medium text-gray-900">{{ $course->credit_units }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Semester</p>
                <p class="text-lg font-medium text-gray-900">{{ $course->semester->name ?? 'N/A' }}</p>
            </div>
        </div>
        @if($course->description)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2">Description</p>
            <p class="text-gray-700">{{ $course->description }}</p>
        </div>
        @endif
    </div>

    <!-- Classrooms -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h2 class="text-xl font-semibold text-white">Classrooms</h2>
        </div>
        <div class="p-6">
            @if($classrooms->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PIN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($classrooms as $classroom)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $classroom->class_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono">{{ $classroom->pin }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $classroom->schedule ?? 'Not set' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $classroom->students_count ?? 0 }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($classroom->is_active)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('lecturer.class.detail', $classroom->id) }}" class="text-green-600 hover:text-green-900">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="text-gray-500">No classrooms created for this course yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

