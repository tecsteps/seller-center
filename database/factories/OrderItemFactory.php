<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerProduct;
use App\Models\SellerVariant;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'seller_product_id' => SellerProduct::factory(),
            'seller_variant_id' => SellerVariant::factory(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'price' => $this->faker->numberBetween(-10000, 10000),
        ];
    }
}
