@extends('layouts.lecturer')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-description', 'Generate and view attendance reports')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Reports</h1>
            <p class="text-sm text-gray-600 mt-1">Generate and view attendance reports</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
            <button onclick="exportCSV()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-base font-semibold rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-100 p-4 mb-4 flex flex-col sm:flex-row gap-3 items-center">
        <select id="classFilter" class="block w-full sm:w-60 px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></select>
        <input type="date" id="dateFilter" class="block w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        <input type="text" id="searchFilter" placeholder="Search student or class..." class="block w-full sm:w-64 px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
        <select id="statusFilter" class="block w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <option value="">All Statuses</option>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
        </select>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4" id="stats-cards">
        <!-- Filled by JS -->
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-lg shadow border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Attendance Records</h3>
            <span id="record-count" class="text-xs text-gray-500"></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Student</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Class</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date & Time</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceTable" class="bg-white divide-y divide-gray-100">
                    <!-- Filled by JS -->
                </tbody>
            </table>
            <div id="pagination" class="flex justify-center items-center gap-2 py-4"></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const lecturer = JSON.parse(localStorage.getItem('lecturer') || '{}');
const lecturerId = lecturer.id;
let allAttendance = [];
let allClasses = [];

function showToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}
function showSpinner(show = true) {
    window.dispatchEvent(new CustomEvent('spinner', { detail: { show } }));
}

let currentPage = 1;
const perPage = 7;
let filteredAttendance = [];

function fetchClassesAndAttendance() {
    if (!lecturerId) return;
    showSpinner(true);
    axios.get(`/api/lecturer/classes?lecturer_id=${lecturerId}`)
        .then(res => {
            allClasses = res.data.data;
            populateClassFilter(allClasses);
            // Fetch attendance for all classes
            const classIds = allClasses.map(c => c.id);
            return axios.get('/api/lecturer/attendance', { params: { class_ids: classIds } });
        })
        .then(res => {
            allAttendance = res.data.data;
            filteredAttendance = allAttendance;
            renderAttendance(allAttendance);
            updateStats(allAttendance);
        })
        .catch(() => showToast('Failed to load attendance', 'error'))
        .finally(() => showSpinner(false));
}

function populateClassFilter(classes) {
    const select = document.getElementById('classFilter');
    select.innerHTML = '<option value="">All Classes</option>';
    classes.forEach(cls => {
        select.innerHTML += `<option value="${cls.id}">${cls.course_code} - ${cls.class_name}</option>`;
    });
}

function renderAttendance(records) {
    const table = document.getElementById('attendanceTable');
    table.innerHTML = '';
    document.getElementById('record-count').textContent = records.length ? `${records.length} records` : '';
    if (!records.length) {
        table.innerHTML = '<tr><td colspan="5" class="text-center text-gray-400 py-8">No attendance records found.</td></tr>';
        document.getElementById('pagination').innerHTML = '';
        return;
    }
    let rowNum = 1 + (currentPage - 1) * perPage;
    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const pageRecords = records.slice(start, end);
    pageRecords.forEach(rec => {
        table.innerHTML += `
        <tr class="hover:bg-blue-50 transition">
            <td class="px-3 sm:px-4 py-4 whitespace-nowrap text-xs text-gray-500">${rowNum++}</td>
            <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">${rec.student_name || '-'}</div>
                <div class="text-xs text-gray-500">${rec.matric_number || '-'}</div>
            </td>
            <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 font-medium">${rec.class_code || '-'}</div>
                <div class="text-xs text-gray-500">${rec.class_name || '-'}</div>
            </td>
            <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${rec.captured_at || '-'}</div>
            </td>
            <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ${rec.status === 'present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'}">${rec.status ? rec.status.charAt(0).toUpperCase() + rec.status.slice(1) : '-'}</span>
            </td>
        </tr>`;
    });
    renderPagination(records.length);
}

function renderPagination(total) {
    const pagination = document.getElementById('pagination');
    const totalPages = Math.ceil(total / perPage);
    if (totalPages <= 1) { pagination.innerHTML = ''; return; }
    let html = '';
    if (currentPage > 1) {
        html += `<button class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300" onclick="gotoPage(${currentPage - 1})">Prev</button>`;
    }
    for (let i = 1; i <= totalPages; i++) {
        html += `<button class="px-3 py-1 rounded ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200'} mx-1" onclick="gotoPage(${i})">${i}</button>`;
    }
    if (currentPage < totalPages) {
        html += `<button class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300" onclick="gotoPage(${currentPage + 1})">Next</button>`;
    }
    pagination.innerHTML = html;
}

function gotoPage(page) {
    currentPage = page;
    renderAttendance(filteredAttendance);
}

function updateStats(records) {
    const total = records.length;
    const students = new Set(records.map(r => r.student_name)).size;
    const classes = new Set(records.map(r => r.class_code)).size;
    const avgAttendance = total ? Math.round((total / (students * classes || 1)) * 100) : 0;
    const stats = [
        { label: 'Total Records', value: total, color: 'blue', icon: 'fa-database', gradient: 'from-blue-400 to-blue-600' },
        { label: 'Avg. Attendance', value: avgAttendance + '%', color: 'green', icon: 'fa-chart-line', gradient: 'from-green-400 to-green-600' },
        { label: 'Classes Covered', value: classes, color: 'purple', icon: 'fa-chalkboard-teacher', gradient: 'from-purple-400 to-purple-600' },
        { label: 'Students Tracked', value: students, color: 'orange', icon: 'fa-user-graduate', gradient: 'from-orange-400 to-orange-600' },
    ];
    const statsDiv = document.getElementById('stats-cards');
    statsDiv.innerHTML = stats.map(s => `
        <div class="bg-gradient-to-br ${s.gradient} rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas ${s.icon}"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10">${s.value}</div>
            <div class="text-xs font-semibold uppercase tracking-wider z-10">${s.label}</div>
        </div>
    `).join('');
}

function exportCSV() {
    if (!allAttendance.length) return showToast('No data to export', 'info');
    const headers = ['Student Name', 'Matric Number', 'Class Code', 'Class Name', 'Date & Time'];
    const rows = allAttendance.map(r => [
        r.student_name || '',
        r.matric_number || '',
        r.class_code || '',
        r.class_name || '',
        r.captured_at || ''
    ]);
    let csv = headers.join(',') + '\n';
    rows.forEach(row => { csv += row.map(v => '"' + v.replace(/"/g, '""') + '"').join(',') + '\n'; });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'attendance_report.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('CSV exported!');
}

document.getElementById('classFilter').addEventListener('change', filterAttendance);
document.getElementById('dateFilter').addEventListener('change', filterAttendance);
document.getElementById('searchFilter').addEventListener('input', filterAttendance);
document.getElementById('statusFilter').addEventListener('change', filterAttendance);

function filterAttendance() {
    let filtered = allAttendance;
    const classId = document.getElementById('classFilter').value;
    const date = document.getElementById('dateFilter').value;
    const search = document.getElementById('searchFilter').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    if (classId) filtered = filtered.filter(r => String(r.class_id) === String(classId));
    if (date) filtered = filtered.filter(r => r.captured_at && r.captured_at.startsWith(date));
    if (search) filtered = filtered.filter(r =>
        (r.student_name && r.student_name.toLowerCase().includes(search)) ||
        (r.class_name && r.class_name.toLowerCase().includes(search)) ||
        (r.class_code && r.class_code.toLowerCase().includes(search)) ||
        (r.matric_number && r.matric_number.toLowerCase().includes(search))
    );
    if (status) filtered = filtered.filter(r => r.status && r.status.toLowerCase() === status);
    filteredAttendance = filtered;
    currentPage = 1;
    renderAttendance(filtered);
    updateStats(filtered);
}

fetchClassesAndAttendance();

// FontAwesome for icons in KPI cards
const faScript = document.createElement('script');
faScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js';
faScript.crossOrigin = 'anonymous';
document.head.appendChild(faScript);
</script>
@endsection 