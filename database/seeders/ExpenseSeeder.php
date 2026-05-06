<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    private array $templates = [
        'Food & Drinks' => [
            'titles' => ['Lunch', 'Dinner', 'Coffee', 'Breakfast', 'Groceries', 'Boba tea', 'Snacks', "McDonald's", 'KFC', 'Pizza', 'Nasi lemak', 'Roti canai', 'Teh tarik'],
            'min' => 4,
            'max' => 80,
            'color' => '#f97316',
        ],
        'Transport' => [
            'titles' => ['Grab', 'Petrol', 'Parking', 'Toll', 'Bus fare', 'Taxi', 'MRT', 'Car wash'],
            'min' => 3,
            'max' => 60,
            'color' => '#3b82f6',
        ],
        'Shopping' => [
            'titles' => ['Clothes', 'Shoes', 'Accessories', 'Lazada order', 'Shopee order', 'Books', 'Stationery'],
            'min' => 20,
            'max' => 300,
            'color' => '#a855f7',
        ],
        'Bills & Utilities' => [
            'titles' => ['Electricity bill', 'Water bill', 'Internet bill', 'Phone bill', 'Netflix', 'Spotify', 'Insurance premium'],
            'min' => 15,
            'max' => 250,
            'color' => '#ef4444',
        ],
        'Entertainment' => [
            'titles' => ['Movie tickets', 'Concert', 'Games', 'Streaming subscription', 'Bowling', 'Karaoke'],
            'min' => 15,
            'max' => 150,
            'color' => '#eab308',
        ],
        'Health' => [
            'titles' => ['Pharmacy', 'Doctor visit', 'Gym membership', 'Medicine', 'Supplements', 'Dental checkup'],
            'min' => 10,
            'max' => 200,
            'color' => '#22c55e',
        ],
        'Housing' => [
            'titles' => ['Rent', 'Cleaning supplies', 'Home repair', 'Furniture'],
            'min' => 50,
            'max' => 1500,
            'color' => '#6366f1',
        ],
    ];

    public function run(): void
    {
        $faker = Faker::create();
        $faker->seed(1234);

        $user = User::first();

        // Create categories
        $categories = [];
        foreach ($this->templates as $name => $data) {
            $categories[$name] = Category::create([
                'user_id' => $user->id,
                'name' => $name,
                'color' => $data['color'],
            ]);
        }

        $categoryList = array_values($categories);

        // 6 months: Nov 1 2025 – Apr 28 2026
        $start = Carbon::create(2025, 11, 1);
        $end = Carbon::create(2026, 4, 28);

        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $weekEnd = $cursor->copy()->addDays(6);
            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

            $daysAvailable = (int) $cursor->diffInDays($weekEnd) + 1;
            $count = min($faker->numberBetween(4, 8), $daysAvailable);

            // Pick unique days within this week
            $offsets = $faker->randomElements(range(0, $daysAvailable - 1), $count, false);

            foreach ($offsets as $offset) {
                $date = $cursor->copy()->addDays($offset);
                $category = $faker->randomElement($categoryList);
                $template = $this->templates[$category->name];

                Expense::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'title' => $faker->randomElement($template['titles']),
                    'amount' => $faker->randomFloat(2, $template['min'], $template['max']),
                    'expense_date' => $date->toDateString(),
                    'notes' => $faker->optional(0.2)->sentence(),
                ]);
            }

            $cursor->addWeek();
        }
    }
}
