<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Currency;
use App\Models\Price;
use App\Models\SellerVariant;

class PriceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Price::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(-10000, 10000),
            'seller_variant_id' => SellerVariant::factory(),
            'currency_id' => Currency::factory(),
        ];
    }
}
