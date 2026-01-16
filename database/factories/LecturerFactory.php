<?php

namespace Database\Factories;

use App\Models\Lecturer;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class LecturerFactory extends Factory
{
    protected $model = Lecturer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'staff_id' => 'LEC' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->randomElement(['Dr.', 'Prof.', 'Mr.', 'Mrs.', 'Ms.']),
            'phone' => $this->faker->phoneNumber(),
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