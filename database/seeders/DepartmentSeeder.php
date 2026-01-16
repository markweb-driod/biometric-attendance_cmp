<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CSC',
                'description' => 'Department of Computer Science',
                'is_active' => true,
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MAT',
                'description' => 'Department of Mathematics',
                'is_active' => true,
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY',
                'description' => 'Department of Physics',
                'is_active' => true,
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHE',
                'description' => 'Department of Chemistry',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert($department);
        }
    }
}
