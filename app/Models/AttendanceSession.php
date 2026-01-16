<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'lecturer_id',
        'session_name',
        'start_time',
        'end_time',
        'status',
        'notes',
        'is_active',
        'code',
        'venue_id',
        'duration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class, 'attendance_session_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
} 