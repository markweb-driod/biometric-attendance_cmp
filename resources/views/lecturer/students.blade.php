@extends('layouts.lecturer')

@section('title', 'My Students')
@section('page-title', 'My Students')
@section('page-description', 'View and manage students enrolled in your courses')

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

<div class="max-w-7xl w-full mx-auto px-2 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Students</h1>
                <p class="text-gray-600 mt-2">Students enrolled in courses you manage</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-2xl font-bold text-green-600">{{ $students->count() }}</div>
                    <div class="text-sm text-gray-500">Total Students</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600">{{ count($studentsByCourse) }}</div>
                    <div class="text-sm text-gray-500">Courses</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($courseStats as $courseName => $stats)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $courseName }}</h3>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">{{ $stats['course_code'] }}</span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Students:</span>
                    <span class="font-semibold text-gray-900">{{ $stats['total_students'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Class:</span>
                    <span class="text-sm text-gray-700">{{ $stats['class_name'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search Students</label>
                <input type="text" id="search-input" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Search by name, matric number, or email...">
            </div>
            <div class="sm:w-48">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Course</label>
                <select id="course-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Courses</option>
                    @foreach($courseStats as $courseName => $stats)
                    <option value="{{ $courseName }}">{{ $courseName }} ({{ $stats['course_code'] }})</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-48">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Level</label>
                <select id="level-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Levels</option>
                    @foreach($students->pluck('academicLevel.name')->unique()->filter() as $level)
                    <option value="{{ $level }}">{{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Student Directory</h2>
            <div class="text-sm text-gray-500">
                Showing <span id="showing-count">{{ $students->count() }}</span> of {{ $students->count() }} students
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matric Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="students-table" class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                    <tr class="student-row" data-course="{{ $student->classrooms->first()->course->course_name ?? 'Unknown' }}" data-level="{{ $student->academicLevel->name ?? '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-green-800">{{ substr($student->user->full_name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">{{ $student->matric_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $student->classrooms->first()->course->course_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $student->classrooms->first()->course->course_code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">{{ $student->academicLevel->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->department->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewStudentDetails({{ $student->id }})" class="text-green-600 hover:text-green-900 mr-3">View Details</button>
                            <button onclick="viewStudentAttendance({{ $student->id }})" class="text-blue-600 hover:text-blue-900">Attendance</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4" id="mobile-students">
            @foreach($students as $student)
            <div class="student-card bg-gray-50 rounded-lg p-4 border border-gray-200" data-course="{{ $student->classrooms->first()->course->course_name ?? 'Unknown' }}" data-level="{{ $student->academicLevel->name ?? '' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <span class="text-sm font-medium text-green-800">{{ substr($student->user->full_name, 0, 2) }}</span>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $student->user->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $student->matric_number }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ $student->classrooms->first()->course->course_name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ $student->academicLevel->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">{{ $student->department->name ?? 'N/A' }}</div>
                    <div class="space-x-2">
                        <button onclick="viewStudentDetails({{ $student->id }})" class="text-green-600 hover:text-green-900 text-sm">Details</button>
                        <button onclick="viewStudentAttendance({{ $student->id }})" class="text-blue-600 hover:text-blue-900 text-sm">Attendance</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($students->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
            <p class="text-gray-500">You don't have any students enrolled in your courses yet.</p>
        </div>
        @endif
    </div>
</div>

<!-- Student Details Modal -->
<div id="student-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Student Details</h3>
                <button onclick="closeStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="student-details-content">
                <!-- Student details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allStudents = @json($students);

function filterStudents() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const courseFilter = document.getElementById('course-filter').value;
    const levelFilter = document.getElementById('level-filter').value;
    
    const rows = document.querySelectorAll('.student-row, .student-card');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const course = row.dataset.course || '';
        const level = row.dataset.level || '';
        const text = row.textContent.toLowerCase();
        
        const matchesSearch = text.includes(searchTerm);
        const matchesCourse = !courseFilter || course === courseFilter;
        const matchesLevel = !levelFilter || level === levelFilter;
        
        if (matchesSearch && matchesCourse && matchesLevel) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('showing-count').textContent = visibleCount;
}

function viewStudentDetails(studentId) {
    const student = allStudents.find(s => s.id === studentId);
    if (!student) return;
    
    const content = `
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                    <span class="text-xl font-bold text-white">${student.user.full_name.substring(0, 2)}</span>
                </div>
                <div>
                    <h4 class="text-xl font-semibold text-gray-900">${student.user.full_name}</h4>
                    <p class="text-gray-600 font-mono">${student.matric_number}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                            ${student.academic_level ? student.academic_level.name : 'N/A'}
                        </span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                            ${student.department ? student.department.name : 'N/A'}
                        </span>
                        <span class="px-2 py-1 ${student.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-semibold rounded-full">
                            ${student.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                    <p class="mt-1 text-sm text-gray-900">${student.user.email}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <p class="mt-1 text-sm text-gray-900">${student.phone || 'Not provided'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department</label>
                    <p class="mt-1 text-sm text-gray-900">${student.department ? student.department.name : 'Not assigned'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Level</label>
                    <p class="mt-1 text-sm text-gray-900">${student.academic_level ? student.academic_level.name : 'Not assigned'}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Face Registration</label>
                    <p class="mt-1 text-sm">
                        <span class="px-2 py-1 ${student.face_registration_enabled ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'} text-xs font-semibold rounded-full">
                            ${student.face_registration_enabled ? 'Enabled' : 'Disabled'}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference Image</label>
                    <p class="mt-1 text-sm">
                        <span class="px-2 py-1 ${student.reference_image_path ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs font-semibold rounded-full">
                            ${student.reference_image_path ? 'Available' : 'Not Available'}
                        </span>
                    </p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Enrolled Courses (${student.classrooms.length})</label>
                <div class="space-y-3 max-h-48 overflow-y-auto">
                    ${student.classrooms.map(classroom => `
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">${classroom.course ? classroom.course.course_name : 'Unknown Course'}</div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    ${classroom.course ? classroom.course.course_code : 'N/A'}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>Class:</span>
                                    <span>${classroom.class_name}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Lecturer:</span>
                                    <span>${classroom.lecturer ? classroom.lecturer.user.full_name : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>PIN:</span>
                                    <span class="font-mono">${classroom.pin}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status:</span>
                                    <span class="${classroom.is_active ? 'text-green-600' : 'text-red-600'} font-semibold">
                                        ${classroom.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button onclick="closeStudentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Close
                </button>
                <a href="/lecturer/students/${student.id}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    View Full Details
                </a>
                <a href="/lecturer/students/${student.id}/attendance" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    View Attendance
                </a>
            </div>
        </div>
    `;
    
    document.getElementById('student-details-content').innerHTML = content;
    document.getElementById('student-modal').classList.remove('hidden');
}

function viewStudentAttendance(studentId) {
    // Redirect to student attendance page or open modal
    window.location.href = `/lecturer/students/${studentId}/attendance`;
}

function closeStudentModal() {
    document.getElementById('student-modal').classList.add('hidden');
}

// Event listeners
document.getElementById('search-input').addEventListener('input', filterStudents);
document.getElementById('course-filter').addEventListener('change', filterStudents);
document.getElementById('level-filter').addEventListener('change', filterStudents);

// Close modal when clicking outside
document.getElementById('student-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStudentModal();
    }
});
</script>
@endpush
