<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ProductType;
use App\Models\ProductTypeAttribute;

class ProductTypeAttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductTypeAttribute::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'type' => $this->faker->randomElement(["text","boolean","number","select","url","color"]),
            'is_translatable' => $this->faker->boolean(),
            'field' => $this->faker->randomElement(["TextInput","Textarea","Checkbox","Toggle","Select","ColorPicker"]),
            'required' => $this->faker->boolean(),
            'rank' => $this->faker->numberBetween(-10000, 10000),
            'description' => $this->faker->text(),
            'unit' => $this->faker->word(),
            'is_variant_attribute' => $this->faker->boolean(),
            'validators' => '{}',
            'product_type_id' => ProductType::factory(),
        ];
    }
}
