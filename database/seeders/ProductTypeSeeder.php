<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $productTypes = [
            'Sneakers',
            'T-Shirt',
            'Jeans',
            'Dress'
        ];

        foreach ($productTypes as $type) {
            ProductType::create(['name' => $type]);
        }
    }
}
