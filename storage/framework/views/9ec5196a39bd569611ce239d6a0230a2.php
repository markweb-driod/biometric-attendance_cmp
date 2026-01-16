<?php $__env->startSection('title', 'Start Attendance'); ?>
<?php $__env->startSection('page-title', 'Start Attendance'); ?>
<?php $__env->startSection('page-description', 'Enable, monitor, and manage attendance sessions for your classes'); ?>

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
const storageBaseUrl = '<?php echo e(asset("storage")); ?>';
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
    if (!lecturerId) {
        showToast('No lecturer ID found. Please login again.', 'error');
        return;
    }
    
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
        .catch((error) => {
            console.error('Error fetching data:', error);
            showToast('Failed to load data', 'error');
        })
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
    Confirmations.closeSession(() => {
        showSpinner(true);
        axios.put(`/api/lecturer/attendance-sessions/${sessionId}`, { close: true })
            .then(() => {
                showToast('Session closed successfully');
                fetchClassesAndSessions();
            })
            .catch(() => showToast('Failed to close session', 'error'))
            .finally(() => showSpinner(false));
    });
}

function regenerateCode(sessionId) {
    Confirmations.regenerateCode(() => {
        showSpinner(true);
        axios.put(`/api/lecturer/attendance-sessions/${sessionId}`, { regenerate_code: true })
            .then(() => {
                showToast('Code regenerated successfully');
                fetchClassesAndSessions();
            })
            .catch(() => showToast('Failed to regenerate code', 'error'))
            .finally(() => showSpinner(false));
    });
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
                students.map(s => `<div class="flex items-center gap-2 mb-1"><img src="${storageBaseUrl}/${s.image_path}" class="w-6 h-6 rounded-full border"><span>${s.full_name} (${s.matric_number})</span><span class="text-xs text-gray-400">${formatTime(s.captured_at)}</span></div>`).join('');
        })
        .catch(() => { div.innerHTML = '<div class="text-xs text-red-400">Failed to load students.</div>'; });
}

fetchClassesAndSessions();
setInterval(fetchClassesAndSessions, 30000); // Refresh every 30s
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\attendance_new.blade.php ENDPATH**/ ?>