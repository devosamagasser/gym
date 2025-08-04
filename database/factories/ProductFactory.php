<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

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
        $price = $this->faker->randomFloat(2, 10, 100);
        $sale = $this->faker->optional()->randomFloat(2, 1, $price - 1);
        $fakerAr = FakerFactory::create('ar_SA');

        return [
            'price' => $price,
            'sale' => $sale,
            'stock' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'en' => [
                'name' => $this->faker->words(3, true),
                'description' => $this->faker->paragraph(),
            ],
            'ar' => [
                'name' => $fakerAr->words(3, true),
                'description' => $fakerAr->paragraph(),
            ],
        ];
    }
}
