@extends('layouts.superadmin')

@section('title', 'Classes')
@section('page-title', 'Classes')
@section('page-description', 'Manage all classes, assign lecturers, and set levels')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="stats-cards">
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Total Classes</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-classes">{{ $stats['total'] ?? '-' }}</div>
        </div>
        <div class="bg-blue-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Active Classes</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-active">{{ $stats['active'] ?? '-' }}</div>
        </div>
        <div class="bg-purple-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Inactive Classes</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-inactive">{{ $stats['inactive'] ?? '-' }}</div>
        </div>
        <div class="bg-orange-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Last Created</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-created">{{ $stats['last_created'] ? \Carbon\Carbon::parse($stats['last_created'])->format('Y-m-d H:i') : '-' }}</div>
        </div>
    </div>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-green-500 rounded-lg px-6 py-3 shadow mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Classes Management</h2>
            <p class="text-white text-base opacity-90">Search, view, manage, and fix all classes</p>
        </div>
    </div>
    <!-- Search/Filter Form -->
    <form method="GET" class="flex flex-col md:flex-row gap-2 w-full mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, lecturer..." class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        <select name="level" class="w-full md:w-40 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            <option value="">All Levels</option>
            @foreach($levels as $level)
                <option value="{{ $level }}" @if(request('level') == $level) selected @endif>{{ $level }} Level</option>
            @endforeach
        </select>
        <select name="lecturer" class="w-full md:w-40 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            <option value="">All Lecturers</option>
            @foreach($lecturers as $lecturer)
                <option value="{{ $lecturer->id }}" @if(request('lecturer') == $lecturer->id) selected @endif>{{ $lecturer->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Filter</button>
    </form>
    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow p-2 sm:p-3 mb-3 w-full">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Class Code</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Class Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Level</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Lecturer</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Schedule</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">PIN</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 uppercase text-xs tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($classes as $class)
                    <tr class="even:bg-gray-50 hover:bg-green-50 transition-colors duration-200 align-middle">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $class->course_code }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $class->class_name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $class->level }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $class->lecturer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $class->schedule }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $class->pin }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $class->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                {{ ucfirst($class->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-row items-center justify-center gap-2">
                                <a href="/superadmin/classes/{{ $class->id }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200 shadow-sm" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 0118 0 9 9 0 01-18 0z" /></svg>
                                    View
                                </a>
                                <button class="inline-flex items-center px-2.5 py-1.5 bg-blue-500 text-white rounded-lg text-xs font-semibold hover:bg-blue-600 shadow-sm" title="Edit" onclick="openEditModal({{ $class->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    Edit
                                </button>
                                <button class="inline-flex items-center px-2.5 py-1.5 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 shadow-sm" title="Delete" onclick="deleteClass({{ $class->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-gray-400 py-8">No classes found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
</div>
<!-- Add/Edit Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative animate-fade-in">
        <button onclick="closeAddModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        <h3 class="text-2xl font-bold text-gray-900 mb-4" id="addModalTitle">Create Class</h3>
        <form id="addForm" class="space-y-4">
            <input type="hidden" id="classId">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class Code</label><input type="text" id="courseCode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base transition" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Class Name</label><input type="text" id="className" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base transition" required></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Level</label><select id="level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base transition" required><option value="">Select Level</option><option value="100">100 Level</option><option value="200">200 Level</option><option value="300">300 Level</option><option value="400">400 Level</option></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Lecturer</label><select id="lecturer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base transition" required><option value="">Select Lecturer</option><!-- Populate with lecturers --></select></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label><input type="text" id="schedule" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base transition"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">PIN</label><input type="text" id="pin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-base transition"></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-lg hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-base font-semibold rounded-lg shadow hover:bg-blue-700 transition">Save</button>
            </div>
        </form>
    </div>
</div>
<style>@keyframes fade-in { from { opacity: 0; transform: scale(0.95);} to { opacity: 1; transform: scale(1);} } .animate-fade-in { animation: fade-in 0.2s ease; }</style>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
let editingClassId = null;
let lecturersList = [];

document.addEventListener('DOMContentLoaded', function() {
    fetchLecturers();
    fetchClasses();
    document.getElementById('searchInput').addEventListener('input', fetchClasses);
    document.getElementById('levelFilter').addEventListener('change', fetchClasses);
    document.getElementById('lecturerFilter').addEventListener('change', fetchClasses);
    document.getElementById('addForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const classId = document.getElementById('classId').value;
        const data = {
            class_name: document.getElementById('className').value,
            course_code: document.getElementById('courseCode').value,
            level: document.getElementById('level').value,
            lecturer_id: document.getElementById('lecturer').value,
            schedule: document.getElementById('schedule').value,
            pin: document.getElementById('pin').value,
        };
        const saveBtn = this.querySelector('button[type="submit"]');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
        axios.put(`/api/superadmin/classes/${classId}`, data)
            .then(() => {
                showToast('Class updated successfully', 'success');
                closeAddModal();
                window.location.reload();
            })
            .catch(() => {
                showToast('Failed to update class.', 'error');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
            });
    });
});

function fetchLecturers() {
    axios.get('/api/superadmin/lecturers')
        .then(res => {
            lecturersList = res.data.data;
            const select = document.getElementById('lecturer');
            select.innerHTML = '<option value="">Select Lecturer</option>';
            lecturersList.forEach(l => {
                select.innerHTML += `<option value="${l.id}">${l.name}</option>`;
            });
            // Also populate filter
            const filter = document.getElementById('lecturerFilter');
            filter.innerHTML = '<option value="">All Lecturers</option>';
            lecturersList.forEach(l => {
                filter.innerHTML += `<option value="${l.id}">${l.name}</option>`;
            });
        });
}

function fetchClasses() {
    const table = document.getElementById('classesTable');
    table.innerHTML = '<tr><td colspan="8" class="text-center text-gray-400 py-8">Loading...</td></tr>';
    const search = document.getElementById('searchInput').value;
    const level = document.getElementById('levelFilter').value;
    const lecturer = document.getElementById('lecturerFilter').value;
    let url = '/api/superadmin/classes?';
    if (search) url += 'search=' + encodeURIComponent(search) + '&';
    if (level) url += 'level=' + encodeURIComponent(level) + '&';
    if (lecturer) url += 'lecturer=' + encodeURIComponent(lecturer);
    axios.get(url)
        .then(res => {
            const classes = res.data.data;
            if (!classes.length) {
                table.innerHTML = '<tr><td colspan="8" class="text-center text-gray-400 py-8">No classes found.</td></tr>';
                return;
            }
            table.innerHTML = '';
            classes.forEach(c => {
                table.innerHTML += `
                <tr>
                    <td class="px-3 py-2">${c.course_code}</td>
                    <td class="px-3 py-2">${c.class_name}</td>
                    <td class="px-3 py-2">${c.academic_level || ''}</td>
                    <td class="px-3 py-2">${c.lecturer_name || ''}</td>
                    <td class="px-3 py-2">${c.schedule || ''}</td>
                    <td class="px-3 py-2">${c.pin}</td>
                    <td class="px-3 py-2">${c.is_active ? '<span class='text-green-600'>Active</span>' : '<span class='text-red-600'>Inactive</span>'}</td>
                    <td class="px-3 py-2"><button class='text-blue-600 hover:underline' onclick='editClass(${c.id})'>Edit</button> <button class='text-red-600 hover:underline' onclick='deleteClass(${c.id})'>Delete</button></td>
                </tr>`;
            });
        })
        .catch(err => {
            table.innerHTML = '<tr><td colspan="8" class="text-center text-red-400 py-8">Failed to load classes.</td></tr>';
            console.error('API error:', err);
        });
}

function openAddModal() {
    editingClassId = null;
    document.getElementById('addModalTitle').textContent = 'Create Class';
    document.getElementById('addForm').reset();
    document.getElementById('classId').value = '';
    document.getElementById('addModal').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}
function openEditModal(classId) {
    axios.get(`/api/superadmin/classes/${classId}`)
        .then(res => {
            const c = res.data.data;
            document.getElementById('addModalTitle').textContent = 'Edit Class';
            document.getElementById('classId').value = c.id;
            document.getElementById('courseCode').value = c.course_code;
            document.getElementById('className').value = c.class_name;
            document.getElementById('level').value = c.level || '';
            document.getElementById('lecturer').value = c.lecturer_id || '';
            document.getElementById('schedule').value = c.schedule || '';
            document.getElementById('pin').value = c.pin;
            document.getElementById('addModal').classList.remove('hidden');
        })
        .catch(() => {
            showToast('Failed to load class details.', 'error');
        });
}
function submitClassForm(e) {
    e.preventDefault();
    const data = {
        class_name: document.getElementById('className').value,
        course_code: document.getElementById('courseCode').value,
        academic_level: document.getElementById('level').value,
        lecturer_id: document.getElementById('lecturer').value,
        schedule: document.getElementById('schedule').value,
        description: document.getElementById('description').value,
        pin: document.getElementById('pin').value,
    };
    if (editingClassId) {
        axios.put('/api/superadmin/classes/' + editingClassId, data)
            .then(() => {
                closeAddModal();
                fetchClasses();
                fetchClassStats();
            })
            .catch(err => {
                alert('Failed to update class.');
                console.error(err);
            });
    } else {
        axios.post('/api/superadmin/classes', data)
            .then(() => {
                closeAddModal();
                fetchClasses();
                fetchClassStats();
            })
            .catch(err => {
                alert('Failed to create class.');
                console.error(err);
            });
    }
}
function deleteClass(id) {
    if (!confirm('Delete this class?')) return;
    axios.delete('/api/superadmin/classes/' + id)
        .then(() => { fetchClasses(); fetchClassStats(); })
        .catch(() => alert('Failed to delete class.'));
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