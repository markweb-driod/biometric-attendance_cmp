<?php $__env->startSection('title', 'Reports'); ?>
<?php $__env->startSection('page-title', 'Reports'); ?>
<?php $__env->startSection('page-description', 'Generate and view attendance reports'); ?>

<?php $__env->startSection('content'); ?>
<!-- Flash Messages -->
<?php if(session('success')): ?>
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span><?php echo e(session('success')); ?></span>
    <button onclick="closeFlash('flash-success')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span><?php echo e(session('error')); ?></span>
    <button onclick="closeFlash('flash-error')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

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

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- Weekly Trend Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Weekly Attendance Trend</h3>
            <canvas id="weeklyTrendChart" height="150"></canvas>
        </div>
        
        <!-- Class Distribution Chart -->
        <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Attendance by Class</h3>
            <canvas id="classDistributionChart" height="150"></canvas>
        </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
    if (!lecturerId) {
        showToast('No lecturer ID found. Please login again.', 'error');
        return;
    }
    
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
        .catch((error) => {
            console.error('Error fetching data:', error);
            showToast('Failed to load attendance', 'error');
        })
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
    
    Confirmations.custom(
        'Export Data',
        `Are you sure you want to export ${allAttendance.length} attendance records to CSV?`,
        'Export CSV',
        'bg-green-600 hover:bg-green-700',
        () => {
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
            showToast('CSV exported successfully!');
        }
    );
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

// Chart.js charts initialization
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($weeklyTrend)): ?>
    // Weekly Trend Chart
    const weeklyCtx = document.getElementById('weeklyTrendChart').getContext('2d');
    const weeklyChart = new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($weeklyTrend), 15, 512) ?>,
            datasets: [{
                label: 'Attendance Count',
                data: <?php echo json_encode(array_values($weeklyTrend), 15, 512) ?>,
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: { weight: 'bold' },
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: { size: 11 }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 11 }
                    }
                }
            }
        }
    });
    <?php endif; ?>

    <?php if(isset($classAttendanceData)): ?>
    // Class Distribution Chart
    const classCtx = document.getElementById('classDistributionChart').getContext('2d');
    const classChart = new Chart(classCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($classAttendanceData->toArray(), 'name'), 512) ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($classAttendanceData->toArray(), 'count'), 512) ?>,
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(14, 165, 233, 0.8)'
                ],
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(168, 85, 247)',
                    'rgb(251, 146, 60)',
                    'rgb(236, 72, 153)',
                    'rgb(14, 165, 233)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    titleFont: { weight: 'bold' },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\reports.blade.php ENDPATH**/ ?>