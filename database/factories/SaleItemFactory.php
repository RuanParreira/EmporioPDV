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
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        return [
            'sale_id' => Sale::factory(),
            'product_id' => $product->id,
            'quantity' => $this->faker->randomFloat(2, 0.1, 5),
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'notes' => $this->faker->optional(0.3)->sentence(3),
        ];
    }
}
