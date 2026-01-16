@extends('layouts.lecturer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Attendance Grading</h1>
        <p class="text-gray-600 mt-2">Select a classroom to view and configure attendance grading</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classrooms as $classroom)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $classroom->class_name }}</h3>
                <p class="text-gray-600 mb-4">{{ $classroom->course->course_name ?? 'N/A' }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $classroom->students->count() }} students</span>
                    <a href="{{ route('lecturer.grading.show', $classroom->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        View Grading
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No active classrooms found</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
