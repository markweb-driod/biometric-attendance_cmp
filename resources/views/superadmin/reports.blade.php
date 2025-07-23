@extends('layouts.superadmin')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('page-description', 'View and export attendance analytics for all classes and levels')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-green-500 rounded-lg px-6 py-3 shadow mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Reports & Analytics</h2>
            <p class="text-white text-base opacity-90">View, filter, and export attendance analytics for all classes and levels</p>
        </div>
        <div class="flex gap-2 mt-3 md:mt-0">
            <a href="#" class="px-4 py-2 bg-white text-green-600 border border-green-600 rounded-lg font-medium hover:bg-green-50 hover:text-green-700 transition shadow">Export CSV</a>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Attendance Rate</div>
            <div class="text-2xl font-extrabold text-green-900" id="kpi-attendance-rate">-</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Total Sessions</div>
            <div class="text-2xl font-extrabold text-green-900" id="kpi-total-sessions">-</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Absentees</div>
            <div class="text-2xl font-extrabold text-green-900" id="kpi-absentees">-</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Top Class</div>
            <div class="text-2xl font-extrabold text-green-900" id="kpi-top-class">-</div>
        </div>
    </div>
    <!-- Filter Bar -->
    <form class="bg-white rounded-xl shadow flex flex-col md:flex-row gap-2 items-center px-4 py-3 mb-4">
        <select class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            <option value="">All Classes</option>
            <!-- Populate with classes -->
        </select>
        <select class="w-full md:w-40 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            <option value="">All Levels</option>
            <option value="100">100 Level</option>
            <option value="200">200 Level</option>
            <option value="300">300 Level</option>
            <option value="400">400 Level</option>
        </select>
        <input type="date" class="w-full md:w-40 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        <input type="text" placeholder="Search students..." class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
    </form>
    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow p-2 sm:p-3 mb-3 w-full">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Class</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Level</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Date & Time</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Method</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr><td colspan="6" class="text-center text-gray-400 py-8">[Report Data Placeholder]</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
function updateKPIs(kpis) {
    document.getElementById('kpi-attendance-rate').textContent = (kpis.attendance_rate ?? '-') + '%';
    document.getElementById('kpi-total-sessions').textContent = kpis.total_sessions ?? '-';
    document.getElementById('kpi-absentees').textContent = kpis.absentees ?? '-';
    document.getElementById('kpi-top-class').textContent = kpis.top_class ?? '-';
}

function fetchReportsData() {
    // TODO: Add filter values here
    axios.get('/api/superadmin/reports')
        .then(res => {
            if (res.data && res.data.kpis) {
                updateKPIs(res.data.kpis);
            }
            // TODO: Update table data as well
        })
        .catch(() => {
            updateKPIs({});
        });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchReportsData();
    // TODO: Add event listeners for filters to call fetchReportsData on change
});
</script>
@endsection 