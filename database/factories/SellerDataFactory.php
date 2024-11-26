<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Seller;
use App\Models\SellerData;

class SellerDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerData::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'seller_id' => Seller::factory(),
            'email' => $this->faker->safeEmail(),
            'status' => $this->faker->randomElement(["open","submitted","accepted","rejected","review"]),
            'rejection_reason' => $this->faker->text(),
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
            'account_holder_name' => $this->faker->word(),
            'file1' => $this->faker->word(),
            'file2' => $this->faker->word(),
            'file3' => $this->faker->word(),
        ];
    }
}
