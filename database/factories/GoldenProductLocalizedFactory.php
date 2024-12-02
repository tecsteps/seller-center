<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GoldenProduct;
use App\Models\GoldenProductLocalized;
use App\Models\Locale;
use App\Models\ProductType;

class GoldenProductLocalizedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoldenProductLocalized::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'attributes' => '{}',
            'product_type_id' => ProductType::factory(),
            'locale_id' => Locale::factory(),
            'golden_product_id' => GoldenProduct::factory(),
        ];
    }
}
