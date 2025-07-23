<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperadminAuthController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\SuperadminDashboardController;
use App\Http\Controllers\SuperadminClassController;

Route::redirect('/superadmin', '/superadmin/dashboard');

Route::get('/', function () {
    return view('student.attendance_capture');
});

// Student routes
Route::get('/student', function () {
    return view('student_landing');
});

// Lecturer routes
Route::get('/lecturer', function () {
    return view('lecturer_landing');
});

Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard']);

Route::get('/lecturer/classes', [LecturerController::class, 'classesPage']);

Route::get('/lecturer/attendance', [LecturerController::class, 'manageAttendance'])->name('lecturer.attendance.manage');

// Route::get('/lecturer/students', [LecturerController::class, 'studentsPage'])->name('lecturer.students');

Route::get('/lecturer/reports', function () {
    return view('lecturer.reports');
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

Route::get('/lecturer/students/{studentId}', function ($studentId) {
    return view('lecturer.student_detail', ['studentId' => $studentId]);
});

Route::post('/lecturer/attendance/start/{classId}', [LecturerController::class, 'startAttendance'])->name('lecturer.attendance.start');
Route::get('/lecturer/attendance/session/{classId}', [LecturerController::class, 'attendanceSession'])->name('lecturer.attendance.session');
Route::post('/lecturer/attendance/session/{sessionId}/mark', [LecturerController::class, 'markAttendance'])->name('lecturer.attendance.mark');
Route::post('/lecturer/attendance/session/{sessionId}/end', [LecturerController::class, 'endAttendance'])->name('lecturer.attendance.end');

Route::get('/api/lecturer/attendance-session-live/{sessionId}', [App\Http\Controllers\LecturerController::class, 'liveAttendanceApi'])->name('lecturer.attendance.live');
Route::get('/api/lecturer/attendance', [App\Http\Controllers\Api\LecturerController::class, 'attendance']);

Route::post('/lecturer/recalibrate-location', [App\Http\Controllers\LecturerController::class, 'updateGeoLocation'])->name('lecturer.recalibrate-location');

// Superadmin Auth
Route::get('/superadmin/login', [SuperadminAuthController::class, 'showLoginForm'])->name('superadmin.login.form');
Route::post('/superadmin/login', [SuperadminAuthController::class, 'login'])->name('superadmin.login');
Route::post('/superadmin/logout', [SuperadminAuthController::class, 'logout'])->name('superadmin.logout');

// Superadmin Portal (protected)
Route::middleware(['auth:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/students', function () { return view('superadmin.students'); });
    Route::get('/lecturers', function () {
        $lecturers = \App\Models\Lecturer::all();
        return view('superadmin.lecturers', compact('lecturers'));
    });
    Route::get('/classes', [SuperadminClassController::class, 'index']);
    Route::get('/attendance', function () { return view('superadmin.attendance'); });
    Route::get('/reports', function () { return view('superadmin.reports'); });
    Route::get('/settings', function () { return view('superadmin.settings'); });
    Route::get('/classes/{id}', function($id) {
        $class = \App\Models\Classroom::with('lecturer')->findOrFail($id);
        return view('superadmin.class_detail', compact('class'));
    });
    Route::get('/attendance-audit', [App\Http\Controllers\SuperadminAttendanceController::class, 'index'])->name('superadmin.attendance.audit');
    Route::get('/attendance-audit/export', [App\Http\Controllers\SuperadminAttendanceController::class, 'exportCsv'])->name('superadmin.attendance.audit.export');
    Route::get('/attendance-audit/stats', [App\Http\Controllers\SuperadminAttendanceController::class, 'dashboardStats'])->name('superadmin.attendance.audit.stats');
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

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

Route::get('/login', function () {
    return redirect('/superadmin/login');
})->name('login');

Route::get('/student/landing', [App\Http\Controllers\StudentController::class, 'landing'])->name('student.landing');
Route::post('/student/validate', [App\Http\Controllers\StudentController::class, 'validateStudent']);
Route::post('/student/register-face', [\App\Http\Controllers\StudentController::class, 'registerFace'])->name('student.register-face');
Route::get('/student/attendance-capture', function () {
    return view('student.attendance_capture');
});
Route::get('/student/register-face', function () {
    return view('student.register_face');
})->name('student.register-face.page');
Route::post('/api/student/validate-matric', [\App\Http\Controllers\StudentController::class, 'validateMatricForFaceRegistration']);

// Student Face Registration Management (Superadmin)
Route::middleware(['auth:superadmin'])->prefix('superadmin/students')->group(function () {
    Route::get('face-registration-management', [App\Http\Controllers\SuperadminStudentController::class, 'faceRegistrationManagement'])->name('superadmin.students.face-registration-management');
    Route::get('face-registration-management/data', [App\Http\Controllers\SuperadminStudentController::class, 'faceRegistrationData'])->name('superadmin.students.face-registration-data');
    Route::post('face-registration-management/update-image/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'updateFaceImage'])->name('superadmin.students.update-face-image');
    Route::delete('face-registration-management/delete-image/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'deleteFaceImage'])->name('superadmin.students.delete-face-image');
    Route::post('face-registration-management/enable/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'enableFaceRegistration'])->name('superadmin.students.enable-face-registration');
    Route::post('face-registration-management/disable/{id}', [App\Http\Controllers\SuperadminStudentController::class, 'disableFaceRegistration'])->name('superadmin.students.disable-face-registration');
    Route::post('face-registration-management/bulk-action', [App\Http\Controllers\SuperadminStudentController::class, 'bulkFaceRegistrationAction'])->name('superadmin.students.bulk-face-registration-action');
    Route::get('face-registration-management/export', [App\Http\Controllers\SuperadminStudentController::class, 'exportFaceRegistration'])->name('superadmin.students.export-face-registration');
});
