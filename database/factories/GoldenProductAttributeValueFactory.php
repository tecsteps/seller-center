<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GoldenProductAttribute;
use App\Models\GoldenProductAttributeValue;
use App\Models\Locale;

class GoldenProductAttributeValueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoldenProductAttributeValue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'value' => $this->faker->word(),
            'golden_product_attribute_id' => GoldenProductAttribute::factory(),
            'locale_id' => Locale::factory(),
        ];
    }
}
