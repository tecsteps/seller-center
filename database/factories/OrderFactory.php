<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Seller;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->word(),
            'status' => $this->faker->randomElement(["pending","processing","shipped","delivered","cancelled"]),
            'total_amount' => $this->faker->numberBetween(-10000, 10000),
            'seller_id' => Seller::factory(),
            'shipping_address' => $this->faker->text(),
            'billing_address' => $this->faker->text(),
        ];
    }
}
