<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matric_number',
        'full_name',
        'email',
        'department',
        'academic_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'class_student');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
