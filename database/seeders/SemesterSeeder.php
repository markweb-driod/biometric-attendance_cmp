<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;
        $academicYear = "{$currentYear}/{$nextYear}";

        $semesters = [
            [
                'name' => 'First Semester',
                'code' => 'SEM1',
                'academic_year' => $academicYear,
                'start_date' => Carbon::create($currentYear, 9, 1), // September 1st
                'end_date' => Carbon::create($currentYear, 12, 31), // December 31st
                'is_active' => true,
                'is_current' => true,
                'description' => 'First semester of the academic year',
            ],
            [
                'name' => 'Second Semester',
                'code' => 'SEM2',
                'academic_year' => $academicYear,
                'start_date' => Carbon::create($nextYear, 1, 1), // January 1st
                'end_date' => Carbon::create($nextYear, 5, 31), // May 31st
                'is_active' => true,
                'is_current' => false,
                'description' => 'Second semester of the academic year',
            ],
        ];

        foreach ($semesters as $semester) {
            Semester::updateOrCreate(
                ['code' => $semester['code'], 'academic_year' => $semester['academic_year']],
                $semester
            );
        }

        // Also create semesters for the previous academic year
        $prevYear = $currentYear - 1;
        $prevAcademicYear = "{$prevYear}/{$currentYear}";

        $previousSemesters = [
            [
                'name' => 'First Semester',
                'code' => 'SEM1',
                'academic_year' => $prevAcademicYear,
                'start_date' => Carbon::create($prevYear, 9, 1),
                'end_date' => Carbon::create($prevYear, 12, 31),
                'is_active' => false,
                'is_current' => false,
                'description' => 'First semester of the previous academic year',
            ],
            [
                'name' => 'Second Semester',
                'code' => 'SEM2',
                'academic_year' => $prevAcademicYear,
                'start_date' => Carbon::create($currentYear, 1, 1),
                'end_date' => Carbon::create($currentYear, 5, 31),
                'is_active' => false,
                'is_current' => false,
                'description' => 'Second semester of the previous academic year',
            ],
        ];

        foreach ($previousSemesters as $semester) {
            Semester::updateOrCreate(
                ['code' => $semester['code'], 'academic_year' => $semester['academic_year']],
                $semester
            );
        }
    }
}