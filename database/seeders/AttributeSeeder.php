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
                    'name' => 'material',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => [
                        ['label' => 'Leather', 'value' => 'leather'],
                        ['label' => 'Canvas', 'value' => 'canvas'],
                        ['label' => 'Synthetic', 'value' => 'synthetic'],
                        ['label' => 'Mesh', 'value' => 'mesh'],
                        ['label' => 'Suede', 'value' => 'suede']
                    ],
                    'description' => 'Main material of the sneaker'
                ],
                [
                    'name' => 'is_waterproof',
                    'type' => 'boolean',
                    'field' => 'Toggle',
                    'required' => true,
                    'description' => 'Whether the sneaker is waterproof'
                ],
                [
                    'name' => 'product_description',
                    'type' => 'text',
                    'field' => 'Textarea',
                    'required' => false,
                    'description' => 'Detailed product description with formatting'
                ]
            ],
            'T-Shirt' => [
                [
                    'name' => 'fabric_composition',
                    'type' => 'text',
                    'field' => 'TextInput',
                    'required' => true,
                    'description' => 'Material composition (e.g., "100% Cotton")'
                ],
                [
                    'name' => 'style_categories',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => [
                        ['label' => 'Casual', 'value' => 'casual'],
                        ['label' => 'Sport', 'value' => 'sport'],
                        ['label' => 'Streetwear', 'value' => 'streetwear'],
                        ['label' => 'Business', 'value' => 'business']
                    ],
                    'description' => 'Style categories this shirt belongs to'
                ],
                [
                    'name' => 'is_sustainable',
                    'type' => 'boolean',
                    'field' => 'Checkbox',
                    'required' => false,
                    'description' => 'Made with sustainable materials/processes'
                ]
            ],
            'Jeans' => [
                [
                    'name' => 'fit_style',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => [
                        ['label' => 'Skinny', 'value' => 'skinny'],
                        ['label' => 'Slim', 'value' => 'slim'],
                        ['label' => 'Regular', 'value' => 'regular'],
                        ['label' => 'Relaxed', 'value' => 'relaxed'],
                        ['label' => 'Bootcut', 'value' => 'bootcut']
                    ],
                    'description' => 'Style of the fit'
                ],
                [
                    'name' => 'fabric_weight',
                    'type' => 'number',
                    'field' => 'TextInput',
                    'required' => true,
                    'description' => 'Weight of the denim in oz/yd²',
                    'unit' => 'oz/yd²'
                ],
                [
                    'name' => 'features',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => [
                        ['label' => 'Stretch', 'value' => 'stretch'],
                        ['label' => 'Distressed', 'value' => 'distressed'],
                        ['label' => 'Raw Denim', 'value' => 'raw_denim'],
                        ['label' => 'Stone Washed', 'value' => 'stone_washed'],
                        ['label' => 'Ripped', 'value' => 'ripped']
                    ],
                    'description' => 'Special features of the jeans'
                ]
            ],
            'Dress' => [
                [
                    'name' => 'style',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => [
                        ['label' => 'Casual', 'value' => 'casual'],
                        ['label' => 'Formal', 'value' => 'formal'],
                        ['label' => 'Party', 'value' => 'party'],
                        ['label' => 'Business', 'value' => 'business'],
                        ['label' => 'Evening', 'value' => 'evening']
                    ],
                    'description' => 'Style category of the dress'
                ],
                [
                    'name' => 'care_instructions',
                    'type' => 'text',
                    'field' => 'Textarea',
                    'required' => true,
                    'description' => 'Care and washing instructions'
                ],
                [
                    'name' => 'primary_color',
                    'type' => 'color',
                    'field' => 'ColorPicker',
                    'required' => true,
                    'description' => 'Primary color of the dress'
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
