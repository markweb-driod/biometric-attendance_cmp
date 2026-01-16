@extends('layouts.superadmin')

@section('title', 'Class Details')
@section('page-title', 'Class Details')
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

<div class="max-w-2xl mx-auto mt-8 bg-white rounded-2xl shadow-lg p-8">
    <a href="/superadmin/classes" class="inline-flex items-center mb-6 text-green-700 hover:underline">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Back to Classes
    </a>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold" id="classTitle">Class: {{ $class->class_name }}</h2>
        <div class="flex gap-2">
            <button onclick="openEditModal()" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold shadow hover:bg-blue-600 transition text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                Edit
            </button>
            <button onclick="deleteClass({{ $class->id }})" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg font-semibold shadow hover:bg-red-600 transition text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Delete
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-base" id="classDetails">
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>Class Code:</span> <span id="detailCourseCode">{{ $class->course_code }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" /></svg>Level:</span> <span id="detailLevel">{{ $class->level }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>Lecturer:</span> <span id="detailLecturer">{{ $class->lecturer->name ?? '-' }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01" /></svg>Schedule:</span> <span id="detailSchedule">{{ $class->schedule }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>PIN:</span> <span id="detailPin">{{ $class->pin }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>Status:</span> <span id="detailStatus" class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $class->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">{{ ucfirst($class->status) }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>Created At:</span> <span id="detailCreatedAt">{{ $class->created_at ? $class->created_at->format('Y-m-d H:i') : '-' }}</span></div>
        <div><span class="font-semibold text-gray-700 flex items-center"><svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>Updated At:</span> <span id="detailUpdatedAt">{{ $class->updated_at ? $class->updated_at->format('Y-m-d H:i') : '-' }}</span></div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative animate-fade-in">
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Edit Class</h3>
        <form id="editForm" class="space-y-4">
            <input type="hidden" id="editClassId" value="{{ $class->id }}">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class Code</label><input type="text" id="editCourseCode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->course_code }}" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class Name</label><input type="text" id="editClassName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->class_name }}" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Level</label><input type="text" id="editLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->level }}" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Lecturer</label><input type="text" id="editLecturer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->lecturer->name ?? '' }}" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label><input type="text" id="editSchedule" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->schedule }}"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">PIN</label><input type="text" id="editPin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->pin }}"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label><input type="number" step="0.000001" id="editLatitude" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->latitude }}"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label><input type="number" step="0.000001" id="editLongitude" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->longitude }}"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Radius (meters)</label><input type="number" id="editRadius" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition" value="{{ $class->radius ?? 50 }}"></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-base font-semibold rounded-lg shadow hover:bg-blue-700 transition">Save</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const classId = document.getElementById('editClassId').value;
    const data = {
        class_name: document.getElementById('editClassName').value,
        course_code: document.getElementById('editCourseCode').value,
        level: document.getElementById('editLevel').value,
        schedule: document.getElementById('editSchedule').value,
        pin: document.getElementById('editPin').value,
        latitude: document.getElementById('editLatitude').value,
        longitude: document.getElementById('editLongitude').value,
        radius: document.getElementById('editRadius').value,
    };
    const saveBtn = this.querySelector('button[type="submit"]');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    axios.put(`/api/superadmin/classes/${classId}`, data)
        .then(() => {
            showToast('Class updated successfully', 'success');
            // Update details on the page
            document.getElementById('classTitle').textContent = 'Class: ' + data.class_name;
            document.getElementById('detailCourseCode').textContent = data.course_code;
            document.getElementById('detailLevel').textContent = data.level;
            document.getElementById('detailSchedule').textContent = data.schedule;
            document.getElementById('detailPin').textContent = data.pin;
            document.getElementById('detailUpdatedAt').textContent = new Date().toISOString().slice(0, 16).replace('T', ' ');
            closeEditModal();
        })
        .catch(() => {
            showToast('Failed to update class.', 'error');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save';
        });
});
function deleteClass(classId) {
    if (!confirm('Delete this class?')) return;
    axios.delete(`/api/superadmin/classes/${classId}`)
        .then(() => {
            showToast('Class deleted successfully', 'success');
            setTimeout(() => { window.location.href = '/superadmin/classes'; }, 1000);
        })
        .catch(() => {
            showToast('Failed to delete class.', 'error');
        });
}
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium shadow-lg ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection 