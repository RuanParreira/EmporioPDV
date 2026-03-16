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
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 5), // Comprou de 1 a 5 unidades
            'unit_price' => $this->faker->randomFloat(2, 5, 100), // Preço falso
            'notes' => $this->faker->optional(0.3)->sentence(3), // 30% de chance de ter uma observação (ex: "Sem cebola")
        ];
    }
}
