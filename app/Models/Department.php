<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }

    public function hods()
    {
        return $this->hasMany(Hod::class);
    }

    public function activeHod()
    {
        return $this->hasOne(Hod::class)->where('is_active', true);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_department');
    }
}
