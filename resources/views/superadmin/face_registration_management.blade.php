@extends('layouts.superadmin')
@section('title', 'Student Face Registration Management')
@section('page-title', 'Student Face Registration Management')
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

<div class="w-full px-2 py-10 space-y-8">
  <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
    <div class="flex-1">
      <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
      <input type="text" id="search-input" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" placeholder="Name, Matric Number, Email...">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
      <select id="level-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition">
        <option value="">All</option>
        <option value="100">100</option>
        <option value="200">200</option>
        <option value="300">300</option>
        <option value="400">400</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Face Status</label>
      <select id="face-status-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition">
        <option value="">All</option>
        <option value="registered">Registered</option>
        <option value="not_registered">Not Registered</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button id="bulk-enable-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Enable</button>
      <button id="bulk-disable-btn" class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-semibold hover:bg-yellow-600 transition">Disable</button>
      <button id="bulk-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">Delete Images</button>
      <a href="{{ route('superadmin.students.export-face-registration') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Export CSV</a>
    </div>
  </div>
  <div class="overflow-x-auto rounded-2xl shadow-xl border border-green-200 bg-white">
    <table class="min-w-full divide-y divide-green-100" id="students-table">
      <thead class="bg-green-50">
        <tr>
          <th class="px-4 py-3"><input type="checkbox" id="select-all"></th>
          <th class="px-4 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Name</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Matric Number</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Level</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Face Image</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Face Registration</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-green-700 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody id="students-tbody" class="bg-white divide-y divide-green-100">
        <!-- Data will be loaded here by JS -->
      </tbody>
    </table>
    <div id="pagination" class="p-4 flex justify-center"></div>
  </div>
</div>
@include('superadmin.face_registration_modals')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const storageBaseUrl = '{{ asset("storage") }}';
let students = [];
let selectedIds = [];
let currentPage = 1;
let lastPage = 1;
let perPage = 20;
let currentFilters = { search: '', level: '', face_status: '' };

function fetchStudents(page = 1) {
  const params = {
    search: document.getElementById('search-input').value,
    level: document.getElementById('level-filter').value,
    face_status: document.getElementById('face-status-filter').value,
    page: page,
    per_page: perPage
  };
  currentFilters = params;
  axios.get('/superadmin/students/face-registration-management/data', { params })
    .then(res => {
      if (res.data.success) {
        students = res.data.data.data;
        currentPage = res.data.data.current_page;
        lastPage = res.data.data.last_page;
        renderTable();
        renderPagination();
      }
    });
}
function renderTable() {
  const tbody = document.getElementById('students-tbody');
  tbody.innerHTML = '';
  students.forEach(s => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="px-4 py-3"><input type="checkbox" class="row-checkbox" value="${s.id}" ${selectedIds.includes(s.id) ? 'checked' : ''}></td>
      <td class="px-4 py-3">${s.full_name || 'N/A'}</td>
      <td class="px-4 py-3">${s.matric_number || ''}</td>
      <td class="px-4 py-3">${s.academic_level || ''}</td>
      <td class="px-4 py-3">${s.reference_image_path ? `<img src='${storageBaseUrl}/${s.reference_image_path}' class='w-12 h-12 rounded object-cover border border-green-200 cursor-pointer' onclick='viewFaceImage("${storageBaseUrl}/${s.reference_image_path}")'>` : '<span class="text-gray-400">None</span>'}</td>
      <td class="px-4 py-3">${s.face_registration_enabled ? '<span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Enabled</span>' : '<span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">Disabled</span>'}</td>
      <td class="px-4 py-3 flex gap-2">
        <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold"
          data-id="${s.id}"
          data-name="${s.full_name ? s.full_name.replace(/\"/g, '&quot;') : ''}"
          data-matric="${s.matric_number ? s.matric_number.replace(/\"/g, '&quot;') : ''}"
          onclick="openUpdateFaceModal(this)">Update</button>
        <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-semibold" onclick="openDeleteFaceModal(${s.id})">Delete</button>
        <button class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs font-semibold" onclick="openToggleFaceModal(${s.id}, ${s.face_registration_enabled})">${s.face_registration_enabled ? 'Disable' : 'Enable'}</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
  // Checkbox logic
  document.querySelectorAll('.row-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
      const id = parseInt(this.value);
      if (this.checked) {
        if (!selectedIds.includes(id)) selectedIds.push(id);
      } else {
        selectedIds = selectedIds.filter(i => i !== id);
      }
    });
  });
  document.getElementById('select-all').checked = students.length > 0 && students.every(s => selectedIds.includes(s.id));
}
function renderPagination() {
  const pag = document.getElementById('pagination');
  pag.innerHTML = '';
  for (let i = 1; i <= lastPage; i++) {
    const btn = document.createElement('button');
    btn.textContent = i;
    btn.className = 'mx-1 px-3 py-1 rounded ' + (i === currentPage ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200');
    btn.onclick = () => fetchStudents(i);
    pag.appendChild(btn);
  }
}
// Filters
['search-input', 'level-filter', 'face-status-filter'].forEach(id => {
  document.getElementById(id).addEventListener('input', () => fetchStudents(1));
});
document.getElementById('select-all').addEventListener('change', function() {
  if (this.checked) {
    selectedIds = students.map(s => s.id);
  } else {
    selectedIds = [];
  }
  renderTable();
});
// Bulk actions
function bulkAction(action) {
  if (selectedIds.length === 0) return showToast('Select at least one student.', 'error');
  axios.post('/superadmin/students/face-registration-management/bulk-action', { action, ids: selectedIds })
    .then(res => {
      showToast(res.data.message || 'Bulk action done.');
      fetchStudents(currentPage);
      selectedIds = [];
    });
}
document.getElementById('bulk-enable-btn').onclick = () => bulkAction('enable');
document.getElementById('bulk-disable-btn').onclick = () => bulkAction('disable');
document.getElementById('bulk-delete-btn').onclick = () => bulkAction('delete_image');
// View face image
window.viewFaceImage = function(url) {
  document.getElementById('view-face-img').src = url;
  document.getElementById('view-face-modal').classList.remove('hidden');
};
// Update face modal
let updateFaceId = null;
window.openUpdateFaceModal = function(btn) {
  const id = btn.getAttribute('data-id');
  const name = btn.getAttribute('data-name');
  const matric = btn.getAttribute('data-matric');
  updateFaceId = id;
  document.getElementById('update-face-modal').classList.remove('hidden');
  startWebcam();
  document.getElementById('photo-preview').innerHTML = '';
  document.getElementById('save-face-btn').disabled = true;
  document.getElementById('update-face-details').innerHTML =
    `<div><span class="font-bold">Name:</span> ${name}</div>
     <div><span class="font-bold">Matric Number:</span> ${matric}</div>`;
};
function startWebcam() {
  const webcam = document.getElementById('webcam');
  navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
    webcam.srcObject = stream;
    window._webcamStream = stream;
  });
}
function stopWebcam() {
  if (window._webcamStream) {
    window._webcamStream.getTracks().forEach(track => track.stop());
    window._webcamStream = null;
  }
}
document.getElementById('capture-photo').onclick = function() {
  const webcam = document.getElementById('webcam');
  const snapshot = document.getElementById('snapshot');
  snapshot.width = webcam.videoWidth;
  snapshot.height = webcam.videoHeight;
  snapshot.getContext('2d').drawImage(webcam, 0, 0);
  const captured = snapshot.toDataURL('image/jpeg');
  document.getElementById('photo-preview').innerHTML = `<img src='${captured}' class='rounded w-full' style='max-height:180px;' />`;
  document.getElementById('save-face-btn').disabled = false;
  document.getElementById('save-face-btn').dataset.captured = captured;
  document.getElementById('webcam-container').classList.add('hidden');
  document.getElementById('capture-photo').classList.add('hidden');
  document.getElementById('retake-photo').classList.remove('hidden');
};
document.getElementById('retake-photo').onclick = function() {
  document.getElementById('photo-preview').innerHTML = '';
  document.getElementById('save-face-btn').disabled = true;
  document.getElementById('webcam-container').classList.remove('hidden');
  document.getElementById('capture-photo').classList.remove('hidden');
  document.getElementById('retake-photo').classList.add('hidden');
};
document.getElementById('save-face-btn').onclick = function() {
  const captured = this.dataset.captured;
  if (!captured || !updateFaceId) return;
  axios.post(`/superadmin/students/face-registration-management/update-image/${updateFaceId}`, { reference_image: captured })
    .then(res => {
      showToast(res.data.message || 'Face image updated.');
      fetchStudents(currentPage);
      closeModal('update-face-modal');
      stopWebcam();
    });
};
document.getElementById('update-face-modal').addEventListener('click', function(e) {
  if (e.target === this) { closeModal('update-face-modal'); stopWebcam(); }
});
// Delete face modal
let deleteFaceId = null;
window.openDeleteFaceModal = function(id) {
  deleteFaceId = id;
  document.getElementById('delete-face-modal').classList.remove('hidden');
};
document.getElementById('confirm-delete-btn').onclick = function() {
  if (!deleteFaceId) return;
  axios.delete(`/superadmin/students/face-registration-management/delete-image/${deleteFaceId}`)
    .then(res => {
      showToast(res.data.message || 'Face image deleted.');
      fetchStudents(currentPage);
      closeModal('delete-face-modal');
    });
};
// Enable/disable modal
let toggleFaceId = null;
let toggleFaceEnable = null;
window.openToggleFaceModal = function(id, enabled) {
  toggleFaceId = id;
  toggleFaceEnable = !enabled;
  document.getElementById('toggle-face-title').textContent = (enabled ? 'Disable' : 'Enable') + ' Face Registration';
  document.getElementById('toggle-face-msg').textContent = `Are you sure you want to ${(enabled ? 'disable' : 'enable')} face registration for this student?`;
  document.getElementById('toggle-face-modal').classList.remove('hidden');
};
document.getElementById('confirm-toggle-btn').onclick = function() {
  if (!toggleFaceId) return;
  axios.post(`/superadmin/students/face-registration-management/${toggleFaceEnable ? 'enable' : 'disable'}/${toggleFaceId}`)
    .then(res => {
      showToast(res.data.message || 'Action done.');
      fetchStudents(currentPage);
      closeModal('toggle-face-modal');
    });
};
// Initial load
fetchStudents();
</script>
@endpush
@endsection 