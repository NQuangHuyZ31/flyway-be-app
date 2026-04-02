<?php

namespace Database\Factories;

use App\Models\product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<product>
 */
class productFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'product_name' => $this->faker->word(),
            'product_code' => $this->faker->unique()->numerify('PRD-#####'),
            'sku' => $this->faker->unique()->numerify('SKU-#####'),
            'category_id' => 1,
            'unit_id' => $this->faker->numberBetween(1, 5),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10,100),
            'cost' => $this->faker->randomFloat(2, 5, 50),
            'minimum_inventory' => $this->faker->numberBetween(1, 20),
            'total_quantity' => $this->faker->numberBetween(20, 100),
            'product_image_url' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
