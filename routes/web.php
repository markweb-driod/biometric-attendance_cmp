<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperadminAuthController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\SuperadminDashboardController;
use App\Http\Controllers\SuperadminClassController;
use App\Http\Controllers\UnifiedAuthController;
use App\Http\Controllers\SuperadminTwoFactorController;
use App\Http\Controllers\HodTwoFactorController;
use App\Http\Controllers\LecturerTwoFactorController; // Added this line as per instruction's implied context


use App\Http\Controllers\Lecturer\AttendanceGradingController;
use App\Http\Controllers\VenueController;

Route::redirect('/superadmin', '/superadmin/dashboard');

Route::get('/', [\App\Http\Controllers\OptimizedLandingController::class, 'studentLanding']);

// Student routes
Route::get('/student', [\App\Http\Controllers\OptimizedLandingController::class, 'studentLanding']);

// Unified Login Routes
Route::get('/login', [UnifiedAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UnifiedAuthController::class, 'login']);

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/verify-otp/{otp_id?}', [\App\Http\Controllers\PasswordResetController::class, 'verifyOtpForm'])->name('password.verify-otp');
Route::post('/verify-otp', [\App\Http\Controllers\PasswordResetController::class, 'verifyOtp'])->name('password.verify-otp.submit');
Route::get('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.reset.submit');
Route::post('/resend-otp', [\App\Http\Controllers\PasswordResetController::class, 'resendOtp'])->name('password.resend-otp');

// Redirect old login pages to unified login
Route::get('/superadmin/login', fn() => redirect('/login'));
Route::get('/lecturer', fn() => redirect('/login'));
Route::get('/hod/login', fn() => redirect('/login'));

Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard'])->name('lecturer.dashboard');

Route::post('/lecturer/logout', [LecturerController::class, 'logout'])->name('lecturer.logout');
Route::get('/lecturer/classes', [LecturerController::class, 'classesPage'])->name('lecturer.classes');

Route::get('/lecturer/attendance', [LecturerController::class, 'manageAttendance'])->name('lecturer.attendance.manage');

Route::get('/lecturer/students', [LecturerController::class, 'studentsPage'])->name('lecturer.students');

Route::get('/lecturer/reports', [LecturerController::class, 'reportsPage'])->name('lecturer.reports');

// Lecturer Course Management Routes
Route::middleware(['auth:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::resource('venues', VenueController::class);
    Route::get('/courses', [\App\Http\Controllers\LecturerCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{courseId}', [\App\Http\Controllers\LecturerCourseController::class, 'show'])->name('courses.show');
    Route::get('/api/courses', [\App\Http\Controllers\LecturerCourseController::class, 'getAssignedCourses'])->name('api.courses');
    Route::get('/api/courses/{courseId}', [\App\Http\Controllers\LecturerCourseController::class, 'getCourseDetails'])->name('api.courses.details');
    Route::get('/api/courses/{courseId}/classrooms', [\App\Http\Controllers\LecturerCourseController::class, 'getCourseClassrooms'])->name('api.courses.classrooms');
    Route::get('/api/courses/{courseId}/statistics', [\App\Http\Controllers\LecturerCourseController::class, 'getCourseStatistics'])->name('api.courses.statistics');
    Route::get('/api/available-courses', [\App\Http\Controllers\LecturerCourseController::class, 'getAvailableCourses'])->name('api.available-courses');
});

Route::get('/lecturer/settings', function () {
    return view('lecturer.settings');
});

// Additional lecturer routes for specific functionality
Route::get('/lecturer/attendance/new', function () {
    return view('lecturer.attendance_new');
});

Route::get('/lecturer/reports/generate', function () {
    return view('lecturer.reports_generate');
});

Route::get('/lecturer/classes/{classId}', [App\Http\Controllers\LecturerController::class, 'classDetail'])->name('lecturer.class.detail');

// Lecturer-specific endpoints
// Updated to only return assigned courses
// Route::get('/lecturer/courses', function () {
//     $lecturer = auth('lecturer')->user();
//     if (!$lecturer) {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }
    
//     $courses = $lecturer->courses()
//         ->select(['courses.id', 'courses.course_name', 'courses.course_code'])
//         ->orderBy('courses.course_code')
//         ->get();
    
//     return response()->json([
//         'success' => true,
//         'courses' => $courses
//     ]);
// })->name('lecturer.courses');

Route::get('/lecturer/students/{studentId}', [LecturerController::class, 'studentDetail'])->name('lecturer.student.detail');

Route::get('/lecturer/students/{studentId}/attendance', [LecturerController::class, 'studentAttendance'])->name('lecturer.student.attendance');

Route::post('/lecturer/attendance/start/{classId}', [LecturerController::class, 'startAttendance'])->name('lecturer.attendance.start');
Route::get('/lecturer/attendance/session/{classId}', [LecturerController::class, 'attendanceSession'])->name('lecturer.attendance.session');
Route::post('/lecturer/attendance/session/{sessionId}/mark', [LecturerController::class, 'markAttendance'])->name('lecturer.attendance.mark');
Route::post('/lecturer/attendance/session/{sessionId}/end', [LecturerController::class, 'endAttendance'])->name('lecturer.attendance.end');

Route::get('/api/lecturer/attendance-session-live/{sessionId}', [App\Http\Controllers\LecturerController::class, 'liveAttendanceApi'])->name('lecturer.attendance.live');
Route::get('/api/lecturer/attendance', [App\Http\Controllers\Api\LecturerController::class, 'attendance']);

Route::post('/lecturer/recalibrate-location', [App\Http\Controllers\LecturerController::class, 'updateGeoLocation'])->name('lecturer.recalibrate-location');

// Superadmin Auth - Redirect to unified login
Route::post('/superadmin/logout', [SuperadminAuthController::class, 'logout'])->name('superadmin.logout');

// Superadmin Portal (protected)
Route::middleware(['auth:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/students', function () { return view('superadmin.students'); });
    Route::get('/students/{id}/details', [\App\Http\Controllers\SuperadminStudentController::class, 'details'])->name('superadmin.students.details');
    Route::post('/students/{id}/toggle', [\App\Http\Controllers\SuperadminUserToggleController::class, 'toggleStudent'])->name('superadmin.students.toggle');
    Route::post('/students/bulk-toggle', [\App\Http\Controllers\SuperadminUserToggleController::class, 'bulkToggleStudents'])->name('superadmin.students.bulk-toggle');
    Route::get('/lecturers', function () {
        $lecturers = \App\Models\Lecturer::with(['user:id,full_name,email', 'department:id,name'])
            ->select(['id', 'user_id', 'staff_id', 'phone', 'department_id', 'title', 'is_active', 'created_at'])
            ->orderBy('staff_id')
            ->paginate(20);

        // Cache departments for 1 hour
        $departments = \Cache::remember('departments_list', 3600, function () {
            return \App\Models\Department::select('id', 'name')->where('is_active', true)->get();
        });
        
        return view('superadmin.lecturers', compact('lecturers', 'departments'));
    })->name('superadmin.lecturers');
    Route::get('/lecturers/{id}/details', [\App\Http\Controllers\SuperadminLecturerController::class, 'details'])->name('superadmin.lecturers.details');
    Route::post('/lecturers/{id}/toggle', [\App\Http\Controllers\SuperadminUserToggleController::class, 'toggleLecturer'])->name('superadmin.lecturers.toggle');
    Route::post('/lecturers/bulk-toggle', [\App\Http\Controllers\SuperadminUserToggleController::class, 'bulkToggleLecturers'])->name('superadmin.lecturers.bulk-toggle');
    Route::get('/classes', [SuperadminClassController::class, 'index']);
    Route::get('/attendance', function () { return view('superadmin.attendance'); });
    Route::get('/reports', [\App\Http\Controllers\SuperadminReportsController::class, 'index'])->name('superadmin.reports');
    Route::get('/reports/data', [\App\Http\Controllers\SuperadminReportsController::class, 'getReportsData'])->name('superadmin.reports.data');
    Route::get('/reports/export', [\App\Http\Controllers\SuperadminReportsController::class, 'exportCsv'])->name('superadmin.reports.export');
    Route::get('/reports/trends', [\App\Http\Controllers\SuperadminReportsController::class, 'getTrends'])->name('superadmin.reports.trends');
    Route::get('/settings', [\App\Http\Controllers\SystemSettingsController::class, 'index'])->name('superadmin.settings');
    Route::get('/classes/{id}', function($id) {
        $class = \App\Models\Classroom::with('lecturer')->findOrFail($id);
        return view('superadmin.class_detail', compact('class'));
    });
    Route::get('/attendance-audit', [App\Http\Controllers\SuperadminAttendanceController::class, 'index'])->name('superadmin.attendance.audit');
    Route::get('/attendance-audit/export', [App\Http\Controllers\SuperadminAttendanceController::class, 'exportCsv'])->name('superadmin.attendance.audit.export');
    Route::get('/attendance-audit/stats', [App\Http\Controllers\SuperadminAttendanceController::class, 'dashboardStats'])->name('superadmin.attendance.audit.stats');
    Route::get('/attendance-audit/live-activity', [App\Http\Controllers\SuperadminAttendanceController::class, 'liveActivity'])->name('superadmin.attendance.audit.live');
});

// Superadmin face verification config
Route::middleware(['auth:superadmin'])->group(function () {
    Route::get('/superadmin/settings/face-config', [\App\Http\Controllers\SuperadminDashboardController::class, 'faceConfigForm'])->name('superadmin.face-config');
    Route::post('/superadmin/settings/face-config', [\App\Http\Controllers\SuperadminDashboardController::class, 'updateFaceConfig'])->name('superadmin.face-config.update');
});

Route::middleware(['auth:superadmin'])->group(function () {
    Route::post('/superadmin/settings/test-facepp', [\App\Http\Controllers\SuperadminDashboardController::class, 'testFacePP']);
});

Route::middleware(['auth:superadmin'])->group(function () {
    Route::post('/superadmin/students/{id}/enable-face-registration', [\App\Http\Controllers\SuperadminStudentController::class, 'enableFaceRegistration'])->name('superadmin.students.enable-face-registration');
    Route::post('/superadmin/students/{id}/disable-face-registration', [\App\Http\Controllers\SuperadminStudentController::class, 'disableFaceRegistration'])->name('superadmin.students.disable-face-registration');
    Route::post('/superadmin/students/enable-face-registration-all', [\App\Http\Controllers\SuperadminStudentController::class, 'enableFaceRegistrationAll']);
    Route::post('/superadmin/students/disable-face-registration-all', [\App\Http\Controllers\SuperadminStudentController::class, 'disableFaceRegistrationAll']);
});

Route::middleware(['auth:superadmin'])->group(function () {
    Route::get('/superadmin/students/face-registration-status', [\App\Http\Controllers\SuperadminStudentController::class, 'faceRegistrationStatus']);
});

// Superadmin Lecturer Management Web Routes
Route::middleware(['auth:superadmin'])->group(function () {
    Route::post('/superadmin/lecturers', [\App\Http\Controllers\SuperadminLecturerController::class, 'storeWeb'])->name('superadmin.lecturers.store');
    Route::put('/superadmin/lecturers/{id}', [\App\Http\Controllers\SuperadminLecturerController::class, 'updateWeb'])->name('superadmin.lecturers.update');
    Route::delete('/superadmin/lecturers/{id}', [\App\Http\Controllers\SuperadminLecturerController::class, 'destroyWeb'])->name('superadmin.lecturers.destroy');
    Route::post('/superadmin/lecturers/bulk-upload', [\App\Http\Controllers\SuperadminLecturerController::class, 'bulkUploadWeb'])->name('superadmin.lecturers.bulk-upload');
    Route::post('/superadmin/lecturers/{id}/update-password', [\App\Http\Controllers\SuperadminLecturerController::class, 'updatePassword'])->name('superadmin.lecturers.update-password');
});

// Superadmin System Management Routes
Route::middleware(['auth:superadmin'])->group(function () {
    // System Settings - Redirect to unified settings
    Route::get('/superadmin/system-settings', function() {
        return redirect()->route('superadmin.settings');
    });
    Route::post('/superadmin/system-settings/update', [\App\Http\Controllers\SystemSettingsController::class, 'update'])->name('superadmin.system-settings.update');
    Route::post('/superadmin/system-settings/reset', [\App\Http\Controllers\SystemSettingsController::class, 'resetToDefaults'])->name('superadmin.system-settings.reset');
    Route::post('/superadmin/system-settings/test-email', [\App\Http\Controllers\SystemSettingsController::class, 'testEmail'])->name('superadmin.system-settings.test-email');
    Route::post('/superadmin/system-settings/test-face-api', [\App\Http\Controllers\SystemSettingsController::class, 'testFaceAPI'])->name('superadmin.system-settings.test-face-api');
    Route::post('/superadmin/system-settings/test-confidence-threshold', [\App\Http\Controllers\SystemSettingsController::class, 'testConfidenceThreshold'])->name('superadmin.system-settings.test-confidence-threshold');
    Route::get('/superadmin/system-settings/export', [\App\Http\Controllers\SystemSettingsController::class, 'exportSettings'])->name('superadmin.system-settings.export');
    Route::post('/superadmin/system-settings/import', [\App\Http\Controllers\SystemSettingsController::class, 'importSettings'])->name('superadmin.system-settings.import');
    
    // System Administration
    Route::post('/superadmin/clear-cache', [\App\Http\Controllers\SuperadminController::class, 'clearCache'])->name('superadmin.clear-cache');
    Route::get('/superadmin/performance-metrics', [\App\Http\Controllers\SuperadminController::class, 'getPerformanceMetrics'])->name('superadmin.performance-metrics');
    Route::get('/superadmin/system-logs', [\App\Http\Controllers\SuperadminController::class, 'getSystemLogs'])->name('superadmin.system-logs');
    Route::post('/superadmin/system-maintenance', [\App\Http\Controllers\SuperadminController::class, 'systemMaintenance'])->name('superadmin.system-maintenance');
    Route::get('/superadmin/export-data', [\App\Http\Controllers\SuperadminController::class, 'exportData'])->name('superadmin.export-data');
    
    
    
    // Academic Structure Management
    Route::get('/superadmin/academic-structure', [\App\Http\Controllers\AcademicStructureController::class, 'index'])->name('superadmin.academic-structure');
    Route::get('/superadmin/academic-structure/dropdown-data', [\App\Http\Controllers\AcademicStructureController::class, 'getDropdownData'])->name('superadmin.academic-structure.dropdown-data');
    
    // Departments
    Route::get('/superadmin/departments', [\App\Http\Controllers\AcademicStructureController::class, 'getDepartments'])->name('superadmin.departments');
    Route::post('/superadmin/departments', [\App\Http\Controllers\AcademicStructureController::class, 'createDepartment'])->name('superadmin.departments.create');
    Route::put('/superadmin/departments/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'updateDepartment'])->name('superadmin.departments.update');
    Route::delete('/superadmin/departments/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'deleteDepartment'])->name('superadmin.departments.delete');
    
    // Academic Levels
    Route::get('/superadmin/academic-levels', [\App\Http\Controllers\AcademicStructureController::class, 'getAcademicLevels'])->name('superadmin.academic-levels');
    Route::post('/superadmin/academic-levels', [\App\Http\Controllers\AcademicStructureController::class, 'createAcademicLevel'])->name('superadmin.academic-levels.create');
    Route::put('/superadmin/academic-levels/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'updateAcademicLevel'])->name('superadmin.academic-levels.update');
    Route::delete('/superadmin/academic-levels/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'deleteAcademicLevel'])->name('superadmin.academic-levels.delete');
    
    // Courses
    Route::get('/superadmin/courses', [\App\Http\Controllers\AcademicStructureController::class, 'getCourses'])->name('superadmin.courses');
    Route::post('/superadmin/courses', [\App\Http\Controllers\AcademicStructureController::class, 'createCourse'])->name('superadmin.courses.create');
    Route::put('/superadmin/courses/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'updateCourse'])->name('superadmin.courses.update');
    Route::delete('/superadmin/courses/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'deleteCourse'])->name('superadmin.courses.delete');
    
    // Classrooms
    Route::get('/superadmin/classrooms', [\App\Http\Controllers\AcademicStructureController::class, 'getClassrooms'])->name('superadmin.classrooms');
    Route::post('/superadmin/classrooms', [\App\Http\Controllers\AcademicStructureController::class, 'createClassroom'])->name('superadmin.classrooms.create');
    Route::put('/superadmin/classrooms/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'updateClassroom'])->name('superadmin.classrooms.update');
    Route::delete('/superadmin/classrooms/{id}', [\App\Http\Controllers\AcademicStructureController::class, 'deleteClassroom'])->name('superadmin.classrooms.delete');
    
    // Reporting System
    Route::get('/superadmin/reporting', [\App\Http\Controllers\ReportingController::class, 'index'])->name('superadmin.reporting');
    Route::get('/superadmin/reporting/dashboard-data', [\App\Http\Controllers\ReportingController::class, 'getDashboardData'])->name('superadmin.reporting.dashboard-data');
    Route::post('/superadmin/reporting/attendance-report', [\App\Http\Controllers\ReportingController::class, 'generateAttendanceReport'])->name('superadmin.reporting.attendance-report');
    Route::post('/superadmin/reporting/student-performance-report', [\App\Http\Controllers\ReportingController::class, 'generateStudentPerformanceReport'])->name('superadmin.reporting.student-performance-report');
    Route::post('/superadmin/reporting/system-analytics-report', [\App\Http\Controllers\ReportingController::class, 'generateSystemAnalyticsReport'])->name('superadmin.reporting.system-analytics-report');
    
    // Audit Trail System
    Route::get('/superadmin/audit-trail', [\App\Http\Controllers\AuditTrailController::class, 'index'])->name('superadmin.audit-trail');
    Route::get('/superadmin/audit-trail/logs', [\App\Http\Controllers\AuditTrailController::class, 'getAuditLogs'])->name('superadmin.audit-trail.logs');
    Route::post('/superadmin/audit-trail/export', [\App\Http\Controllers\AuditTrailController::class, 'exportAuditLogs'])->name('superadmin.audit-trail.export');
    Route::get('/superadmin/audit-trail/system-health', [\App\Http\Controllers\AuditTrailController::class, 'getSystemHealth'])->name('superadmin.audit-trail.system-health');
    
    // Session Monitoring System
    Route::get('/superadmin/session-monitoring', [\App\Http\Controllers\SessionMonitoringController::class, 'index'])->name('superadmin.session-monitoring');
    Route::get('/superadmin/session-monitoring/live-sessions', [\App\Http\Controllers\SessionMonitoringController::class, 'liveSessions'])->name('superadmin.session-monitoring.live');
    Route::get('/superadmin/session-monitoring/session-history', [\App\Http\Controllers\SessionMonitoringController::class, 'sessionHistory'])->name('superadmin.session-monitoring.history');
    Route::get('/superadmin/session-monitoring/session/{id}/details', [\App\Http\Controllers\SessionMonitoringController::class, 'sessionDetails'])->name('superadmin.session-monitoring.details');
    Route::post('/superadmin/session-monitoring/session/{id}/terminate', [\App\Http\Controllers\SessionMonitoringController::class, 'terminateSession'])->name('superadmin.session-monitoring.terminate');
    Route::get('/superadmin/session-monitoring/statistics', [\App\Http\Controllers\SessionMonitoringController::class, 'statistics'])->name('superadmin.session-monitoring.statistics');
    
    // Course Assignment Management
    Route::prefix('superadmin/course-assignment')->name('superadmin.course-assignment.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CourseAssignmentController::class, 'index'])->name('index');
        Route::get('/api/lecturers', [\App\Http\Controllers\CourseAssignmentController::class, 'getLecturers'])->name('api.lecturers');
        Route::get('/api/courses', [\App\Http\Controllers\CourseAssignmentController::class, 'getCourses'])->name('api.courses');
        Route::get('/api/dropdown-data', [\App\Http\Controllers\CourseAssignmentController::class, 'getDropdownData'])->name('api.dropdown-data');
        Route::get('/api/lecturer/{lecturerId}/courses', [\App\Http\Controllers\CourseAssignmentController::class, 'getLecturerCourses'])->name('api.lecturer.courses');
        Route::get('/api/course/{courseId}/lecturers', [\App\Http\Controllers\CourseAssignmentController::class, 'getCourseLecturers'])->name('api.course.lecturers');
        Route::post('/api/assign', [\App\Http\Controllers\CourseAssignmentController::class, 'assignCourses'])->name('api.assign');
        Route::post('/api/unassign', [\App\Http\Controllers\CourseAssignmentController::class, 'unassignCourses'])->name('api.unassign');
        Route::post('/api/bulk-assign', [\App\Http\Controllers\CourseAssignmentController::class, 'bulkAssign'])->name('api.bulk-assign');
        Route::post('/api/create-course', [\App\Http\Controllers\CourseAssignmentController::class, 'createCourse'])->name('api.create-course');
    });
    
    // Enhanced System Audit Logs (Advanced Monitoring)
    Route::get('/superadmin/audit-logs', [\App\Http\Controllers\SuperadminAuditController::class, 'index'])->name('superadmin.audit.index');
    Route::get('/superadmin/audit-logs/{id}', [\App\Http\Controllers\SuperadminAuditController::class, 'show'])->name('superadmin.audit.show');
    Route::get('/superadmin/audit-logs/api/logs', [\App\Http\Controllers\SuperadminAuditController::class, 'getAuditLogs'])->name('superadmin.audit.api.logs');
    Route::get('/superadmin/audit-logs/api/stats', [\App\Http\Controllers\SuperadminAuditController::class, 'getAuditStats'])->name('superadmin.audit.api.stats');
    Route::get('/superadmin/audit-logs/api/charts', [\App\Http\Controllers\SuperadminAuditController::class, 'getActivityCharts'])->name('superadmin.audit.api.charts');
    Route::get('/superadmin/audit-logs/api/export', [\App\Http\Controllers\SuperadminAuditController::class, 'exportData'])->name('superadmin.audit.api.export');
    
    // Emergency Controls
    Route::get('/superadmin/emergency-controls', [\App\Http\Controllers\EmergencyController::class, 'index'])->name('superadmin.emergency-controls');
    Route::post('/superadmin/emergency-controls/shutdown', [\App\Http\Controllers\EmergencyController::class, 'emergencyShutdown'])->name('superadmin.emergency-controls.shutdown');
    Route::post('/superadmin/emergency-controls/recovery', [\App\Http\Controllers\EmergencyController::class, 'systemRecovery'])->name('superadmin.emergency-controls.recovery');
    Route::post('/superadmin/emergency-controls/clear-caches', [\App\Http\Controllers\EmergencyController::class, 'clearAllCaches'])->name('superadmin.emergency-controls.clear-caches');
    Route::post('/superadmin/emergency-controls/restart-services', [\App\Http\Controllers\EmergencyController::class, 'restartServices'])->name('superadmin.emergency-controls.restart-services');
    
    // API Key Management (All routes require 2FA)
    Route::middleware('require.2fa')->prefix('superadmin/api-keys')->name('superadmin.api-keys.')->group(function() {
        Route::get('/', [\App\Http\Controllers\SuperadminApiKeyController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\SuperadminApiKeyController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\SuperadminApiKeyController::class, 'store'])->name('store');
        Route::get('/documentation', [\App\Http\Controllers\SuperadminApiKeyController::class, 'documentation'])->name('documentation');
        Route::get('/{id}', [\App\Http\Controllers\SuperadminApiKeyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\SuperadminApiKeyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\SuperadminApiKeyController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\SuperadminApiKeyController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/regenerate', [\App\Http\Controllers\SuperadminApiKeyController::class, 'regenerate'])->name('regenerate');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\SuperadminApiKeyController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{id}/usage-stats', [\App\Http\Controllers\SuperadminApiKeyController::class, 'usageStats'])->name('usage-stats');
        Route::get('/{id}/logs', [\App\Http\Controllers\SuperadminApiKeyController::class, 'logs'])->name('logs');
    });
});

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');


Route::get('/student/landing', [App\Http\Controllers\StudentController::class, 'landing'])->name('student.landing');
Route::post('/student/validate', [App\Http\Controllers\StudentController::class, 'validateStudent']);
Route::get('/student/register-face', function () {
    return view('student.register_face');
})->name('student.register-face.form');
Route::post('/student/register-face', [\App\Http\Controllers\StudentController::class, 'registerFace'])->name('student.register-face');
Route::get('/student/attendance-capture', function () {
    return view('student.attendance_capture');
})->name('student.attendance-capture');

// Test route for face verification debugging
Route::get('/test-face-verification', function () {
    $apiKey = \App\Models\SystemSetting::getValue('faceplusplus_api_key', 'NOT_SET');
    $apiSecret = \App\Models\SystemSetting::getValue('faceplusplus_api_secret', 'NOT_SET');
    $confidenceThreshold = \App\Models\SystemSetting::getValue('face_confidence_threshold', 75);
    
    return response()->json([
        'api_key_configured' => $apiKey !== 'NOT_SET' && !empty($apiKey),
        'api_secret_configured' => $apiSecret !== 'NOT_SET' && !empty($apiSecret),
        'confidence_threshold' => $confidenceThreshold,
        'face_verification_enabled' => \App\Models\SystemSetting::getValue('face_recognition_enabled', true),
        'message' => $apiKey === 'NOT_SET' || empty($apiKey) ? 'Face++ API credentials not configured. Please add FACEPLUSPLUS_API_KEY and FACEPLUSPLUS_API_SECRET to your .env file.' : 'API credentials are configured.'
    ]);
});
Route::get('/student/register-face', function () {
    $currentSettings = [
        'enable_browser_face_detection' => \App\Models\SystemSetting::getValue('enable_browser_face_detection', true),
        'browser_face_confidence_threshold' => \App\Models\SystemSetting::getValue('browser_face_confidence_threshold', 0.5),
        'browser_face_allow_loose_alignment' => \App\Models\SystemSetting::getValue('browser_face_allow_loose_alignment', true),
    ];
    return view('student.register_face', compact('currentSettings'));
})->name('student.register-face.page');

// Attendance Monitoring Dashboard Routes
Route::get('/attendance-monitoring', [\App\Http\Controllers\AttendanceMonitoringController::class, 'index'])->name('attendance-monitoring.dashboard');
Route::get('/attendance-monitoring/demo', function() { return view('attendance-monitoring.demo'); })->name('attendance-monitoring.demo');
Route::get('/attendance-monitoring/filter', [\App\Http\Controllers\AttendanceMonitoringController::class, 'filter'])->name('attendance-monitoring.filter');
Route::get('/api/attendance-monitoring/chart-data', [\App\Http\Controllers\AttendanceMonitoringController::class, 'getChartData'])->name('attendance-monitoring.chart-data');
Route::post('/api/student/validate-matric', [\App\Http\Controllers\StudentController::class, 'validateMatricForFaceRegistration']);

// Student Face Registration Management (Superadmin)
Route::middleware(['auth:superadmin'])->prefix('superadmin/students')->group(function () {
    Route::get('face-registration-management', [App\Http\Controllers\SuperadminStudentController::class, 'faceRegistrationManagement'])->name('superadmin.students.face-registration-management');
    Route::get('face-registration-management/data', [App\Http\Controllers\SuperadminStudentController::class, 'faceRegistrationData'])->name('superadmin.students.face-registration-data');
    Route::post('face-registration-management/update-image/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'updateFaceImage'])->name('superadmin.students.update-face-image');
    Route::delete('face-registration-management/delete-image/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'deleteFaceImage'])->name('superadmin.students.delete-face-image');
    Route::post('face-registration-management/enable/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'enableFaceRegistration'])->name('superadmin.students.face-registration-management.enable');
    Route::post('face-registration-management/disable/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'disableFaceRegistration'])->name('superadmin.students.face-registration-management.disable');
    Route::post('face-registration-management/bulk-action', [App\Http\Controllers\SuperadminStudentController::class, 'bulkFaceRegistrationAction'])->name('superadmin.students.bulk-face-registration-action');
    Route::get('face-registration-management/export', [App\Http\Controllers\SuperadminStudentController::class, 'exportFaceRegistration'])->name('superadmin.students.export-face-registration');
});

Route::prefix('superadmin')->middleware(['auth:superadmin'])->group(function () {
    Route::get('/2fa', [SuperadminTwoFactorController::class, 'show'])->name('superadmin.2fa.show');
    Route::post('/2fa', [SuperadminTwoFactorController::class, 'verify'])->name('superadmin.2fa.verify');
    Route::get('/2fa/setup', [SuperadminTwoFactorController::class, 'setup'])->name('superadmin.2fa.setup');
    Route::post('/2fa/confirm', [SuperadminTwoFactorController::class, 'confirm'])->name('superadmin.2fa.confirm');
    Route::post('/2fa/disable', [SuperadminTwoFactorController::class, 'disable'])->name('superadmin.2fa.disable');
});



Route::prefix('lecturer')->middleware(['auth:lecturer'])->group(function () {
    Route::get('/2fa', [LecturerTwoFactorController::class, 'show'])->name('lecturer.2fa.show');
    Route::post('/2fa', [LecturerTwoFactorController::class, 'verify'])->name('lecturer.2fa.verify');
    Route::get('/2fa/setup', [LecturerTwoFactorController::class, 'setup'])->name('lecturer.2fa.setup');
    Route::post('/2fa/confirm', [LecturerTwoFactorController::class, 'confirm'])->name('lecturer.2fa.confirm');
    Route::post('/2fa/disable', [LecturerTwoFactorController::class, 'disable'])->name('lecturer.2fa.disable');
    // Attendance Grading Routes
    Route::get('/lecturer/grading', [AttendanceGradingController::class, 'index'])->name('lecturer.grading.index');
    Route::get('/lecturer/grading/{classroom}', [AttendanceGradingController::class, 'show'])->name('lecturer.grading.show');
    Route::post('/lecturer/grading/{classroom}/rules', [AttendanceGradingController::class, 'updateRules'])->name('lecturer.grading.updateRules');
    Route::get('/lecturer/grading/{classroom}/export', [AttendanceGradingController::class, 'export'])->name('lecturer.grading.export');
    Route::get('/lecturer/grading/{classroom}/distribution', [AttendanceGradingController::class, 'getGradeDistribution'])->name('lecturer.grading.distribution');
    Route::post('/lecturer/grading/{classroom}/bulk-override', [AttendanceGradingController::class, 'bulkOverride'])->name('lecturer.grading.bulkOverride');
    Route::post('/lecturer/grading/{classroom}/notify', [AttendanceGradingController::class, 'sendNotifications'])->name('lecturer.grading.notify');
    Route::get('/lecturer/grading/{classroom}/export-pdf', [AttendanceGradingController::class, 'exportPdf'])->name('lecturer.grading.exportPdf');

    // Attendance Session Controls
    Route::post('/lecturer/attendance/recalibrate/{id}', [\App\Http\Controllers\Api\AttendanceSessionController::class, 'updateSessionLocation'])->name('lecturer.attendance.recalibrate');
});
