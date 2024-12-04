<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GoldenProductVariant;
use App\Models\GoldenProductVariantLocalized;
use App\Models\Locale;

class GoldenProductVariantLocalizedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoldenProductVariantLocalized::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'attributes' => '{}',
            'golden_product_variant_id' => GoldenProductVariant::factory(),
            'locale_id' => Locale::factory(),
        ];
    }
}
