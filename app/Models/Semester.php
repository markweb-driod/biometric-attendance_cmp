<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'academic_year',
        'start_date',
        'end_date',
        'is_active',
        'is_current',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_current' => 'boolean',
    ];

    /**
     * Get the current active semester
     */
    public static function getCurrent()
    {
        return self::where('is_current', true)->where('is_active', true)->first();
    }

    /**
     * Get all active semesters
     */
    public static function getActive()
    {
        return self::where('is_active', true)->orderBy('start_date', 'desc')->get();
    }

    /**
     * Check if semester is currently running
     */
    public function isCurrentlyRunning()
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Get semester display name
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->name} {$this->academic_year}";
    }

    /**
     * Get semester short name
     */
    public function getShortNameAttribute()
    {
        return "{$this->code} {$this->academic_year}";
    }

    /**
     * Set as current semester (deactivates others)
     */
    public function setAsCurrent()
    {
        // Deactivate all other current semesters
        self::where('is_current', true)->update(['is_current' => false]);
        
        // Set this as current
        $this->update(['is_current' => true, 'is_active' => true]);
    }

    /**
     * Relationships
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}