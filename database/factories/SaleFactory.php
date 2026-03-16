<?php

namespace Database\Factories;

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
        $randomDate = $this->faker->dateTimeBetween('-1 week', 'now');
        return [
            'user_id' => User::factory(),
            'total_value' => $this->faker->randomFloat(2, 10, 500),
            'payment_method' => $this->faker->randomElement([
                'dinheiro',
                'pix',
                'cartao',

            ]),
            'created_at' => $randomDate,
        ];
    }
}
