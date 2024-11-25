<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Seller;

class SellerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seller::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'status' => $this->faker->randomElement(["open","submitted","accepted","rejected","review"]),
            'description' => $this->faker->text(),
            'company_name' => $this->faker->word(),
            'address_line1' => $this->faker->word(),
            'address_line2' => $this->faker->word(),
            'city' => $this->faker->city(),
            'state' => $this->faker->word(),
            'postal_code' => $this->faker->postcode(),
            'country_code' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'vat' => $this->faker->word(),
            'tin' => $this->faker->word(),
            'eori' => $this->faker->word(),
            'iban' => $this->faker->word(),
            'swift_bic' => $this->faker->word(),
            'bank_name' => $this->faker->word(),
        ];
    }
}
