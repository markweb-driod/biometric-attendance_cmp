@extends('layouts.superadmin')

@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('page-description', 'Monitor and export attendance for all classes and levels')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-green-500 rounded-lg px-6 py-3 shadow mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Attendance Management</h2>
            <p class="text-white text-base opacity-90">Monitor, search, and export all attendance records and sessions</p>
        </div>
        <div class="flex gap-2 mt-3 md:mt-0">
            <a href="{{ route('superadmin.attendance.audit.export') }}" class="px-4 py-2 bg-white text-green-600 border border-green-600 rounded-lg font-medium hover:bg-green-50 hover:text-green-700 transition shadow">Export CSV</a>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="stats-cards">
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Total Attendance</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-attendance">-</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Present</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-present">-</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Biometric</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-biometric">-</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Last Attendance</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-last">-</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <select id="classFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                <option value="">All Classes</option>
                <!-- Populate with classes -->
            </select>
            <select id="levelFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                <option value="">All Levels</option>
                <option value="100">100 Level</option>
                <option value="200">200 Level</option>
                <option value="300">300 Level</option>
                <option value="400">400 Level</option>
            </select>
            <input type="date" id="dateFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
            <input type="text" id="searchInput" placeholder="Search students..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    </tr>
                </thead>
                <tbody id="attendanceTable" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="6" class="text-center text-gray-400 py-8">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchAttendanceStats();
    fetchAttendance();
    document.getElementById('searchInput').addEventListener('input', fetchAttendance);
    document.getElementById('classFilter').addEventListener('change', fetchAttendance);
    document.getElementById('levelFilter').addEventListener('change', fetchAttendance);
    document.getElementById('dateFilter').addEventListener('change', fetchAttendance);
});

function fetchAttendanceStats() {
    axios.get('/api/superadmin/attendance/stats')
        .then(res => {
            const stats = res.data;
            document.getElementById('kpi-attendance').textContent = stats.total ?? '-';
            document.getElementById('kpi-present').textContent = stats.present ?? '-';
            document.getElementById('kpi-biometric').textContent = stats.biometric ?? '-';
            document.getElementById('kpi-last').textContent = stats.last_attendance ? new Date(stats.last_attendance).toLocaleString() : '-';
        })
        .catch(err => {
            document.getElementById('kpi-attendance').textContent = '-';
            document.getElementById('kpi-present').textContent = '-';
            document.getElementById('kpi-biometric').textContent = '-';
            document.getElementById('kpi-last').textContent = '-';
            showToast('Failed to load attendance stats', 'error');
            console.error('KPI stats error:', err);
        });
}

function fetchAttendance() {
    const table = document.getElementById('attendanceTable');
    table.innerHTML = '<tr><td colspan="6" class="text-center text-gray-400 py-8">Loading...</td></tr>';
    const search = document.getElementById('searchInput').value;
    const classId = document.getElementById('classFilter').value;
    const level = document.getElementById('levelFilter').value;
    const date = document.getElementById('dateFilter').value;
    let url = '/api/superadmin/attendance?';
    if (search) url += 'search=' + encodeURIComponent(search) + '&';
    if (classId) url += 'class_id=' + encodeURIComponent(classId) + '&';
    if (level) url += 'level=' + encodeURIComponent(level) + '&';
    if (date) url += 'date=' + encodeURIComponent(date);
    axios.get(url)
        .then(res => {
            const records = res.data.data;
            if (!records.length) {
                table.innerHTML = '<tr><td colspan="6" class="text-center text-gray-400 py-8">No attendance records found.</td></tr>';
                return;
            }
            table.innerHTML = '';
            records.forEach(r => {
                table.innerHTML += `
                <tr>
                    <td class="px-3 py-2">${r.student_name || ''}</td>
                    <td class="px-3 py-2">${r.class_name || ''}</td>
                    <td class="px-3 py-2">${r.level || ''}</td>
                    <td class="px-3 py-2">${r.captured_at || ''}</td>
                    <td class="px-3 py-2">${r.status || ''}</td>
                    <td class="px-3 py-2">${r.method || ''}</td>
                </tr>`;
            });
            fetchAttendanceStats();
        })
        .catch(err => {
            table.innerHTML = '<tr><td colspan="6" class="text-center text-red-400 py-8">Failed to load attendance records.</td></tr>';
            console.error('API error:', err);
        });
}
</script> 