<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Locale;
use App\Models\ProductTypeAttributeOption;
use App\Models\ProductTypeAttributeOptionValue;

class ProductTypeAttributeOptionValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductTypeAttributeOptionValue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'value' => $this->faker->word(),
            'locale_id' => Locale::factory(),
            'product_type_attribute_option_id' => ProductTypeAttributeOption::factory(),
        ];
    }
}
