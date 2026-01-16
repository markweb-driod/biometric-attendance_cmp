<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SystemAnalyticsExport implements WithMultipleSheets
{
    protected $analytics;

    public function __construct($analytics)
    {
        $this->analytics = $analytics;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // System Overview Sheet
        $sheets[] = new SystemOverviewSheet($this->analytics['system_overview']);
        
        // User Statistics Sheet
        $sheets[] = new UserStatisticsSheet($this->analytics['user_statistics']);
        
        // Attendance Analytics Sheet
        $sheets[] = new AttendanceAnalyticsSheet($this->analytics['attendance_analytics']);
        
        // Performance Metrics Sheet
        $sheets[] = new PerformanceMetricsSheet($this->analytics['performance_metrics']);
        
        return $sheets;
    }
}

class SystemOverviewSheet implements FromArray, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['System Overview Report', ''],
            ['Generated on', date('Y-m-d H:i:s')],
            [''],
            ['Metric', 'Value'],
            ['Total Users', $this->data['total_users']],
            ['Active Users', $this->data['active_users']],
            ['Total Students', $this->data['total_students']],
            ['Active Students', $this->data['active_students']],
            ['Total Lecturers', $this->data['total_lecturers']],
            ['Active Lecturers', $this->data['active_lecturers']],
            ['Total Departments', $this->data['total_departments']],
            ['Active Departments', $this->data['active_departments']],
            ['Total Courses', $this->data['total_courses']],
            ['Active Courses', $this->data['active_courses']],
            ['Total Classrooms', $this->data['total_classrooms']],
            ['Active Classrooms', $this->data['active_classrooms']],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
        ];
    }
}

class UserStatisticsSheet implements FromArray, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $array = [
            ['User Statistics Report', ''],
            ['Generated on', date('Y-m-d H:i:s')],
            [''],
            ['Users by Role', ''],
            ['Role', 'Count'],
        ];

        foreach ($this->data['users_by_role'] as $role => $count) {
            $array[] = [ucfirst($role), $count];
        }

        $array[] = [''];
        $array[] = ['Users by Status', ''];
        $array[] = ['Status', 'Count'];

        foreach ($this->data['users_by_status'] as $status => $count) {
            $array[] = [$status ? 'Active' : 'Inactive', $count];
        }

        $array[] = [''];
        $array[] = ['Recent Registrations (30 days)', $this->data['recent_registrations']];

        return $array;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
        ];
    }
}

class AttendanceAnalyticsSheet implements FromArray, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Attendance Analytics Report', ''],
            ['Generated on', date('Y-m-d H:i:s')],
            [''],
            ['Metric', 'Value'],
            ['Daily Attendance', $this->data['daily_attendance']],
            ['Weekly Attendance', $this->data['weekly_attendance']],
            ['Monthly Attendance', $this->data['monthly_attendance']],
            ['Total Attendance', $this->data['total_attendance']],
            ['Attendance Sessions', $this->data['attendance_sessions']],
            ['Average Attendance per Session', $this->data['average_attendance_per_session']],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
        ];
    }
}

class PerformanceMetricsSheet implements FromArray, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Performance Metrics Report', ''],
            ['Generated on', date('Y-m-d H:i:s')],
            [''],
            ['Metric', 'Value'],
            ['Database Size', $this->data['database_size']],
            ['Cache Hit Rate', $this->data['cache_hit_rate']],
            ['Average Response Time', $this->data['average_response_time']],
            ['Error Rate', $this->data['error_rate']],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            4 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
        ];
    }
}
