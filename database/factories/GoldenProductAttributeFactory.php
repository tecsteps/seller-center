<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GoldenProduct;
use App\Models\GoldenProductAttribute;
use App\Models\ProductTypeAttribute;

class GoldenProductAttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoldenProductAttribute::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_type_attribute_id' => ProductTypeAttribute::factory(),
            'golden_product_id' => GoldenProduct::factory(),
            'is_option' => $this->faker->boolean(),
        ];
    }
}
