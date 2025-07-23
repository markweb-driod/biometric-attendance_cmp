@extends('layouts.lecturer')

@section('title', 'Classes')
@section('page-title', 'Classes')
@section('page-description', 'Manage your courses and class schedules')

@section('content')
@if($errors->any())
    <div class="max-w-2xl mx-auto mt-12 p-8 bg-white rounded-xl shadow text-center">
        <h2 class="text-2xl font-bold text-red-600 mb-2">Error</h2>
        <ul class="text-gray-700 mb-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@else
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Classes</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your courses and class schedules</p>
        </div>
        <div class="flex gap-2 mt-4 sm:mt-0">
            <button onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-full shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add New Class
            </button>
            <button onclick="exportClasses()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-full shadow hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export
            </button>
        </div>
    </div>
    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6" id="stats-cards">
        <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-chalkboard"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10">{{ $classes->count() }}</div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Total Classes</div>
        </div>
        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-check-circle"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10">{{ $classes->where('is_active', true)->count() }}</div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Active Classes</div>
        </div>
        <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-users"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10">{{ $classes->sum(fn($c) => $c->students->count()) }}</div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Total Students</div>
        </div>
        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-calendar-alt"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10">{{ $classes->count() ? round($classes->where('is_active', true)->count() / $classes->count() * 100) : 0 }}%</div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Active %</div>
        </div>
    </div>
    <script>const faScript=document.createElement('script');faScript.src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js';faScript.crossOrigin='anonymous';document.head.appendChild(faScript);</script>
    <!-- Filters (static for now) -->
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 mb-8 flex flex-col md:flex-row md:items-center gap-4">
        <div class="flex-1 flex gap-2">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search classes..." class="block w-full pl-12 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
            </div>
        </div>
        <div class="flex gap-2 flex-1">
            <select id="departmentFilter" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                <option value="">All Departments</option>
                <option value="cs">Computer Science</option>
                <option value="math">Mathematics</option>
                <option value="physics">Physics</option>
            </select>
            <select id="statusFilter" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="completed">Completed</option>
            </select>
            <select id="semesterFilter" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                <option value="">All Semesters</option>
                <option value="fall2024">Fall 2024</option>
                <option value="spring2024">Spring 2024</option>
                <option value="summer2024">Summer 2024</option>
            </select>
        </div>
    </div>
    </div>
    <!-- Classes Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-7" id="classesGrid">
        @forelse($classes as $cls)
            <div class="relative bg-white border-l-4 border-green-500 shadow-xl rounded-2xl p-6 flex flex-col justify-between hover:shadow-2xl transition-shadow duration-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-shrink-0 w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900">{{ $cls->course_code }}</h3>
                        <p class="text-base text-gray-600">{{ $cls->class_name }}</p>
                        <div class="flex gap-2 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-50 text-green-700">Level: {{ $cls->level }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-50 text-green-700">Students: {{ $cls->students->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $cls->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $cls->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <a href="/lecturer/classes/{{ $cls->id }}" class="bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-green-700 transition">View</a>
                        <button onclick="editClass('{{ $cls->id }}')" class="bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-300 transition">Edit</button>
                        <button onclick="deleteClass('{{ $cls->id }}')" class="bg-red-100 text-red-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-red-200 transition">Delete</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-400 py-8">No classes found.</div>
        @endforelse
    </div>
</div>
@endif
<!-- Add/Edit Class Modal -->
<div id="addClassModal" class="fixed inset-0 bg-gray-700 bg-opacity-40 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto p-6 border border-gray-200 shadow-2xl rounded-2xl bg-white">
        <div class="mt-2">
            <h3 class="text-xl font-bold text-gray-900 mb-4" id="modalTitle">Add New Class</h3>
            <form id="addClassForm" class="space-y-4">
                <input type="hidden" name="classId" id="classId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Code</label>
                    <input type="text" name="course_code" id="course_code" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="e.g., CS101">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Name</label>
                    <input type="text" name="class_name" id="class_name" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="e.g., Introduction to Programming">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department" id="department" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select Department</option>
                        <option value="cs">Computer Science</option>
                        <option value="math">Mathematics</option>
                        <option value="physics">Physics</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <select name="level" id="level" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select Level</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room/Schedule</label>
                    <input type="text" name="schedule" id="schedule" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="e.g., Mon, Wed, Fri 9:00 AM">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" id="description" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="e.g., Room 101">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"><span id="modalSubmitText">Add Class</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const lecturer = JSON.parse(localStorage.getItem('lecturer') || '{}');
const lecturerId = lecturer.id;
let editingClassId = null;

function showToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}
function showSpinner(show = true) {
    window.dispatchEvent(new CustomEvent('spinner', { detail: { show } }));
}

function fetchClasses() {
    if (!lecturerId) return;
    showSpinner(true);
    axios.get(`/api/lecturer/classes?lecturer_id=${lecturerId}`)
        .then(res => {
            renderClasses(res.data.data);
            updateStats(res.data.data);
        })
        .catch((err) => {
            showToast('Failed to load classes', 'error');
            console.error('API error:', err);
            const grid = document.getElementById('classesGrid');
            grid.innerHTML = '<div class="col-span-3 text-center text-red-400 py-8">Failed to load classes. Please try again later.</div>';
        })
        .finally(() => showSpinner(false));
}

function renderClasses(classes) {
    const grid = document.getElementById('classesGrid');
    grid.innerHTML = '';
    if (!classes.length) {
        grid.innerHTML = '<div class="col-span-3 text-center text-gray-400 py-8">No classes found.</div>';
        return;
    }
    classes.forEach(cls => {
        const studentCount = cls.student_count !== undefined ? cls.student_count : '-';
        grid.innerHTML += `
        <div class="relative bg-white border-l-4 border-green-500 shadow-lg rounded-xl p-5 mb-2 flex flex-col justify-between hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">${cls.course_code}</h3>
                    <p class="text-sm text-gray-600">${cls.class_name}</p>
                    <div class="flex gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Level: ${cls.level}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Students: ${studentCount}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ${cls.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        ${cls.is_active ? 'Active' : 'Inactive'}
                    </span>
                </div>
                <div class="flex gap-2">
                    <button onclick="viewClass('${cls.id}')" class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-lg hover:bg-green-700 transition">View</button>
                    <button onclick="editClass('${cls.id}')" class="bg-gray-200 text-gray-700 text-xs font-semibold px-3 py-1 rounded-lg hover:bg-gray-300 transition">Edit</button>
                    <button onclick="deleteClass('${cls.id}')" class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-lg hover:bg-red-200 transition">Delete</button>
                </div>
            </div>
        </div>`;
    });
}

function updateStats(classes) {
    const stats = [
        { label: 'Total Classes', value: classes.length, color: 'blue', extra: '' },
        { label: 'Active Classes', value: classes.filter(c => c.is_active).length, color: 'green', extra: 'Currently running' },
        { label: 'Total Students', value: '-', color: 'purple', extra: 'Across all classes' },
        { label: 'Avg. Attendance', value: '-', color: 'orange', extra: '+3% this week' },
    ];
    const statsDiv = document.getElementById('stats-cards');
    statsDiv.innerHTML = stats.map(s => `
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-3 sm:p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-${s.color}-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-${s.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
                    </div>
                </div>
                <div class="ml-2 sm:ml-3">
                    <p class="text-xs font-medium text-gray-500">${s.label}</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900">${s.value}</p>
                    <p class="text-xs text-${s.color}-600">${s.extra}</p>
                </div>
            </div>
        </div>
    `).join('');
}

function openModal(editId = null) {
    document.getElementById('addClassModal').classList.remove('hidden');
    if (editId) {
        editingClassId = editId;
        document.getElementById('modalTitle').textContent = 'Edit Class';
        document.getElementById('modalSubmitText').textContent = 'Update Class';
        // Fetch class data and fill form
        showSpinner(true);
        axios.get(`/api/lecturer/classes?lecturer_id=${lecturerId}`)
            .then(res => {
                const cls = res.data.data.find(c => c.id == editId);
                if (cls) {
                    document.getElementById('classId').value = cls.id;
                    document.getElementById('course_code').value = cls.course_code;
                    document.getElementById('class_name').value = cls.class_name;
                    document.getElementById('department').value = cls.department || '';
                    document.getElementById('schedule').value = cls.schedule || '';
                    document.getElementById('description').value = cls.description || '';
                }
            })
            .finally(() => showSpinner(false));
    } else {
        editingClassId = null;
        document.getElementById('modalTitle').textContent = 'Add New Class';
        document.getElementById('modalSubmitText').textContent = 'Add Class';
        document.getElementById('addClassForm').reset();
        document.getElementById('classId').value = '';
    }
}

function closeModal() {
    document.getElementById('addClassModal').classList.add('hidden');
}

function viewClass(classId) {
    window.location.href = `/lecturer/classes/${classId}`;
}

function editClass(classId) {
    openModal(classId);
}

function deleteClass(classId) {
    if (!confirm('Are you sure you want to delete this class?')) return;
    showSpinner(true);
    axios.delete(`/api/lecturer/classes/${classId}`)
        .then(() => {
            showToast('Class deleted');
            fetchClasses();
        })
        .catch(() => showToast('Failed to delete class', 'error'))
        .finally(() => showSpinner(false));
}

document.getElementById('addClassForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showSpinner(true);
    const data = {
        class_name: document.getElementById('class_name').value,
        course_code: document.getElementById('course_code').value,
        schedule: document.getElementById('schedule').value,
        description: document.getElementById('description').value,
        lecturer_id: lecturerId,
        pin: Math.random().toString(36).substr(2, 6).toUpperCase(), // Generate random PIN
        level: document.getElementById('level').value,
        department: 'Computer Science',
    };
    if (editingClassId) {
        axios.put(`/api/lecturer/classes/${editingClassId}`, data)
            .then(() => {
                showToast('Class updated');
                closeModal();
                fetchClasses();
            })
            .catch(() => showToast('Failed to update class', 'error'))
            .finally(() => showSpinner(false));
    } else {
        axios.post('/api/lecturer/classes', data)
            .then(() => {
                showToast('Class added');
                closeModal();
                fetchClasses();
            })
            .catch(() => showToast('Failed to add class', 'error'))
            .finally(() => showSpinner(false));
    }
});

// Close modal when clicking outside

document.getElementById('addClassModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

function exportClasses() {
    showToast('Export not implemented yet', 'info');
}

// Initial fetch
fetchClasses();
</script>
@endsection 