<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'classroom_id',
        'attendance_session_id',
        'semester_id',
        'image_path',
        'captured_at',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function session()
    {
        return $this->belongsTo(\App\Models\AttendanceSession::class, 'attendance_session_id');
    }

    public function attendanceSession()
    {
        return $this->belongsTo(\App\Models\AttendanceSession::class, 'attendance_session_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
