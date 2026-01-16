<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamEligibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'semester',
        'academic_year',
        'attendance_percentage',
        'required_threshold',
        'status',
        'override_reason',
        'overridden_by',
        'overridden_at',
        'validated_at',
        'validation_details',
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
        'required_threshold' => 'decimal:2',
        'overridden_at' => 'datetime',
        'validated_at' => 'datetime',
        'validation_details' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function overriddenBy()
    {
        return $this->belongsTo(Hod::class, 'overridden_by');
    }

    public function isEligible()
    {
        return $this->status === 'eligible' || $this->status === 'overridden';
    }

    public function isOverridden()
    {
        return $this->status === 'overridden';
    }

    public function meetsThreshold()
    {
        return $this->attendance_percentage >= $this->required_threshold;
    }

    public function getEligibilityStatusAttribute()
    {
        if ($this->isOverridden()) {
            return 'Overridden';
        }
        
        if ($this->meetsThreshold()) {
            return 'Eligible';
        }
        
        return 'Ineligible';
    }

    public function scopeEligible($query)
    {
        return $query->where('status', 'eligible');
    }

    public function scopeIneligible($query)
    {
        return $query->where('status', 'ineligible');
    }

    public function scopeOverridden($query)
    {
        return $query->where('status', 'overridden');
    }

    public function scopeForSemester($query, $semester, $academicYear)
    {
        return $query->where('semester', $semester)
                    ->where('academic_year', $academicYear);
    }
    
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
