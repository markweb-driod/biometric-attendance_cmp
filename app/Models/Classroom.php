<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'course_code',
        'pin',
        'schedule',
        'description',
        'lecturer_id',
        'is_active',
        'level',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(\App\Models\AttendanceSession::class);
    }
}
