<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicLevel;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class NSUKStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_NG'); // Nigerian locale for more realistic names
        
        // Get departments and academic levels
        $departments = Department::all();
        $academicLevels = AcademicLevel::whereIn('name', ['100 Level', '200 Level', '300 Level', '400 Level'])->get();
        
        if ($departments->isEmpty() || $academicLevels->isEmpty()) {
            $this->command->error('Required departments or academic levels not found. Please run department and academic level seeders first.');
            return;
        }

        // Clear existing students
        $this->command->info('Clearing existing students...');
        Student::query()->delete();
        User::where('role', 'student')->delete();

        $this->command->info('Seeding 300 NSUK students...');
        
        $studentsPerLevel = 75; // 300 students / 4 levels = 75 per level
        $studentsPerDepartment = 25; // 75 students / 3 departments = 25 per department
        
        $studentCount = 0;
        $matricCounter = 1;

        foreach ($academicLevels as $level) {
            $this->command->info("Seeding students for {$level->name}...");
            
            foreach ($departments->take(3) as $department) { // Use first 3 departments
                for ($i = 0; $i < $studentsPerDepartment; $i++) {
                    $studentCount++;
                    
                    // Generate matric number in format: 0200470123
                    $matricNumber = $this->generateMatricNumber($matricCounter);
                    $matricCounter++;
                    
                    // Generate Nigerian-style names
                    $firstName = $faker->firstName();
                    $lastName = $faker->lastName();
                    $fullName = $firstName . ' ' . $lastName;
                    
                    // Create user
                    $user = User::create([
                        'username' => strtolower($firstName . '.' . $lastName . '.' . $matricNumber),
                        'email' => strtolower($firstName . '.' . $lastName . '.' . $matricNumber) . '@nsuk.edu.ng',
                        'full_name' => $fullName,
                        'password' => bcrypt('student123'),
                        'role' => 'student',
                        'is_active' => true,
                    ]);

                    // Create student
                    Student::create([
                        'user_id' => $user->id,
                        'matric_number' => $matricNumber,
                        'phone' => $this->generateNigerianPhoneNumber($faker),
                        'department_id' => $department->id,
                        'academic_level_id' => $level->id,
                        'department' => $department->name,
                        'level' => str_replace(' Level', '', $level->name),
                        'academic_level' => $level->name,
                        'reference_image_path' => null,
                        'face_registration_enabled' => false,
                        'is_active' => true,
                    ]);

                    if ($studentCount % 50 == 0) {
                        $this->command->info("Seeded {$studentCount} students...");
                    }
                }
            }
        }

        $this->command->info("Successfully seeded {$studentCount} NSUK students!");
        $this->command->info("Students distributed across 4 levels (100-400) and 3 departments.");
    }

    /**
     * Generate matric number in NSUK format: 0200470123
     */
    private function generateMatricNumber($counter): string
    {
        // Format: 02 (year) + 0047 (department code) + 0123 (student number)
        $year = '02'; // 2024/2025 session
        $deptCode = '0047'; // Computer Science department code
        $studentNumber = str_pad($counter, 4, '0', STR_PAD_LEFT);
        
        return $year . $deptCode . $studentNumber;
    }

    /**
     * Generate Nigerian phone number
     */
    private function generateNigerianPhoneNumber($faker): string
    {
        $prefixes = ['080', '081', '090', '091', '070', '071'];
        $prefix = $faker->randomElement($prefixes);
        $number = $faker->numerify('########');
        
        return $prefix . $number;
    }
}

