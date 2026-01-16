<?php

namespace Database\Factories;

use App\Models\Hod;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class HodFactory extends Factory
{
    protected $model = Hod::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'staff_id' => 'HOD' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->randomElement(['Dr.', 'Prof.', 'Mr.', 'Mrs.']),
            'phone' => $this->faker->phoneNumber(),
            'office_location' => $this->faker->address(),
            'is_active' => true,
            'appointed_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'last_login_at' => null,
            'permissions' => ['view_reports', 'manage_eligibility', 'send_notifications'],
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

    public function withUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function withDepartment(Department $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => $department->id,
        ]);
    }
}