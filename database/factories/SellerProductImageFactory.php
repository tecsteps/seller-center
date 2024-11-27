<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SellerProduct;
use App\Models\SellerProductImage;
use App\Models\SellerVariant;

class SellerProductImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerProductImage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'seller_product_id' => SellerProduct::factory(),
            'seller_variant_id' => SellerVariant::factory(),
            'image' => $this->faker->word(),
            'number' => $this->faker->numberBetween(-10000, 10000),
        ];
    }
}
