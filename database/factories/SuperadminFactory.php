<?php

namespace Database\Factories;

use App\Models\Superadmin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class SuperadminFactory extends Factory
{
    protected $model = Superadmin::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'is_active' => true,
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
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
}

