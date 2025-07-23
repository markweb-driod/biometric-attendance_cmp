@extends('layouts.lecturer')

@section('title', 'Live Attendance')
@section('page-title', 'Live Attendance')
@section('page-description', 'Mark and monitor attendance for your class in real time')

@section('content')
<div class="max-w-7xl w-full mx-auto px-2 sm:px-6 lg:px-8 py-6">
    @if(session('quick_url'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex flex-col sm:flex-row items-center gap-3">
        <div class="flex-1 text-green-900 text-sm">
            <span class="font-bold">Quick Attendance Link:</span>
            <input type="text" id="quick-attendance-url" class="w-full sm:w-auto bg-green-100 border border-green-300 rounded px-2 py-1 font-mono text-green-800 text-xs" value="{{ session('quick_url') }}" readonly>
            <span class="block text-xs text-green-700 mt-1">Share this link with students. It will auto-fill the attendance code and is only valid while this session is active.</span>
        </div>
        <button onclick="copyQuickUrl()" class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition text-xs">Copy Link</button>
    </div>
    <script>
    function copyQuickUrl() {
        const input = document.getElementById('quick-attendance-url');
        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        let btn = event.target;
        btn.textContent = 'Copied!';
        setTimeout(() => { btn.textContent = 'Copy Link'; }, 1500);
    }
    </script>
    @endif
    <!-- Session Info & KPI Cards -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $class->code }} - {{ $class->name }} ({{ $class->level }})</h2>
                <div class="text-sm text-gray-500 mb-2">Attendance Code: <span class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-800">{{ $session->code }}</span></div>
                <div class="text-xs text-gray-400">Session started: {{ $session->start_time->format('Y-m-d H:i') }}</div>
            </div>
            <div id="kpi-cards" class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full md:w-auto">
                <div class="bg-blue-50 rounded-lg px-4 py-2 text-center">
                    <div class="text-xs text-blue-700 font-semibold">Total</div>
                    <div class="text-xl font-bold text-blue-900" id="kpi-total">{{ $class->students->count() }}</div>
                </div>
                <div class="bg-green-50 rounded-lg px-4 py-2 text-center">
                    <div class="text-xs text-green-700 font-semibold">Present</div>
                    <div class="text-xl font-bold text-green-900" id="kpi-present">{{ $attendances->where('status', 'present')->count() }}</div>
                </div>
                <div class="bg-red-50 rounded-lg px-4 py-2 text-center">
                    <div class="text-xs text-red-700 font-semibold">Absent</div>
                    <div class="text-xl font-bold text-red-900" id="kpi-absent">{{ $class->students->count() - $attendances->where('status', 'present')->count() }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg px-4 py-2 text-center">
                    <div class="text-xs text-purple-700 font-semibold">Attendance %</div>
                    <div class="text-xl font-bold text-purple-900" id="kpi-percent">
                        {{ $class->students->count() > 0 ? round(($attendances->where('status', 'present')->count() / $class->students->count()) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <input type="number" id="recalibrate-radius" class="border rounded px-2 py-1 w-24" placeholder="Radius (m)" value="{{ $session->radius ?? 50 }}">
            <button id="recalibrate-btn" type="button" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">📍 Recalibrate Location</button>
            <span id="recalibrate-status" class="text-sm text-gray-500"></span>
        </div>
    </div>

    <!-- Student List Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Student Attendance List</h3>
            <button id="refresh-table" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Refresh</button>
        </div>
        <form method="POST" action="{{ route('lecturer.attendance.mark', ['sessionId' => $session->id]) }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matric No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody id="student-table" class="bg-white divide-y divide-gray-200">
                        @php
                            $perPage = 20;
                            $page = request()->get('page', 1);
                            $studentsArr = $students instanceof \Illuminate\Support\Collection ? $students->values() : collect($students)->values();
                            $total = $studentsArr->count();
                            $start = ($page - 1) * $perPage;
                            $paginated = $studentsArr->slice($start, $perPage);
                        @endphp
                        @foreach($paginated as $student)
                            @php
                                $attendance = $attendances->where('student_id', $student->id)->first();
                            @endphp
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $student->full_name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $student->matric_number }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($attendance && $attendance->status === 'present')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Present</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Absent</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($attendance && $attendance->image_path)
                                        <img src="{{ asset('storage/' . $attendance->image_path) }}" alt="Attendance Image" class="w-12 h-12 object-cover rounded border cursor-pointer attendance-thumb" data-full="{{ asset('storage/' . $attendance->image_path) }}" />
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <button name="mark_present" value="{{ $student->id }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">Mark Present</button>
                                    <button name="mark_absent" value="{{ $student->id }}" class="px-3 py-1 bg-gray-400 text-white text-xs rounded-lg hover:bg-gray-500 transition">Mark Absent</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @php
                $lastPage = ceil($total / $perPage);
            @endphp
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">Page {{ $page }} of {{ $lastPage }}</div>
                <div class="flex gap-2">
                    @if($page > 1)
                        <a href="?page={{ $page - 1 }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Previous</a>
                    @endif
                    @if($page < $lastPage)
                        <a href="?page={{ $page + 1 }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Next</a>
                    @endif
                </div>
            </div>
        </form>
        <form method="POST" action="{{ route('lecturer.attendance.end', ['sessionId' => $session->id]) }}" class="mt-8">
            @csrf
            <button type="submit" class="px-6 py-2 bg-red-600 text-white text-base rounded-lg font-semibold hover:bg-red-700 transition">End Attendance Session</button>
        </form>
    </div>
</div>
<!-- Add spinner overlay -->
<div id="table-spinner" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-20 z-50 hidden">
    <div class="w-16 h-16 border-4 border-green-500 border-t-transparent rounded-full animate-spin"></div>
</div>
<!-- Image Preview Modal -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded shadow-lg p-4 max-w-2xl w-full flex flex-col items-center">
        <img id="modal-image" src="" alt="Attendance Image" class="max-h-[70vh] w-auto rounded mb-4" />
        <button id="close-image-modal" class="px-4 py-2 bg-gray-700 text-white rounded">Close</button>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
function showTableSpinner(show) {
    document.getElementById('table-spinner').classList.toggle('hidden', !show);
}
function fetchLiveAttendance() {
    showTableSpinner(true);
    axios.get('/api/lecturer/attendance-session-live/{{ $session->id }}')
        .then(res => {
            const data = res.data;
            // Update KPI cards
            document.getElementById('kpi-total').textContent = data.total;
            document.getElementById('kpi-present').textContent = data.present;
            document.getElementById('kpi-absent').textContent = data.absent;
            document.getElementById('kpi-percent').textContent = data.percent + '%';
            // Update student table
            let html = '';
            data.students.forEach(student => {
                html += `<tr>
                    <td class='px-4 py-3 whitespace-nowrap'>${student.full_name}</td>
                    <td class='px-4 py-3 whitespace-nowrap'>${student.matric_number}</td>
                    <td class='px-4 py-3 whitespace-nowrap'>${student.status === 'present' ? `<span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800'>Present</span>` : `<span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600'>Absent</span>`}</td>
                    <td class='px-4 py-3 whitespace-nowrap'>` +
                        (student.status === 'present' && student.image_path ?
                            `<img src='/storage/${student.image_path}' alt='Attendance Image' class='w-12 h-12 object-cover rounded border cursor-pointer attendance-thumb' data-full='/storage/${student.image_path}' />`
                            : `<span class='text-xs text-gray-400'>-</span>`) +
                    `</td>
                    <td class='px-4 py-3 whitespace-nowrap'>
                        ` + (student.status !== 'present' ? `
                        <button data-id='${student.id}' data-action='present' class='mark-btn px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition mr-1'>Mark Present</button>
                        <button data-id='${student.id}' data-action='absent' class='mark-btn px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition'>Mark Absent</button>
                        ` : '') + `
                    </td>
                </tr>`;
            });
            document.getElementById('student-table').innerHTML = html;
            // Re-attach image modal logic
            document.querySelectorAll('.attendance-thumb').forEach(function(img) {
                img.addEventListener('click', function() {
                    const modal = document.getElementById('image-modal');
                    const modalImg = document.getElementById('modal-image');
                    modalImg.src = img.getAttribute('data-full');
                    modal.classList.remove('hidden');
                });
            });
            // Attach AJAX mark present/absent
            document.querySelectorAll('.mark-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const studentId = btn.getAttribute('data-id');
                    const action = btn.getAttribute('data-action');
                    showTableSpinner(true);
                    axios.post(`{{ route('lecturer.attendance.mark', ['sessionId' => $session->id]) }}`,
                        {
                            [action === 'present' ? 'mark_present' : 'mark_absent']: studentId
                        },
                        {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        }
                    ).then(() => {
                        // Only refresh table, not full page
                        fetchLiveAttendance();
                    });
                });
            });
        })
        .finally(() => showTableSpinner(false));
}
// Initial fetch and polling
fetchLiveAttendance();
setInterval(fetchLiveAttendance, 15000);
document.getElementById('refresh-table').addEventListener('click', function() {
    fetchLiveAttendance();
});
</script>
@endsection

@section('scripts')
<script>
document.getElementById('recalibrate-btn').onclick = function() {
    var radius = document.getElementById('recalibrate-radius').value || 50;
    if (navigator.geolocation) {
        document.getElementById('recalibrate-status').textContent = 'Getting location...';
        navigator.geolocation.getCurrentPosition(function(position) {
            fetch('/lecturer/attendance/recalibrate/' + {{ $session->id }}, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    radius: radius
                })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('recalibrate-status').textContent = 'Location recalibrated!';
            })
            .catch(() => {
                document.getElementById('recalibrate-status').textContent = 'Error recalibrating location.';
            });
        }, function(error) {
            document.getElementById('recalibrate-status').textContent = 'Location access denied.';
        });
    } else {
        document.getElementById('recalibrate-status').textContent = 'Geolocation not supported.';
    }
};
</script>
// Image preview modal logic
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-image');
    const closeModal = document.getElementById('close-image-modal');
    document.querySelectorAll('.attendance-thumb').forEach(function(img) {
        img.addEventListener('click', function() {
            modalImg.src = img.getAttribute('data-full');
            modal.classList.remove('hidden');
        });
    });
    closeModal.addEventListener('click', function() {
        modal.classList.add('hidden');
        modalImg.src = '';
    });
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modalImg.src = '';
        }
    });
});
</script>
@endsection 