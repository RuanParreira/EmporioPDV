<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleItem>
 */
class SaleItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => null,
            'quantity' => $this->faker->randomFloat(3, 0.1, 5),
            'product_name' => $this->faker->word(),
            'unit_price' => $this->faker->randomFloat(2, 5, 100),
            'notes' => $this->faker->optional(0.3)->sentence(3),
        ];
    }
}
