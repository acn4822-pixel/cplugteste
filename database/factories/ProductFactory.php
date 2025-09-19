<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'sku' => $this->faker->unique()->ean13(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'cost_price' => $this->faker->randomFloat(2, 5, 50),
            'sale_price' => $this->faker->randomFloat(2, 51, 100),
        ];
    }
}
