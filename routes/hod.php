<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HodAuthController;
use App\Http\Controllers\HodDashboardController;
use App\Http\Controllers\HodCourseMonitoringController;
use App\Http\Controllers\HodStudentMonitoringController;
use App\Http\Controllers\HodExamEligibilityController;
use App\Http\Controllers\HodAuditController;
use App\Http\Controllers\HodStudentManagementController;
use App\Http\Controllers\HodLecturerManagementController;
use App\Http\Controllers\CourseAssignmentController;

// HOD Authentication Routes
Route::prefix('hod')->name('hod.')->group(function () {
    // Public routes (no authentication required)
    // Login now goes through unified login at /login
    Route::get('/login', function() {
        return redirect('/login');
    })->name('login');
    Route::post('/logout', [HodAuthController::class, 'logout'])->name('logout');
    
    // Protected routes (require HOD authentication)
    Route::middleware(['auth:hod', 'hod.role', 'hod.session'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [HodDashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard-stats', [HodDashboardController::class, 'getDashboardStats'])->name('api.dashboard-stats');
        
        // Search
        Route::get('/search', [HodDashboardController::class, 'search'])->name('search');
        
        // Notifications
        Route::get('/api/notifications', [HodDashboardController::class, 'getNotifications'])->name('api.notifications');
        Route::post('/api/notifications/read', [HodDashboardController::class, 'markNotificationAsRead'])->name('api.notifications.read');
        Route::post('/api/notifications/read-all', [HodDashboardController::class, 'markAllNotificationsAsRead'])->name('api.notifications.read-all');
        
        // Session management
        Route::post('/api/ping', function () {
            return response()->json(['status' => 'ok', 'timestamp' => now()]);
        })->name('api.ping');
        Route::get('/api/live-activity', [HodDashboardController::class, 'getLiveActivity'])->name('api.live-activity');
        Route::get('/api/attendance-chart', [HodDashboardController::class, 'getAttendanceChart'])->name('api.attendance-chart');
        
        // Course Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/courses', [HodCourseMonitoringController::class, 'index'])->name('courses');
            Route::get('/courses/chart/{chartType}', [HodCourseMonitoringController::class, 'showChart'])->name('courses.chart');
            Route::get('/api/courses/performance', [HodCourseMonitoringController::class, 'getCoursePerformanceData'])->name('api.courses.performance');
            Route::get('/api/courses/trends', [HodCourseMonitoringController::class, 'getWeeklyTrends'])->name('api.courses.trends');
            Route::get('/api/courses/lecturers', [HodCourseMonitoringController::class, 'getLecturerMetrics'])->name('api.courses.lecturers');
            Route::get('/api/courses/analysis', [HodCourseMonitoringController::class, 'getPerformanceAnalysis'])->name('api.courses.analysis');
            Route::post('/api/courses/export', [HodCourseMonitoringController::class, 'exportData'])->name('api.courses.export');
            Route::post('/api/courses/clear-cache', [HodCourseMonitoringController::class, 'clearCache'])->name('api.courses.clear-cache');
        });

        // Student Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/students', [HodStudentMonitoringController::class, 'index'])->name('students');
            Route::get('/students/chart/{chartType}', [HodStudentMonitoringController::class, 'showChart'])->name('students.chart');
            Route::get('/api/students/attendance', [HodStudentMonitoringController::class, 'getStudentAttendanceData'])->name('api.students.attendance');
            Route::get('/api/students/trends', [HodStudentMonitoringController::class, 'getWeeklyTrends'])->name('api.students.trends');
            Route::get('/api/students/metrics', [HodStudentMonitoringController::class, 'getStudentMetrics'])->name('api.students.metrics');
            Route::get('/api/students/chart-data', [HodStudentMonitoringController::class, 'getCourseAttendanceChart'])->name('api.students.chart-data');
            Route::get('/api/students/analysis', [HodStudentMonitoringController::class, 'getAttendanceAnalysis'])->name('api.students.analysis');
            Route::get('/api/students/course-summary', [HodStudentMonitoringController::class, 'getCourseSummary'])->name('api.students.course-summary');
            Route::get('/api/students/at-risk', [HodStudentMonitoringController::class, 'getAtRiskStudents'])->name('api.students.at-risk');
            Route::get('/api/students/top-performers', [HodStudentMonitoringController::class, 'getTopPerformers'])->name('api.students.top-performers');
            Route::post('/api/students/export', [HodStudentMonitoringController::class, 'exportData'])->name('api.students.export');
                   Route::post('/api/students/clear-cache', [HodStudentMonitoringController::class, 'clearCache'])->name('api.students.clear-cache');
               });

               // Two-Factor Authentication
               Route::prefix('two-factor')->name('two-factor.')->group(function () {
                   Route::get('/verify', [\App\Http\Controllers\HodTwoFactorController::class, 'show'])->name('show');
                   Route::post('/verify', [\App\Http\Controllers\HodTwoFactorController::class, 'verify'])->name('verify');
                   Route::post('/resend', [\App\Http\Controllers\HodTwoFactorController::class, 'resend'])->name('resend');
                   Route::post('/clear', [\App\Http\Controllers\HodTwoFactorController::class, 'clear'])->name('clear');
               });

               // Exam Eligibility Management
               Route::prefix('exam')->name('exam.')->group(function () {
                   Route::get('/eligibility', [HodExamEligibilityController::class, 'index'])->name('eligibility');
                   Route::get('/configuration', function () {
                       $hod = Auth::guard('hod')->user();
                       return view('hod.exam.configuration', compact('hod'));
                   })->name('eligibility.configuration');
                   Route::get('/api/eligibility/data', [HodExamEligibilityController::class, 'getEligibilityData'])->name('api.eligibility.data');
                   Route::get('/api/eligibility/stats', [HodExamEligibilityController::class, 'getEligibilityStats'])->name('api.eligibility.stats');
                   Route::get('/api/eligibility/at-risk', [HodExamEligibilityController::class, 'getAtRiskStudents'])->name('api.eligibility.at-risk');
                   Route::post('/api/eligibility/override', [HodExamEligibilityController::class, 'overrideEligibility'])->name('api.eligibility.override');
                   Route::post('/api/eligibility/bulk-override', [HodExamEligibilityController::class, 'bulkOverrideEligibility'])->name('api.eligibility.bulk-override');
                   Route::post('/api/eligibility/prepare-waiver', [HodExamEligibilityController::class, 'prepareWaiver'])->name('api.eligibility.prepare-waiver');
                   Route::post('/api/eligibility/execute-waiver', [HodExamEligibilityController::class, 'executeWaiver'])->middleware('require.2fa')->name('api.eligibility.execute-waiver');
                   Route::post('/api/eligibility/calculate', [HodExamEligibilityController::class, 'calculateEligibility'])->middleware('require.2fa')->name('api.eligibility.calculate');
                   Route::post('/api/configuration', [HodExamEligibilityController::class, 'saveConfiguration'])->name('api.configuration');
                   Route::get('/api/eligibility/export', [HodExamEligibilityController::class, 'exportData'])->name('api.eligibility.export');
               });

               // Audit Logs & Compliance
               Route::prefix('audit')->name('audit.')->group(function () {
                   Route::get('/', [HodAuditController::class, 'index'])->name('index');
                   Route::get('/api/logs', [HodAuditController::class, 'getAuditLogs'])->name('api.logs');
                   Route::get('/api/logs/{id}', [HodAuditController::class, 'getAuditLogDetails'])->name('api.logs.details');
                   Route::get('/api/stats', [HodAuditController::class, 'getAuditStats'])->name('api.stats');
                   Route::get('/api/security-alerts', [HodAuditController::class, 'getSecurityAlerts'])->name('api.security-alerts');
                   Route::get('/api/compliance-report', [HodAuditController::class, 'getComplianceReport'])->name('api.compliance-report');
                   Route::get('/api/export', [HodAuditController::class, 'exportData'])->name('api.export');
               });

               // Student Management
               Route::prefix('management/students')->name('management.students.')->group(function () {
                   Route::get('/', [HodStudentManagementController::class, 'index'])->name('index');
                   Route::get('/api/list', [HodStudentManagementController::class, 'getStudents'])->name('api.list');
                   Route::get('/api/statistics', [HodStudentManagementController::class, 'getStatistics'])->name('api.statistics');
                   Route::get('/api/show/{id}', [HodStudentManagementController::class, 'show'])->name('api.show');
                   Route::post('/api/upload', [HodStudentManagementController::class, 'bulkUpload'])->name('api.upload');
                   Route::get('/api/template', [HodStudentManagementController::class, 'downloadTemplate'])->name('api.template');
                   Route::put('/api/update/{id}', [HodStudentManagementController::class, 'update'])->name('api.update');
                   Route::delete('/api/delete/{id}', [HodStudentManagementController::class, 'destroy'])->name('api.delete');
                   Route::get('/{id}', [HodStudentManagementController::class, 'showDetails'])->name('show');
               });

               // Lecturer Management
               Route::prefix('management/lecturers')->name('management.lecturers.')->group(function () {
                   Route::get('/', [HodLecturerManagementController::class, 'index'])->name('index');
                   Route::get('/api/list', [HodLecturerManagementController::class, 'getLecturers'])->name('api.list');
                   Route::get('/api/statistics', [HodLecturerManagementController::class, 'getStatistics'])->name('api.statistics');
                   Route::get('/api/show/{id}', [HodLecturerManagementController::class, 'show'])->name('api.show');
                   Route::post('/api/upload', [HodLecturerManagementController::class, 'bulkUpload'])->name('api.upload');
                   Route::get('/api/template', [HodLecturerManagementController::class, 'downloadTemplate'])->name('api.template');
                   Route::put('/api/update/{id}', [HodLecturerManagementController::class, 'update'])->name('api.update');
                   Route::delete('/api/delete/{id}', [HodLecturerManagementController::class, 'destroy'])->name('api.delete');
                   Route::get('/{id}', [HodLecturerManagementController::class, 'showDetails'])->name('show');
               });

               // Course Assignment Management
               Route::prefix('management/course-assignment')->name('management.course-assignment.')->group(function () {
                   Route::get('/', [CourseAssignmentController::class, 'index'])->name('index');
                   Route::get('/api/lecturers', [CourseAssignmentController::class, 'getLecturers'])->name('api.lecturers');
                   Route::get('/api/courses', [CourseAssignmentController::class, 'getCourses'])->name('api.courses');
                   Route::get('/api/dropdown-data', [CourseAssignmentController::class, 'getDropdownData'])->name('api.dropdown-data');
                   Route::get('/api/lecturer/{lecturerId}/courses', [CourseAssignmentController::class, 'getLecturerCourses'])->name('api.lecturer.courses');
                   Route::get('/api/course/{courseId}/lecturers', [CourseAssignmentController::class, 'getCourseLecturers'])->name('api.course.lecturers');
                   Route::post('/api/assign', [CourseAssignmentController::class, 'assignCourses'])->name('api.assign');
                   Route::post('/api/unassign', [CourseAssignmentController::class, 'unassignCourses'])->name('api.unassign');
                   Route::post('/api/bulk-assign', [CourseAssignmentController::class, 'bulkAssign'])->name('api.bulk-assign');
                   Route::post('/api/create-course', [CourseAssignmentController::class, 'createCourse'])->name('api.create-course');
               });

               // Profile
               Route::get('/profile', function () {
                   $hod = Auth::guard('hod')->user();
                   return view('hod.profile.index', compact('hod'));
               })->name('profile');

               // Settings
               Route::get('/settings', function () {
                   $hod = Auth::guard('hod')->user();
                   return view('hod.settings.index', compact('hod'));
               })->name('settings');
           });
       });
