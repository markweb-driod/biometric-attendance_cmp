<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            AcademicLevelSeeder::class,
            SemesterSeeder::class,
            UserSeeder::class,
            LecturerSeeder::class,
            HodSeeder::class,
            CourseSeeder::class,
            ClassroomSeeder::class,
            SystemSettingSeeder::class,
            NSUKStudentSeeder::class,
        ]);
    }
}
