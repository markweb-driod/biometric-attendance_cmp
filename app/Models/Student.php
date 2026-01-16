<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matric_number',
        'phone',
        'department_id',
        'academic_level_id',
        'current_semester_id',
        'reference_image_path',
        'face_registration_enabled',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'face_registration_enabled' => 'boolean',
    ];

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'class_student');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function currentSemester()
    {
        return $this->belongsTo(Semester::class, 'current_semester_id');
    }

    public function attendancesForSemester($semesterId = null)
    {
        $semesterId = $semesterId ?? $this->current_semester_id;
        return $this->attendances()->where('semester_id', $semesterId);
    }
}
