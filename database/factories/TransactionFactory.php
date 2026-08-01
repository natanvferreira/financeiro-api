<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'description' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'date' => fake()->date(),
        ];
    }
}
