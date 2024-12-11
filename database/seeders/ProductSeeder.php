<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\Location;
use App\Models\ProductType;
use App\Models\SellerProduct;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $seller = Seller::first();
        $location = Location::first();

        $products = [
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Revolutionary Air technology meets modern comfort. The Nike Air Max 270 features the first-ever Max Air unit designed specifically for Nike Sportswear.',
                'brand' => 'Nike',
                'type' => 'Sneakers',
                'attributes' => [
                    'Material' => 'Engineered mesh upper with synthetic overlays',
                    'Sole Type' => 'Max Air cushioning',
                    'Closure Type' => 'Lace-up',
                    'Style' => 'Athletic',
                    'Season' => 'All-season',
                    'Water Resistant' => 'No',
                    'Collection' => 'Air Max',
                    'Target Gender' => 'Unisex'
                ],
                'variant_attributes' => [
                    ['size' => '42', 'color' => 'Black'],
                    ['size' => '43', 'color' => 'Black'],
                    ['size' => '44', 'color' => 'Black'],
                    ['size' => '42', 'color' => 'White'],
                    ['size' => '43', 'color' => 'White'],
                    ['size' => '44', 'color' => 'White'],
                    ['size' => '42', 'color' => 'Red'],
                    ['size' => '43', 'color' => 'Red']
                ],
                'base_price' => 169.99,
                'image' => $faker->imageUrl(640, 480, 'sneakers', true)
            ],
            [
                'name' => 'Premium Cotton Crew Neck',
                'description' => 'Ultra-soft 100% organic cotton t-shirt with a modern fit. Pre-shrunk fabric ensures lasting comfort and shape retention.',
                'brand' => 'Organic Basics',
                'type' => 'T-Shirt',
                'attributes' => [
                    'Material' => '100% organic cotton',
                    'Weight' => '180 GSM',
                    'Fit Type' => 'Regular fit',
                    'Neckline' => 'Crew neck',
                    'Care Instructions' => 'Machine wash cold, tumble dry low',
                    'Sustainability' => 'Organic certified',
                    'Origin' => 'Made in Portugal',
                    'Sleeve Type' => 'Short sleeve'
                ],
                'variant_attributes' => [
                    ['size' => 'M', 'color' => 'Black'],
                    ['size' => 'L', 'color' => 'Black'],
                    ['size' => 'XL', 'color' => 'Black'],
                    ['size' => 'M', 'color' => 'White'],
                    ['size' => 'L', 'color' => 'White'],
                    ['size' => 'XL', 'color' => 'White'],
                    ['size' => 'M', 'color' => 'Navy'],
                    ['size' => 'L', 'color' => 'Navy']
                ],
                'base_price' => 39.99,
                'image' => $faker->imageUrl(640, 480, 'tshirt', true)
            ],
            [
                'name' => 'Levi\'s 501 Original',
                'description' => 'The original straight fit jean that started it all. The 501® Original Fit Jeans are a cultural icon, featuring the signature button fly and straight leg fit.',
                'brand' => 'Levi\'s',
                'type' => 'Jeans',
                'attributes' => [
                    'Material' => '100% cotton denim',
                    'Rise' => 'Mid rise',
                    'Fit Type' => 'Straight fit',
                    'Closure Type' => 'Button fly',
                    'Care Instructions' => 'Machine wash cold, inside out',
                    'Origin' => 'Made in USA',
                    'Stretch' => 'Non-stretch',
                    'Pocket Style' => '5-pocket styling',
                    'Wash Type' => 'Light Wash',
                    'Inseam Measurement' => '32'
                ],
                'variant_attributes' => [
                    ['size' => '32/32', 'wash' => 'Dark'],
                    ['size' => '33/32', 'wash' => 'Dark'],
                    ['size' => '34/32', 'wash' => 'Dark'],
                    ['size' => '32/32', 'wash' => 'Medium'],
                    ['size' => '33/32', 'wash' => 'Medium'],
                    ['size' => '34/32', 'wash' => 'Medium'],
                    ['size' => '32/32', 'wash' => 'Light'],
                    ['size' => '33/32', 'wash' => 'Light']
                ],
                'base_price' => 98.99,
                'image' => $faker->imageUrl(640, 480, 'jeans', true)
            ],
            [
                'name' => 'Silk Evening Gown',
                'description' => 'Elegant floor-length evening dress in pure silk. Features a flattering A-line silhouette, subtle side slit, and delicate beading at the neckline.',
                'brand' => 'Elegance',
                'type' => 'Dress',
                'attributes' => [
                    'Material' => '100% pure silk',
                    'Style' => 'A-line',
                    'Length' => 'Floor length',
                    'Occasion' => 'Evening wear',
                    'Features' => 'Beaded neckline, side slit',
                    'Care Instructions' => 'Dry clean only',
                    'Lining' => 'Full silk lining',
                    'Closure' => 'Hidden back zipper'
                ],
                'variant_attributes' => [
                    ['size' => 'S', 'color' => 'Black'],
                    ['size' => 'M', 'color' => 'Black'],
                    ['size' => 'L', 'color' => 'Black'],
                    ['size' => 'S', 'color' => 'Navy'],
                    ['size' => 'M', 'color' => 'Navy'],
                    ['size' => 'L', 'color' => 'Navy'],
                    ['size' => 'S', 'color' => 'Red'],
                    ['size' => 'M', 'color' => 'Red']
                ],
                'base_price' => 299.99,
                'image' => $faker->imageUrl(640, 480, 'dress', true)
            ]
        ];

        // Get all currencies
        $currencies = Currency::all();
        $conversionRates = [
            'USD' => 1.0,
            'EUR' => 0.93,
            'GBP' => 0.79
        ];

        foreach ($products as $productData) {
            $type = $productData['type'];
            $variantAttributes = $productData['variant_attributes'];
            $attributes = $productData['attributes'];
            $basePrice = $productData['base_price'];
            $image = $productData['image'];

            unset(
                $productData['type'],
                $productData['variant_attributes'],
                $productData['attributes'],
                $productData['base_price'],
                $productData['image']
            );

            $productType = ProductType::where('name', $type)->first();

            $sellerProduct = SellerProduct::create([
                ...$productData,
                'seller_id' => $seller->id,
                'attributes' => $attributes
            ]);

            // Create variants with their attributes
            foreach ($variantAttributes as $index => $attributeValues) {
                $sku = strtoupper(substr($productData['brand'], 0, 3) . '-' .
                    implode('-', array_values($attributeValues)) .
                    '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT));

                $variant = $sellerProduct->sellerVariants()->create([
                    'sku' => $sku,
                    'attributes' => $attributeValues,
                    'seller_id' => $seller->id
                ]);

                // Create prices in different currencies
                foreach ($currencies as $currency) {
                    $amount = (int) ($basePrice * $conversionRates[$currency->code] * 100); // Convert to cents
                    $variant->prices()->create([
                        'amount' => $amount,
                        'currency_id' => $currency->id,
                        'seller_product_id' => $sellerProduct->id
                    ]);
                }

                // Create stock
                $variant->stocks()->create([
                    'quantity' => rand(10, 100),
                    'seller_product_id' => $sellerProduct->id,
                    'seller_id' => $seller->id,
                    'location_id' => $location->id,
                    'safety_stock' => rand(5, 15),
                    'reserved' => 0
                ]);
            }
        }
    }
}
