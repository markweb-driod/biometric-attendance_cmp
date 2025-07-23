@extends('layouts.lecturer')

@section('title', 'Start Attendance')
@section('page-title', 'Start Attendance')
@section('page-description', 'Enable, monitor, and manage attendance sessions for your classes')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Manage Attendance Sessions</h2>
        <p class="text-sm text-gray-600 mb-4">Enable attendance for your classes, set time windows, and monitor active sessions in real time.</p>
        <div id="sessionsContainer">
            <!-- Filled by JS -->
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const lecturer = JSON.parse(localStorage.getItem('lecturer') || '{}');
const lecturerId = lecturer.id;
let allClasses = [];
let allSessions = [];

function showToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}
function showSpinner(show = true) {
    window.dispatchEvent(new CustomEvent('spinner', { detail: { show } }));
}

function fetchClassesAndSessions() {
    if (!lecturerId) return;
    showSpinner(true);
    axios.get(`/api/lecturer/classes?lecturer_id=${lecturerId}`)
        .then(res => {
            allClasses = res.data.data;
            return axios.get(`/api/lecturer/attendance-sessions?lecturer_id=${lecturerId}`);
        })
        .then(res => {
            allSessions = res.data.data;
            renderSessions();
        })
        .catch(() => showToast('Failed to load data', 'error'))
        .finally(() => showSpinner(false));
}

function renderSessions() {
    const container = document.getElementById('sessionsContainer');
    container.innerHTML = '';
    if (!allClasses.length) {
        container.innerHTML = '<div class="text-center text-gray-400 py-8">No classes found.</div>';
        return;
    }
    allClasses.forEach(cls => {
        const session = allSessions.find(s => s.classroom_id === cls.id && s.is_active);
        container.innerHTML += `
        <div class="mb-8 border-b pb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">${cls.course_code} - ${cls.class_name}</h3>
                    <p class="text-xs text-gray-500">${cls.schedule || ''}</p>
                </div>
                <div class="flex items-center gap-2 mt-2 sm:mt-0">
                    ${session ? `
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        <button onclick="closeSession(${session.id})" class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded-lg hover:bg-red-200">Close</button>
                    ` : `
                        <button onclick="openSessionModal(${cls.id})" class="px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">Enable Attendance</button>
                    `}
                </div>
            </div>
            ${session ? renderActiveSession(session, cls) : ''}
        </div>`;
    });
}

function renderActiveSession(session, cls) {
    const now = new Date();
    const end = session.end_time ? new Date(session.end_time) : null;
    let countdown = '';
    if (end) {
        const ms = end - now;
        if (ms > 0) {
            const min = Math.floor(ms / 60000);
            const sec = Math.floor((ms % 60000) / 1000);
            countdown = `<span class='ml-2 text-xs text-gray-500'>Ends in ${min}m ${sec}s</span>`;
        } else {
            countdown = `<span class='ml-2 text-xs text-red-500'>Session ended</span>`;
        }
    }
    return `
        <div class="mt-2">
            <div class="flex items-center gap-4 mb-2">
                <span class="text-xs font-medium text-gray-700">Attendance Code:</span>
                <span class="font-mono text-base bg-gray-100 px-2 py-1 rounded">${session.code}</span>
                <button onclick="regenerateCode(${session.id})" class="px-2 py-1 bg-gray-200 text-xs rounded hover:bg-gray-300">Regenerate</button>
                ${countdown}
            </div>
            <div class="flex items-center gap-4 mb-2">
                <span class="text-xs font-medium text-gray-700">Window:</span>
                <span class="text-xs">${formatTime(session.start_time)} - ${session.end_time ? formatTime(session.end_time) : 'Open'}</span>
            </div>
            <div class="mb-2">
                <button onclick="viewSessionStudents(${session.id})" class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-lg hover:bg-purple-200">View Attendance</button>
            </div>
            <div id="students-${session.id}" class="mt-2"></div>
        </div>
    `;
}

function formatTime(dt) {
    if (!dt) return '';
    const d = new Date(dt);
    return d.toLocaleString();
}

function openSessionModal(classId) {
    const start = prompt('Enter start time (YYYY-MM-DD HH:MM, leave blank for now):');
    const end = prompt('Enter end time (YYYY-MM-DD HH:MM, leave blank for open session):');
    const startTime = start ? new Date(start.replace(' ', 'T')) : new Date();
    const endTime = end ? new Date(end.replace(' ', 'T')) : null;
    showSpinner(true);
    axios.post('/api/lecturer/attendance-sessions', {
        classroom_id: classId,
        lecturer_id: lecturerId,
        start_time: startTime.toISOString(),
        end_time: endTime ? endTime.toISOString() : null
    })
    .then(() => {
        showToast('Attendance enabled');
        fetchClassesAndSessions();
    })
    .catch(() => showToast('Failed to enable attendance', 'error'))
    .finally(() => showSpinner(false));
}

function closeSession(sessionId) {
    if (!confirm('Close this attendance session?')) return;
    showSpinner(true);
    axios.put(`/api/lecturer/attendance-sessions/${sessionId}`, { close: true })
        .then(() => {
            showToast('Session closed');
            fetchClassesAndSessions();
        })
        .catch(() => showToast('Failed to close session', 'error'))
        .finally(() => showSpinner(false));
}

function regenerateCode(sessionId) {
    showSpinner(true);
    axios.put(`/api/lecturer/attendance-sessions/${sessionId}`, { regenerate_code: true })
        .then(() => {
            showToast('Code regenerated');
            fetchClassesAndSessions();
        })
        .catch(() => showToast('Failed to regenerate code', 'error'))
        .finally(() => showSpinner(false));
}

function viewSessionStudents(sessionId) {
    const div = document.getElementById(`students-${sessionId}`);
    div.innerHTML = 'Loading...';
    axios.get(`/api/lecturer/attendance-sessions/${sessionId}/students`)
        .then(res => {
            const students = res.data.data;
            if (!students.length) {
                div.innerHTML = '<div class="text-xs text-gray-400">No students have marked attendance yet.</div>';
                return;
            }
            div.innerHTML = '<div class="mb-2 text-xs font-semibold text-gray-700">Students Present:</div>' +
                students.map(s => `<div class="flex items-center gap-2 mb-1"><img src="/storage/${s.image_path}" class="w-6 h-6 rounded-full border"><span>${s.full_name} (${s.matric_number})</span><span class="text-xs text-gray-400">${formatTime(s.captured_at)}</span></div>`).join('');
        })
        .catch(() => { div.innerHTML = '<div class="text-xs text-red-400">Failed to load students.</div>'; });
}

fetchClassesAndSessions();
setInterval(fetchClassesAndSessions, 30000); // Refresh every 30s
</script>
@endsection 