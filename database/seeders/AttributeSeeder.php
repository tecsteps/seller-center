<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributesByType = [
            'Sneakers' => [
                [
                    'name' => 'size',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45']
                ],
                [
                    'name' => 'color',
                    'type' => 'select',
                    'field' => 'ColorPicker',
                    'required' => true,
                    'options' => ['Black', 'White', 'Red', 'Blue', 'Grey']
                ]
            ],
            'T-Shirt' => [
                [
                    'name' => 'size',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL']
                ],
                [
                    'name' => 'color',
                    'type' => 'select',
                    'field' => 'ColorPicker',
                    'required' => true,
                    'options' => ['Black', 'White', 'Navy', 'Grey', 'Red']
                ]
            ],
            'Jeans' => [
                [
                    'name' => 'size',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => ['28/30', '30/30', '32/30', '34/30', '36/30', '28/32', '30/32', '32/32', '34/32', '36/32']
                ],
                [
                    'name' => 'wash',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => ['Light', 'Medium', 'Dark', 'Black', 'Raw']
                ]
            ],
            'Dress' => [
                [
                    'name' => 'size',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => ['XS', 'S', 'M', 'L', 'XL']
                ],
                [
                    'name' => 'color',
                    'type' => 'select',
                    'field' => 'ColorPicker',
                    'required' => true,
                    'options' => ['Black', 'Navy', 'Red', 'White', 'Floral']
                ]
            ]
        ];

        foreach ($attributesByType as $typeName => $attributes) {
            $productType = ProductType::where('name', $typeName)->first();
            if ($productType) {
                foreach ($attributes as $attributeData) {
                    $productType->attributes()->create($attributeData);
                }
            }
        }
    }
}
