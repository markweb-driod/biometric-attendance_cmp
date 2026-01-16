<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'credit_units',
        'academic_level_id',
        'semester_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'course_department');
    }

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_course')
            ->withPivot(['is_active', 'assigned_at', 'unassigned_at'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    public function allLecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_course')
            ->withPivot(['is_active', 'assigned_at', 'unassigned_at'])
            ->withTimestamps();
    }
}
