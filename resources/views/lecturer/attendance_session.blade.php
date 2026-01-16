@extends('layouts.lecturer')

@section('title', 'Live Attendance')
@section('page-title', 'Live Attendance')
@section('page-description', 'Mark and monitor attendance for your class in real time')

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

<div class="max-w-7xl w-full mx-auto px-2 sm:px-6 lg:px-8 py-6" data-session-id="{{ $session->id }}">
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
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3 mb-2">
          <h2 class="text-2xl font-bold text-gray-900 truncate">{{ $class->code }} - {{ $class->name }} <span class="text-gray-400 font-normal">({{ $class->level }})</span></h2>
        </div>
        <div class="text-sm text-gray-600 mb-2">
          <span class="font-semibold">Lecturer:</span> {{ $lecturer->name }} | 
          <span class="font-semibold">Course:</span> {{ $class->course->name ?? 'N/A' }} | 
          <span class="font-semibold">PIN:</span> {{ $class->pin }}
        </div>
        <div class="flex items-center gap-3 mb-2">
          <span class="text-sm text-gray-500">Attendance Code:</span>
          <span class="font-mono text-lg bg-green-100 border border-green-300 rounded px-3 py-1 text-green-800 tracking-widest select-all" id="session-code">{{ $session->code }}</span>
          <button onclick="navigator.clipboard.writeText('{{ $session->code }}'); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy',1200);" class="ml-2 px-3 py-1 bg-green-600 text-white rounded text-xs font-semibold hover:bg-green-700 transition">Copy</button>
        </div>
        <div class="flex items-center gap-3 mb-2">
            <span class="text-sm text-gray-500">Attendance Link:</span>
            <div class="flex items-center bg-gray-50 border border-gray-300 rounded px-2 py-1 max-w-md w-full">
                <input type="text" readonly value="{{ route('student.attendance-capture', ['code' => $session->code]) }}" class="bg-transparent border-none text-xs text-gray-600 flex-1 focus:ring-0 select-all" id="session-link-input">
                <button onclick="copySessionLink()" class="ml-2 text-blue-600 hover:text-blue-800 text-xs font-bold">COPY</button>
            </div>
        </div>
        <script>
            function copySessionLink() {
                const input = document.getElementById('session-link-input');
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value).then(() => {
                    const btn = event.target;
                    const originalText = btn.textContent;
                    btn.textContent = 'COPIED!';
                    setTimeout(() => btn.textContent = originalText, 1500);
                });
            }
        </script>
        <div class="text-xs text-gray-400 mb-2">
          <span class="font-semibold">Session started:</span> {{ $session->start_time->format('Y-m-d H:i') }} | 
          <span class="font-semibold">Duration:</span> {{ $session->start_time->diffForHumans() }} | 
          <span class="font-semibold">Status:</span> <span class="text-green-600 font-semibold">{{ ucfirst($session->status) }}</span>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <input type="number" id="recalibrate-radius" class="border rounded px-2 py-1 w-24" placeholder="Radius (m)" value="{{ $session->radius ?? 50 }}">
          <button id="recalibrate-btn" type="button" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">📍 Recalibrate Location</button>
          <span id="recalibrate-status" class="text-sm text-gray-500"></span>
        </div>
      </div>
      <div class="flex flex-col gap-3 min-w-[260px]">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
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
    </div>
    <!-- Filter Controls -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-4 mb-4 flex flex-col sm:flex-row sm:items-end gap-4">
      <div class="flex-1">
        <label class="block text-xs font-semibold text-gray-700 mb-1">Search</label>
        <input type="text" id="filter-search" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 text-base" placeholder="Name or Matric Number...">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
        <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 text-base">
          <option value="">All</option>
          <option value="present">Present</option>
          <option value="absent">Absent</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button id="refresh-table" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Refresh</button>
      </div>
    </div>
    <!-- Student List Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Student Attendance List</h3>
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
              <!-- Table rows rendered by JS/AJAX -->
            </tbody>
          </table>
        </div>
        <!-- Pagination Controls -->
        <div id="pagination-controls" class="flex justify-center items-center gap-2 mt-4"></div>
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
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
let currentPage = 1;
let lastPage = 1;
let perPage = 15;
let currentSearch = '';
let currentStatus = '';
let isLoading = false;
let searchTimeout = null;

function fetchLiveAttendance(page = 1) {
  if (isLoading) return;
  
  isLoading = true;
  const search = document.getElementById('filter-search').value.trim();
  const status = document.getElementById('filter-status').value;
  
  // Show loading spinner
  document.getElementById('table-spinner').classList.remove('hidden');
  
  axios.get(`/api/lecturer/attendance-session-live/${sessionId()}`, {
    params: {
      page: page,
      per_page: perPage,
      search: search,
      status: status
    }
  }).then(res => {
    const data = res.data;
    currentPage = data.page;
    lastPage = data.last_page;
    
    // Update KPIs
    document.getElementById('kpi-total').textContent = data.total;
    document.getElementById('kpi-present').textContent = data.present;
    document.getElementById('kpi-absent').textContent = data.absent;
    document.getElementById('kpi-percent').textContent = data.percent + '%';
    
    // Render table
    let html = '';
    if (data.students && data.students.length > 0) {
    data.students.forEach(student => {
      html += `<tr>
          <td class='px-4 py-3 whitespace-nowrap'>${student.full_name || 'Unknown'}</td>
        <td class='px-4 py-3 whitespace-nowrap'>${student.matric_number}</td>
        <td class='px-4 py-3 whitespace-nowrap'>${student.status === 'present' ? `<span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800'>Present</span>` : `<span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600'>Absent</span>`}</td>
        <td class='px-4 py-3 whitespace-nowrap'>-</td>
        <td class='px-4 py-3 whitespace-nowrap'>
          <button data-id='${student.id}' data-action='present' class='mark-btn px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition mr-1'>Mark Present</button>
          <button data-id='${student.id}' data-action='absent' class='mark-btn px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition'>Mark Absent</button>
        </td>
      </tr>`;
    });
    } else {
      html = `<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No students found</td></tr>`;
    }
    
    document.getElementById('student-table').innerHTML = html;
    renderPaginationControls();
  }).catch(error => {
    console.error('Error fetching attendance data:', error);
    document.getElementById('student-table').innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-red-500">Error loading data. Please try again.</td></tr>`;
  }).finally(() => {
    isLoading = false;
    document.getElementById('table-spinner').classList.add('hidden');
  });
}

function renderPaginationControls() {
  const pag = document.getElementById('pagination-controls');
  pag.innerHTML = '';
  if (lastPage <= 1) return;
  // Previous button
  const prev = document.createElement('button');
  prev.textContent = 'Previous';
  prev.className = 'px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50';
  prev.disabled = currentPage === 1;
  prev.onclick = () => fetchLiveAttendance(currentPage - 1);
  pag.appendChild(prev);
  // Page numbers (show up to 5 pages, centered)
  let start = Math.max(1, currentPage - 2);
  let end = Math.min(lastPage, currentPage + 2);
  if (currentPage <= 3) end = Math.min(5, lastPage);
  if (currentPage >= lastPage - 2) start = Math.max(1, lastPage - 4);
  for (let i = start; i <= end; i++) {
    const btn = document.createElement('button');
    btn.textContent = i;
    btn.className = 'mx-1 px-3 py-1 rounded ' + (i === currentPage ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200');
    btn.disabled = i === currentPage;
    btn.onclick = () => fetchLiveAttendance(i);
    pag.appendChild(btn);
  }
  // Next button
  const next = document.createElement('button');
  next.textContent = 'Next';
  next.className = 'px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50';
  next.disabled = currentPage === lastPage;
  next.onclick = () => fetchLiveAttendance(currentPage + 1);
  pag.appendChild(next);
}

function sessionId() {
  // Extract sessionId from the data attribute
  const container = document.querySelector('[data-session-id]');
  return container ? container.dataset.sessionId : '{{ $session->id }}';
}

document.addEventListener('DOMContentLoaded', function() {
  fetchLiveAttendance(1);
  
  // Debounced search to prevent constant refreshing
  document.getElementById('filter-search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
    fetchLiveAttendance(1);
    }, 500); // Wait 500ms after user stops typing
  });
  
  document.getElementById('filter-status').addEventListener('change', function() {
    fetchLiveAttendance(1);
  });
  
  document.getElementById('refresh-table').addEventListener('click', function() {
    fetchLiveAttendance(currentPage);
  });
  
  // Add event delegation for mark buttons
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('mark-btn')) {
      e.preventDefault();
      const studentId = e.target.dataset.id;
      const action = e.target.dataset.action;
      markAttendance(studentId, action);
    }
  });
});

function markAttendance(studentId, action) {
  if (isLoading) return;
  
  const formData = new FormData();
  formData.append('student_id', studentId);
  formData.append('status', action);
  
  // Get CSRF token from meta tag or use blade directive
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
  formData.append('_token', csrfToken);
  
  fetch(`/lecturer/attendance/session/{{ $session->id }}/mark`, {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Refresh the table to show updated status
      fetchLiveAttendance(currentPage);
    } else {
      alert('Error: ' + (data.message || 'Failed to mark attendance'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error marking attendance');
  });
}
</script>
@endpush
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