<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
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
            'user_id' => User::all()->random()->id,
            'product_name' => fake()->randomElement(Product::$product_name),
            'product_description' => fake()->sentence(),
            'product_price' => fake()->randomFloat(2, 1, 100),
            'product_tag' => fake()->randomElements(Product::$product_tag, fake()->numberBetween(1, 4)),
        ];
    }
}
