<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Location;
use App\Models\SellerVariant;
use App\Models\Stock;

class StockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stock::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'reserved' => $this->faker->numberBetween(-10000, 10000),
            'safety_stock' => $this->faker->numberBetween(-10000, 10000),
            'seller_variant_id' => SellerVariant::factory(),
            'location_id' => Location::factory(),
        ];
    }
}
