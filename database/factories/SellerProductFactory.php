<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Seller;
use App\Models\SellerProduct;

class SellerProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerProduct::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'brand' => $this->faker->word(),
            'sku' => $this->faker->word(),
            'description' => $this->faker->text(),
            'attributes' => '{}',
            'category_id' => Category::factory(),
            'seller_id' => Seller::factory(),
            'status' => $this->faker->randomElement(["draft","active","delisted"]),
        ];
    }
}
