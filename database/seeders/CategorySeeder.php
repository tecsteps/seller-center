<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create root category
        Category::create([
            'name' => 'Root',
            'description' => 'Root category for all products',
            'is_active' => true,
            'parent_id' => null,
        ]);

        // Create main fashion category
        Category::create([
            'name' => 'Fashion',
            'description' => 'Clothing, shoes, accessories, and jewelry',
            'is_active' => true,
            'parent_id' => 1,
        ]);

        // Create fashion subcategories
        $subCategories = [
            [
                'name' => 'Women\'s Clothing',
                'description' => 'Dresses, tops, skirts and more for women',
                'is_active' => true,
                'parent_id' => 2,
            ],
            [
                'name' => 'Men\'s Clothing',
                'description' => 'Shirts, pants, suits and more for men',
                'is_active' => true,
                'parent_id' => 2,
            ],
            [
                'name' => 'Shoes',
                'description' => 'Athletic shoes, boots, sandals, and more',
                'is_active' => true,
                'parent_id' => 2,
            ],
            [
                'name' => 'Accessories',
                'description' => 'Hats, scarves, belts, and more',
                'is_active' => true,
                'parent_id' => 2,
            ],
        ];

        foreach ($subCategories as $category) {
            Category::create($category);
        }
    }
}
