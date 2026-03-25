<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'caixa']),
            'total_value' => $this->faker->randomFloat(2, 50, 500),
            'payment_method' => $this->faker->randomElement(['dinheiro', 'cartao', 'pix']),
            'received_value' => $this->faker->randomFloat(2, 50, 500),
            'created_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
