@extends('layouts.lecturer')

@section('title', 'Class Details')
@section('page-title', 'Class Details')
@section('page-description', 'View details and manage this class')

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

@if(!isset($class) || !$class)
    <div class="max-w-2xl mx-auto mt-12 p-8 bg-white rounded-xl shadow text-center">
        <h2 class="text-2xl font-bold text-red-600 mb-2">Class Not Found</h2>
        <p class="text-gray-700 mb-4">The class you are looking for does not exist or you do not have access to it.</p>
        <a href="/lecturer/classes" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Back to Classes</a>
    </div>
@elseif($errors->any())
    <div class="max-w-2xl mx-auto mt-12 p-8 bg-white rounded-xl shadow text-center">
        <h2 class="text-2xl font-bold text-red-600 mb-2">Error</h2>
        <ul class="text-gray-700 mb-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <a href="/lecturer/classes" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Back to Classes</a>
    </div>
@else
@php
    $studentPage = request()->get('student_page', 1);
    $attendancePage = request()->get('attendance_page', 1);
    $perPage = 8;
    $studentsArr = $class->students instanceof \Illuminate\Support\Collection ? $class->students->values() : collect($class->students)->values();
    $totalStudents = $studentsArr->count();
    $studentStart = ($studentPage - 1) * $perPage;
    $studentPaginated = $studentsArr->slice($studentStart, $perPage);
    $studentLastPage = ceil($totalStudents / $perPage);
    $attArr = $attendances instanceof \Illuminate\Support\Collection ? $attendances->values() : collect($attendances)->values();
    $totalAtt = $attArr->count();
    $attStart = ($attendancePage - 1) * $perPage;
    $attPaginated = $attArr->slice($attStart, $perPage);
    $attLastPage = ceil($totalAtt / $perPage);
    $activeSession = $class->attendanceSessions()->where('is_active', true)->latest('start_time')->first();
@endphp
<div class="w-full min-h-screen px-2 sm:px-6 lg:px-12 py-8 bg-gradient-to-br from-green-50 via-white to-green-100">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-500 to-green-400 rounded-2xl shadow-xl border border-green-200 p-4 sm:p-6 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6" style="min-height: 100px;">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-2xl font-bold text-green-600 shadow">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><text x="12" y="16" text-anchor="middle" font-size="10" fill="currentColor">{{ strtoupper(substr($class->course_code,0,2)) }}</text></svg>
                </div>
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">{{ $class->course_code }} - {{ $class->class_name }}</h2>
                    <div class="text-base text-green-50 font-medium mt-1">Class Details <span class="mx-2 text-white/40">|</span> <span class="font-semibold">Level {{ $class->level }}</span></div>
                </div>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-1 mt-2">
                <div class="text-sm text-green-50">Schedule: <span class="font-semibold text-white">{{ $class->schedule ?? '-' }}</span></div>
                <div class="text-sm text-green-50">Description: <span class="font-semibold text-white">{{ $class->description ?? '-' }}</span></div>
                <div class="text-sm text-green-50">Lecturer: <span class="font-semibold text-white">{{ $class->lecturer->name ?? '-' }}</span></div>
            </div>
        </div>
        <div class="flex flex-col gap-2 items-end">
            @if($activeSession)
                <div class="bg-white rounded-xl px-5 py-3 text-center shadow flex flex-col items-center min-w-[120px]">
                    <div class="text-xs text-green-700 font-semibold mb-1">Current Attendance Code</div>
                    <div class="font-mono text-xl text-green-900 tracking-wider mb-1" id="attendance-code">{{ $activeSession->code }}</div>
                    <button onclick="copyCode()" class="px-3 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold hover:bg-green-200">Copy Code</button>
                </div>
            @else
                <div class="bg-gray-100 rounded-xl px-5 py-3 text-center shadow flex flex-col items-center min-w-[120px]">
                    <div class="text-xs text-gray-500 font-semibold mb-1">Class PIN</div>
                    <div class="font-mono text-xl text-gray-900 tracking-wider mb-1">{{ $class->pin }}</div>
                    <button onclick="copyPin()" class="px-3 py-1 bg-gray-200 text-gray-800 rounded text-xs font-semibold hover:bg-gray-300">Copy PIN</button>
                </div>
            @endif
        </div>
    </div>
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-2xl shadow-xl border-2 border-green-200 px-8 py-8 text-center transform transition-transform duration-200 hover:scale-105 flex flex-col items-center">
            <div class="mb-2"><svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 0 0-3-3.87"/><path d="M9 20H4v-2a4 4 0 0 1 3-3.87"/><circle cx="9" cy="7" r="4"/><circle cx="17" cy="7" r="4"/></svg></div>
            <div class="text-base text-green-700 font-bold mb-1">Total Students</div>
            <div class="text-3xl font-extrabold text-green-700 count-up" data-count="{{ $class->students->count() }}">0</div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border-2 border-green-100 px-8 py-8 text-center transform transition-transform duration-200 hover:scale-105 flex flex-col items-center">
            <div class="mb-2"><svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20v-6"/><circle cx="12" cy="10" r="4"/></svg></div>
            <div class="text-base text-green-700 font-bold mb-1">Present</div>
            <div class="text-3xl font-extrabold text-green-900 count-up" data-count="{{ $attendances->where('status', 'present')->count() }}">0</div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border-2 border-red-100 px-8 py-8 text-center transform transition-transform duration-200 hover:scale-105 flex flex-col items-center">
            <div class="mb-2"><svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20v-6"/><circle cx="12" cy="10" r="4"/></svg></div>
            <div class="text-base text-red-700 font-bold mb-1">Absent</div>
            <div class="text-3xl font-extrabold text-red-900 count-up" data-count="{{ $class->students->count() - $attendances->where('status', 'present')->count() }}">0</div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border-2 border-green-100 px-8 py-8 text-center transform transition-transform duration-200 hover:scale-105 flex flex-col items-center">
            <div class="mb-2"><svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20v-6"/><circle cx="12" cy="10" r="4"/></svg></div>
            <div class="text-base text-green-700 font-bold mb-1">Attendance %</div>
            <div class="text-3xl font-extrabold text-green-700 count-up" data-count="{{ $class->students->count() > 0 ? round(($attendances->where('status', 'present')->count() / $class->students->count()) * 100) : 0 }}">0</div>
        </div>
    </div>
    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-4 sm:p-6">
        <div class="flex gap-4 border-b mb-4">
            <button id="tab-students" class="py-2 px-4 text-sm font-semibold border-b-2 border-green-600 text-green-700 focus:outline-none">Students</button>
            <button id="tab-attendance" class="py-2 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-green-700 focus:outline-none">Attendance</button>
        </div>
        <!-- Students Tab -->
        <div id="students-section">
            @if($class->students->count() === 0)
                <div class="flex flex-col items-center justify-center py-12">
                    <svg width="80" height="80" fill="none" viewBox="0 0 80 80" class="mb-4">
                        <circle cx="40" cy="40" r="38" stroke="#22c55e" stroke-width="4" fill="#f0fdf4"/>
                        <path d="M40 25a10 10 0 1 1 0 20 10 10 0 0 1 0-20zm0 24c-8.837 0-16 4.03-16 9v2h32v-2c0-4.97-7.163-9-16-9z" fill="#bbf7d0"/>
                    </svg>
                    <div class="text-lg font-semibold text-green-700 mb-2">No students enrolled yet</div>
                    <div class="text-gray-500 mb-4">Start by enrolling students to this class to begin tracking attendance.</div>
                    <a href="/lecturer/students" class="inline-block px-5 py-2 bg-green-600 text-white rounded-lg font-semibold shadow hover:bg-green-700 transition">Enroll Students</a>
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-2">
                    <h3 class="text-base font-semibold">Enrolled Students ({{ $class->students->count() }})</h3>
                    <div class="flex gap-2 items-center">
                        <input id="student-search" type="text" placeholder="Search students..." class="px-2 py-1 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                        <button id="export-students" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold hover:bg-green-200">Export CSV</button>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table id="students-table" class="min-w-full text-xs sm:text-sm divide-y divide-gray-200 mb-2">
                        <thead class="bg-gray-100 sticky top-0 z-10 rounded-t-lg">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Student</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Matric No</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Level</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Department</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($studentPaginated as $student)
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">{{ $student->full_name }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $student->matric_number }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $student->level }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $student->department }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="flex justify-between items-center py-2">
                        <div class="text-xs text-gray-500">Page {{ $studentPage }} of {{ $studentLastPage }}</div>
                        <div class="flex gap-2">
                            @if($studentPage > 1)
                                <a href="?student_page={{ $studentPage - 1 }}&attendance_page={{ $attendancePage }}" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs">Previous</a>
                            @endif
                            @if($studentPage < $studentLastPage)
                                <a href="?student_page={{ $studentPage + 1 }}&attendance_page={{ $attendancePage }}" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs">Next</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- Attendance Tab -->
        <div id="attendance-section" style="display:none;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-2">
                <h3 class="text-base font-semibold">Attendance Records</h3>
                <div class="flex gap-2 items-center">
                    <input id="attendance-search" type="text" placeholder="Search attendance..." class="px-2 py-1 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                    <button id="export-attendance" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold hover:bg-green-200">Export CSV</button>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table id="attendance-table" class="min-w-full text-xs sm:text-sm divide-y divide-gray-200 mb-2">
                    <thead class="bg-gray-100 sticky top-0 z-10 rounded-t-lg">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Student</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Date & Time</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($attPaginated as $a)
                            <tr class="hover:bg-green-50 transition">
                                <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">{{ $a->student->full_name ?? '-' }} ({{ $a->student->matric_number ?? '-' }})</td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">{{ $a->captured_at ? $a->captured_at->format('Y-m-d H:i') : '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $a->status === 'present' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-between items-center py-2">
                    <div class="text-xs text-gray-500">Page {{ $attendancePage }} of {{ $attLastPage }}</div>
                    <div class="flex gap-2">
                        @if($attendancePage > 1)
                            <a href="?student_page={{ $studentPage }}&attendance_page={{ $attendancePage - 1 }}" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs">Previous</a>
                        @endif
                        @if($attendancePage < $attLastPage)
                            <a href="?student_page={{ $studentPage }}&attendance_page={{ $attendancePage + 1 }}" class="px-2 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-xs">Next</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Tab logic
const tabStudents = document.getElementById('tab-students');
const tabAttendance = document.getElementById('tab-attendance');
const studentsSection = document.getElementById('students-section');
const attendanceSection = document.getElementById('attendance-section');
tabStudents && tabStudents.addEventListener('click', function() {
    tabStudents.classList.add('border-green-600', 'text-green-700');
    tabStudents.classList.remove('border-transparent', 'text-gray-500');
    tabAttendance.classList.remove('border-green-600', 'text-green-700');
    tabAttendance.classList.add('border-transparent', 'text-gray-500');
    studentsSection.style.display = '';
    attendanceSection.style.display = 'none';
});
tabAttendance && tabAttendance.addEventListener('click', function() {
    tabAttendance.classList.add('border-green-600', 'text-green-700');
    tabAttendance.classList.remove('border-transparent', 'text-gray-500');
    tabStudents.classList.remove('border-green-600', 'text-green-700');
    tabStudents.classList.add('border-transparent', 'text-gray-500');
    studentsSection.style.display = 'none';
    attendanceSection.style.display = '';
});
// Student search
const studentSearch = document.getElementById('student-search');
const studentsTable = document.getElementById('students-table');
studentSearch && studentSearch.addEventListener('input', function() {
    const val = this.value.toLowerCase();
    Array.from(studentsTable.querySelectorAll('tbody tr')).forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
// Attendance search
const attendanceSearch = document.getElementById('attendance-search');
const attendanceTable = document.getElementById('attendance-table');
attendanceSearch && attendanceSearch.addEventListener('input', function() {
    const val = this.value.toLowerCase();
    Array.from(attendanceTable.querySelectorAll('tbody tr')).forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
});
// Export Students CSV
const exportStudentsBtn = document.getElementById('export-students');
exportStudentsBtn && exportStudentsBtn.addEventListener('click', function() {
    let rows = Array.from(studentsTable.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
    let csv = 'Student,Matric No,Level,Department\n';
    rows.forEach(row => {
        let cells = row.querySelectorAll('td');
        csv += Array.from(cells).map(cell => '"' + cell.textContent.trim().replace(/"/g, '""') + '"').join(',') + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'students.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});
// Export Attendance CSV
const exportAttendanceBtn = document.getElementById('export-attendance');
exportAttendanceBtn && exportAttendanceBtn.addEventListener('click', function() {
    let rows = Array.from(attendanceTable.querySelectorAll('tbody tr')).filter(row => row.style.display !== 'none');
    let csv = 'Student,Date & Time,Status\n';
    rows.forEach(row => {
        let cells = row.querySelectorAll('td');
        csv += Array.from(cells).map(cell => '"' + cell.textContent.trim().replace(/"/g, '""') + '"').join(',') + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'attendance.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
});
// Copy code buttons
function copyCode() {
    const code = document.getElementById('attendance-code').textContent;
    navigator.clipboard.writeText(code);
    showToast('Attendance code copied to clipboard!');
}

function copyPin() {
    const pin = '{{ $class->pin }}';
    navigator.clipboard.writeText(pin);
    showToast('Class PIN copied to clipboard!');
}

function showToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}
</script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.count-up').forEach(function(el) {
        const target = parseInt(el.getAttribute('data-count')) || 0;
        let count = 0;
        const step = Math.ceil(target / 30) || 1;
        function animate() {
            count += step;
            if (count >= target) {
                el.textContent = target;
            } else {
                el.textContent = count;
                requestAnimationFrame(animate);
            }
        }
        animate();
    });
});
</script>
@endpush
@endif
@endsection 