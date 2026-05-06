<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => null,
            'title' => $this->faker->words(3, true),
            'amount' => $this->faker->randomFloat(2, 5, 500),
            'expense_date' => $this->faker->dateBetween('-6 months', 'now'),
            'notes' => $this->faker->optional(0.2)->sentence(),
        ];
    }
}
