<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'course_id',
        'lecturer_id',
        'pin',
        'schedule',
        'description',
        'semester_id',
        'academic_year',
        'is_active',
        'grading_rules',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'grading_rules' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

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

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public static function generatePin()
    {
        do {
            $pin = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6));
        } while (self::where('pin', $pin)->exists());
        
        return $pin;
    }
}
