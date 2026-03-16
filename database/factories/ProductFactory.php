<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 200), // Valor com 2 casas decimais (ex: 15.50)
            'measure_unit' => $this->faker->randomElement(['UN', 'KG']),
            'active' => $this->faker->boolean(90) // 90% chance de criar ativo
        ];
    }
}
