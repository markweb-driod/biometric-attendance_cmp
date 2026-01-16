@extends('layouts.app')

@section('title', 'Attendance Monitoring Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Dashboard Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Attendance Monitoring Dashboard</h1>
            <p class="mt-2 text-gray-600">Real-time attendance tracking and analytics</p>
        </div>

        <!-- Dashboard Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Bar Chart Component -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-attendance-monitoring.charts.attendance-bar-chart 
                    :chart-id="'attendance-bar-chart'"
                    :title="'Daily/Weekly Attendance Comparison'"
                    :height="400"
                />
            </div>

            <!-- Additional dashboard components will go here -->
            <!-- Attendance Table Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Attendance Logs</h3>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" id="filter-search" placeholder="Search Student..." class="px-3 py-2 border rounded-md text-sm">
                        <select id="filter-status" class="px-3 py-2 border rounded-md text-sm">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                        </select>
                        <input type="date" id="filter-date" class="px-3 py-2 border rounded-md text-sm">
                        <button onclick="fetchAttendance()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Filter</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Rows loaded via AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-4">
                    <button id="prev-btn" class="px-3 py-1 bg-gray-100 rounded text-sm disabled:opacity-50" onclick="changePage(-1)">Previous</button>
                    <span id="page-info" class="text-sm text-gray-600">Page 1</span>
                    <button id="next-btn" class="px-3 py-1 bg-gray-100 rounded text-sm disabled:opacity-50" onclick="changePage(1)">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let currentPage = 1;

    document.addEventListener('DOMContentLoaded', () => {
        fetchAttendance();
    });

    function changePage(delta) {
        currentPage += delta;
        fetchAttendance();
    }

    function fetchAttendance() {
        const search = document.getElementById('filter-search').value;
        const status = document.getElementById('filter-status').value;
        const date = document.getElementById('filter-date').value;
        const tbody = document.getElementById('attendance-table-body');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const pageInfo = document.getElementById('page-info');

        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>';

        axios.get('{{ route("attendance-monitoring.filter") }}', {
            params: { page: currentPage, search, status, date }
        })
        .then(res => {
            const data = res.data;
            tbody.innerHTML = '';
            
            if (data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">No records found</td></tr>';
                return;
            }

            data.data.forEach(item => {
                const dateStr = new Date(item.captured_at).toLocaleString();
                const studentName = item.student?.user?.full_name || 'Unknown';
                const matric = item.student?.matric_number || 'N/A';
                const sessionCode = item.session?.code || 'N/A';
                const courseCode = item.classroom?.course?.course_code || '';
                const statusBadge = item.status === 'present' 
                    ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>'
                    : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>';
                
                const imgSrc = item.image_path ? `/storage/${item.image_path}` : 'https://via.placeholder.com/40';

                const row = `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${dateStr}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${studentName}</div>
                            <div class="text-xs text-gray-500">${matric}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                             ${courseCode} (${sessionCode})
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="${imgSrc}" class="h-10 w-10 rounded-full object-cover border" alt="Face">
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            // Update Pagination logic
            currentPage = data.current_page;
            pageInfo.textContent = `Page ${data.current_page} of ${data.last_page}`;
            prevBtn.disabled = !data.prev_page_url;
            nextBtn.disabled = !data.next_page_url;
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Error loading data</td></tr>';
        });
    }
</script>