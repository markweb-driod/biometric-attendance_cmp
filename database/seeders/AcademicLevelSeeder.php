<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'name' => '100 Level',
                'code' => '100',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => '200 Level',
                'code' => '200',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => '300 Level',
                'code' => '300',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => '400 Level',
                'code' => '400',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => '500 Level',
                'code' => '500',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($levels as $level) {
            DB::table('academic_levels')->insert($level);
        }
    }
}
