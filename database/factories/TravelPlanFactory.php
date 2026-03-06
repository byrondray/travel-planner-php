<?php

namespace Database\Factories;

use App\Models\TravelPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelPlanFactory extends Factory
{
    protected $model = TravelPlan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+2 months'),
            'budget' => fake()->randomFloat(2, 500, 10000),
            'currency' => 'USD',
            'status' => 'draft',
            'processing_status' => 'completed',
        ];
    }
}
