<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProductTypeAttribute;
use App\Models\ProductTypeAttributeOption;

class ProductTypeAttributeOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductTypeAttributeOption::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_type_attribute_id' => ProductTypeAttribute::factory(),
        ];
    }
}
