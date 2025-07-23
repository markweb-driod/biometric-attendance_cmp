<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\AttendanceSessionController;
use App\Http\Controllers\SuperadminStudentController;
use App\Http\Controllers\SuperadminLecturerController;
use App\Http\Controllers\SuperadminClassController;
use App\Http\Controllers\SuperadminAttendanceController;
use App\Http\Controllers\SuperadminDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Student Attendance API Routes
Route::post('/validate-student', [StudentAttendanceController::class, 'validateStudent']);
Route::post('/capture-attendance', [StudentAttendanceController::class, 'captureAttendance']);
Route::post('/student/fetch-details', [App\Http\Controllers\Api\StudentAttendanceController::class, 'fetchDetails']);
Route::post('/student/capture-attendance', [App\Http\Controllers\Api\StudentAttendanceController::class, 'captureAttendance']);

// Lecturer API Routes
Route::post('/validate-lecturer', [LecturerController::class, 'validateLecturer']);
Route::get('/lecturer/dashboard', [LecturerController::class, 'getDashboard']);

// Classroom CRUD API
Route::get('/lecturer/classes', [ClassroomController::class, 'index']);
Route::post('/lecturer/classes', [ClassroomController::class, 'store']);
Route::put('/lecturer/classes/{id}', [ClassroomController::class, 'update']);
Route::delete('/lecturer/classes/{id}', [ClassroomController::class, 'destroy']);

// Attendance Session API
Route::get('/lecturer/attendance-sessions', [AttendanceSessionController::class, 'index']);
Route::post('/lecturer/attendance-sessions', [AttendanceSessionController::class, 'store']);
Route::put('/lecturer/attendance-sessions/{id}', [AttendanceSessionController::class, 'update']);
Route::get('/lecturer/attendance-sessions/{id}/students', [AttendanceSessionController::class, 'students']);

// Superadmin Dashboard API
Route::get('/superadmin/dashboard/stats', [SuperadminDashboardController::class, 'getStats']);

// Superadmin Students API
Route::get('/superadmin/students/stats', [SuperadminStudentController::class, 'stats']);
Route::get('/superadmin/students', [SuperadminStudentController::class, 'index']);
Route::get('/superadmin/students/{id}', [SuperadminStudentController::class, 'show']);
Route::post('/superadmin/students', [SuperadminStudentController::class, 'store']);
Route::put('/superadmin/students/{id}', [SuperadminStudentController::class, 'update']);
Route::delete('/superadmin/students/{id}', [SuperadminStudentController::class, 'destroy']);
Route::post('/superadmin/students/bulk-upload', [SuperadminStudentController::class, 'bulkUpload']);

// Superadmin Lecturers API
Route::get('/superadmin/lecturers', [SuperadminLecturerController::class, 'index']);
Route::get('/superadmin/lecturers/stats', [SuperadminLecturerController::class, 'stats']);
Route::get('/superadmin/lecturers/{id}', [SuperadminLecturerController::class, 'show']);
Route::post('/superadmin/lecturers', [SuperadminLecturerController::class, 'store']);
Route::put('/superadmin/lecturers/{id}', [SuperadminLecturerController::class, 'update']);
Route::delete('/superadmin/lecturers/{id}', [SuperadminLecturerController::class, 'destroy']);
Route::post('/superadmin/lecturers/bulk-upload', [SuperadminLecturerController::class, 'bulkUpload']);

// Superadmin Classes API
Route::get('/superadmin/classes', [SuperadminClassController::class, 'index']);
Route::get('/superadmin/classes/stats', [SuperadminClassController::class, 'stats']);
Route::post('/superadmin/classes', [SuperadminClassController::class, 'store']);
Route::get('/superadmin/classes/{id}', [SuperadminClassController::class, 'show']);
Route::put('/superadmin/classes/{id}', [SuperadminClassController::class, 'update']);
Route::delete('/superadmin/classes/{id}', [SuperadminClassController::class, 'destroy']);
// Superadmin Attendance API
Route::get('/superadmin/attendance', [SuperadminAttendanceController::class, 'apiIndex']);
Route::get('/superadmin/attendance/stats', [SuperadminAttendanceController::class, 'stats']); 
Route::get('/superadmin/reports', [App\Http\Controllers\ReportController::class, 'reportData']);
Route::get('/superadmin/reports/export', [App\Http\Controllers\ReportController::class, 'exportCsv']); 