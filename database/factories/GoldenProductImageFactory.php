<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GoldenProduct;
use App\Models\GoldenProductImage;
use App\Models\SellerProductImage;

class GoldenProductImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoldenProductImage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'golden_product_id' => GoldenProduct::factory(),
            'image' => $this->faker->word(),
            'number' => $this->faker->numberBetween(-10000, 10000),
            'seller_product_image_id' => SellerProductImage::factory(),
        ];
    }
}
