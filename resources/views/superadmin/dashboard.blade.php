@extends('layouts.superadmin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Superadmin overview and quick actions')

@section('content')
<div class="container mx-auto p-4 md:p-6">
  <!-- KPI Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col items-center hover:shadow-2xl transition group">
      <div class="mb-2"><svg class="w-8 h-8 text-green-500 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg></div>
      <div class="text-3xl font-bold" id="kpi-students">-</div>
      <div class="text-sm text-gray-500">Total Students</div>
      <div class="text-xs text-green-500" id="kpi-students-trend"></div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col items-center hover:shadow-2xl transition group">
      <div class="mb-2"><svg class="w-8 h-8 text-blue-500 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
      <div class="text-3xl font-bold" id="kpi-lecturers">-</div>
      <div class="text-sm text-gray-500">Total Lecturers</div>
      <div class="text-xs text-blue-500" id="kpi-lecturers-trend"></div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col items-center hover:shadow-2xl transition group">
      <div class="mb-2"><svg class="w-8 h-8 text-purple-500 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></div>
      <div class="text-3xl font-bold" id="kpi-classes">-</div>
      <div class="text-sm text-gray-500">Total Classes</div>
      <div class="text-xs text-purple-500" id="kpi-classes-trend"></div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-5 flex flex-col items-center hover:shadow-2xl transition group">
      <div class="mb-2"><svg class="w-8 h-8 text-orange-500 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
      <div class="text-3xl font-bold" id="kpi-attendance">-</div>
      <div class="text-sm text-gray-500">Attendance Rate</div>
      <div class="text-xs text-orange-500" id="kpi-attendance-trend"></div>
    </div>
  </div>

  <!-- Charts and System Health -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-5">
      <h3 class="text-lg font-semibold mb-4">Attendance Trends</h3>
      <canvas id="attendanceChart" height="120"></canvas>
        </div>
    <div class="bg-white rounded-xl shadow-lg p-5">
      <h3 class="text-lg font-semibold mb-4">System Health</h3>
      <div id="system-health" class="space-y-3"></div>
        </div>
        </div>

  <!-- More Analytics -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-5">
      <h3 class="text-lg font-semibold mb-4">User Growth (Last 7 Days)</h3>
      <canvas id="userGrowthChart" height="120"></canvas>
        </div>
    <div class="bg-white rounded-xl shadow-lg p-5">
      <h3 class="text-lg font-semibold mb-4">Class Distribution</h3>
      <canvas id="classDistributionChart" height="120"></canvas>
    </div>
  </div>

  <!-- Top Performing Classes -->
  <div class="bg-white rounded-xl shadow-lg p-5 mb-6">
    <h3 class="text-lg font-semibold mb-4">Top Performing Classes</h3>
    <div id="top-classes-list" class="space-y-2"></div>
        </div>

  <!-- Quick Actions and Activity Feed -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl shadow-lg p-5">
      <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
      <div class="grid grid-cols-2 gap-4">
        <a href="/superadmin/students/upload" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
          <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          <span class="text-sm font-medium text-green-700">Upload Students</span>
        </a>
        <a href="/superadmin/lecturers/upload" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
          <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          <span class="text-sm font-medium text-blue-700">Upload Lecturers</span>
        </a>
        <a href="/superadmin/classes/create" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
          <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          <span class="text-sm font-medium text-purple-700">Create Class</span>
        </a>
        <a href="/superadmin/reports" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-200">
          <svg class="w-8 h-8 text-orange-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <span class="text-sm font-medium text-orange-700">Generate Report</span>
        </a>
            </div>
        </div>
    <div class="bg-white rounded-xl shadow-lg p-5">
      <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
      <div id="recent-activity" class="space-y-3"></div>
        </div>
    </div>
</div>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Fetch and update dashboard data
async function loadDashboardData() {
  try {
    const response = await fetch('/api/superadmin/dashboard/stats');
    if (!response.ok) throw new Error('Failed to fetch dashboard data');
    const data = await response.json();
    updateKpis(data.kpis);
    updateSystemHealth(data.system_health);
    updateRecentActivity(data.recent_activities);
    updateAttendanceChart(data.attendance_trends);
    updateUserGrowthChart(data.user_growth || []);
    updateClassDistributionChart(data.class_distribution || []);
    updateTopClasses(data.top_classes || []);
  } catch (error) {
    console.error('Dashboard data error:', error);
  }
}

function updateKpis(kpis) {
  document.getElementById('kpi-students').textContent = kpis.students;
  document.getElementById('kpi-lecturers').textContent = kpis.lecturers;
  document.getElementById('kpi-classes').textContent = kpis.classes;
  document.getElementById('kpi-attendance').textContent = kpis.attendance_rate + '%';
  // Optionally, add trend indicators here
}

function updateSystemHealth(health) {
  const container = document.getElementById('system-health');
  container.innerHTML = `
    <div class="flex items-center justify-between p-3 ${health.database === 'healthy' ? 'bg-green-50' : 'bg-red-50'} rounded-lg">
      <div class="flex items-center">
        <div class="w-3 h-3 ${health.database === 'healthy' ? 'bg-green-500' : 'bg-red-500'} rounded-full mr-3"></div>
        <span class="text-sm font-medium">Database</span>
      </div>
      <span class="text-xs ${health.database === 'healthy' ? 'text-green-600' : 'text-red-600'}">${health.database}</span>
    </div>
    <div class="flex items-center justify-between p-3 ${health.activity === 'active' ? 'bg-green-50' : 'bg-yellow-50'} rounded-lg">
      <div class="flex items-center">
        <div class="w-3 h-3 ${health.activity === 'active' ? 'bg-green-500' : 'bg-yellow-500'} rounded-full mr-3"></div>
        <span class="text-sm font-medium">Activity</span>
      </div>
      <span class="text-xs ${health.activity === 'active' ? 'text-green-600' : 'text-yellow-600'}">${health.activity}</span>
    </div>
    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
      <div class="flex items-center">
        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
        <span class="text-sm font-medium">Sessions Today</span>
      </div>
      <span class="text-xs text-blue-600">${health.sessions_today}</span>
    </div>
  `;
}

function updateRecentActivity(activities) {
  const container = document.getElementById('recent-activity');
  if (!activities || !activities.length) {
    container.innerHTML = '<div class="text-gray-400 text-sm">No recent activity.</div>';
    return;
  }
  container.innerHTML = activities.map(activity => `
    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors duration-200">
      <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900">${activity.message}</p>
        <p class="text-xs text-gray-500">${activity.time}</p>
      </div>
    </div>
  `).join('');
}

let attendanceChart;
function updateAttendanceChart(trends) {
  const ctx = document.getElementById('attendanceChart').getContext('2d');
  const labels = trends.map(t => t.date);
  const data = trends.map(t => t.attendances);
  if (attendanceChart) attendanceChart.destroy();
  attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Attendance',
        data: data,
        borderColor: 'rgba(34,197,94,1)',
        backgroundColor: 'rgba(34,197,94,0.1)',
        tension: 0.4,
        fill: true,
        pointRadius: 4,
        pointHoverRadius: 6,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
}

let userGrowthChart;
function updateUserGrowthChart(growth) {
  const ctx = document.getElementById('userGrowthChart').getContext('2d');
  const labels = growth.map(g => g.date);
  const students = growth.map(g => g.students);
  const lecturers = growth.map(g => g.lecturers);
  if (userGrowthChart) userGrowthChart.destroy();
  userGrowthChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Students',
          data: students,
          borderColor: 'rgba(34,197,94,1)',
          backgroundColor: 'rgba(34,197,94,0.1)',
          tension: 0.4,
          fill: true,
        },
        {
          label: 'Lecturers',
          data: lecturers,
          borderColor: 'rgba(59,130,246,1)',
          backgroundColor: 'rgba(59,130,246,0.1)',
          tension: 0.4,
          fill: true,
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } },
      scales: { y: { beginAtZero: true } }
    }
  });
}

let classDistributionChart;
function updateClassDistributionChart(distribution) {
  const ctx = document.getElementById('classDistributionChart').getContext('2d');
  const labels = distribution.map(d => d.level);
  const data = distribution.map(d => d.count);
  if (classDistributionChart) classDistributionChart.destroy();
  classDistributionChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: [
          'rgba(34,197,94,0.7)',
          'rgba(59,130,246,0.7)',
          'rgba(168,85,247,0.7)',
          'rgba(251,191,36,0.7)',
          'rgba(239,68,68,0.7)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true } }
    }
  });
}

function updateTopClasses(classes) {
  const container = document.getElementById('top-classes-list');
  if (!classes || !classes.length) {
    container.innerHTML = '<div class="text-gray-400 text-sm">No data.</div>';
    return;
  }
  container.innerHTML = classes.map((cls, idx) => `
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
      <div class="flex items-center">
        <span class="inline-block w-6 h-6 bg-green-100 text-green-700 font-bold rounded-full flex items-center justify-center mr-3">${idx+1}</span>
        <span class="text-sm font-medium text-gray-900">${cls.name}</span>
        <span class="ml-2 text-xs text-gray-500">(${cls.lecturer})</span>
      </div>
      <span class="text-sm font-bold text-gray-700">${cls.student_count} students</span>
    </div>
  `).join('');
}

// Initial load and auto-refresh
loadDashboardData();
setInterval(loadDashboardData, 30000);
</script>
@endsection 