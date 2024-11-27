<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Seller;
use App\Models\SellerProduct;
use App\Models\SellerVariant;

class SellerVariantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerVariant::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'sku' => $this->faker->word(),
            'description' => $this->faker->text(),
            'attributes' => '{}',
            'seller_product_id' => SellerProduct::factory(),
            'seller_id' => Seller::factory(),
        ];
    }
}
