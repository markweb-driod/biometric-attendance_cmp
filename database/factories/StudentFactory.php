<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\AcademicLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'academic_level_id' => 1, // Default level
            'matric_number' => strtoupper($this->faker->bothify('??/????/???')),
            'phone' => $this->faker->phoneNumber(),
            'current_semester_id' => 1, // Default semester
            'reference_image_path' => null,
            'face_registration_enabled' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withDepartment(Department $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => $department->id,
        ]);
    }
}