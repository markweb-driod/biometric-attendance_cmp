<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lecturer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'staff_id',
        'phone',
        'department_id',
        'title',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, Classroom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'lecturer_course')
            ->withPivot(['is_active', 'assigned_at', 'unassigned_at'])
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    public function allCourses()
    {
        return $this->belongsToMany(Course::class, 'lecturer_course')
            ->withPivot(['is_active', 'assigned_at', 'unassigned_at'])
            ->withTimestamps();
    }

    /**
     * Check if lecturer is assigned to a specific course
     */
    public function isAssignedToCourse($courseId)
    {
        return $this->courses()->where('courses.id', $courseId)->exists();
    }

    /**
     * Get classrooms only for assigned courses
     */
    public function assignedClassrooms()
    {
        $assignedCourseIds = $this->courses()->pluck('courses.id');
        if ($assignedCourseIds->isEmpty()) {
            // Return empty query if no assigned courses
            return $this->classrooms()->whereRaw('1 = 0');
        }
        return $this->classrooms()->whereIn('course_id', $assignedCourseIds);
    }
}
