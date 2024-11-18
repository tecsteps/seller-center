<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\SellerProduct;
use App\Models\Status;
use App\Models\SellerVariant;
use App\Models\Location;
use App\Models\Stock;
use App\Models\Currency;
use App\Models\Price;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = \App\Models\User::factory()->create([
            'name' => 'Fabian',
            'email' => 'fabian.wesner@roq.tech',
            'password' => bcrypt('fabian.wesner@roq.tech'),
        ]);

        $categories = [
            [
                'name' => 'All Products',
                'description' => 'Root category for all products',
                'is_active' => true,
                'parent_id' => null,
            ],
            [
                'name' => 'Electronics',
                'description' => 'Smartphones, laptops, tablets, and other electronic devices',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Appliances, kitchenware, furniture, and home décor',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing, shoes, accessories, and jewelry',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Athletic equipment, camping gear, and outdoor recreation products',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Cosmetics, skincare, haircare, and personal hygiene products',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, e-books, movies, music, and educational content',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Children\'s toys, board games, and entertainment items',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Automotive',
                'description' => 'Car parts, accessories, and maintenance products',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Pet Supplies',
                'description' => 'Pet food, accessories, and care products for all types of pets',
                'is_active' => true,
                'parent_id' => 1,
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery, office furniture, and business essentials',
                'is_active' => true,
                'parent_id' => 1,
            ],
        ];
        // First create parent categories
        foreach ($categories as $category) {
            Category::create($category);
        }

        // Add sub-categories
        $subCategories = [
            // Electronics sub-categories
            [
                'name' => 'Smartphones',
                'description' => 'Mobile phones and accessories',
                'is_active' => true,
                'parent_id' => 2, // Electronics
            ],
            [
                'name' => 'Laptops',
                'description' => 'Notebook computers and accessories',
                'is_active' => true, 
                'parent_id' => 2,
            ],
            // Home & Kitchen sub-categories
            [
                'name' => 'Cookware',
                'description' => 'Pots, pans and cooking utensils',
                'is_active' => true,
                'parent_id' => 3,
            ],
            [
                'name' => 'Small Appliances',
                'description' => 'Coffee makers, toasters, blenders etc',
                'is_active' => true,
                'parent_id' => 3,
            ],
            // Fashion sub-categories
            [
                'name' => 'Women\'s Clothing',
                'description' => 'Dresses, tops, pants and more for women',
                'is_active' => true,
                'parent_id' => 4,
            ],
            [
                'name' => 'Men\'s Clothing',
                'description' => 'Shirts, pants, suits and more for men',
                'is_active' => true,
                'parent_id' => 4,
            ],
        ];

        foreach ($subCategories as $category) {
            Category::create($category);
        }

        

        $sellerProducts = [
            [
                'name' => 'Sony WH-1000XM4 Wireless Headphones',
                'description' => 'Industry-leading noise canceling with Dual Noise Sensor technology',
                'attributes' => [
                    'brand' => 'Sony',
                    'color' => ['Black', 'Silver', 'Blue'],
                    'battery_life' => '30 hours',
                    'connectivity' => 'Bluetooth 5.0'
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Le Creuset Enameled Dutch Oven',
                'description' => 'Premium cast iron cooking pot with superior heat distribution and retention',
                'attributes' => [
                    'material' => 'Enameled Cast Iron',
                    'capacity' => '5.5 Qt',
                    'colors_available' => ['Flame', 'Marine', 'Cerise'],
                    'dishwasher_safe' => true
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Nike Air Zoom Pegasus 38',
                'description' => 'Responsive running shoes with Zoom Air cushioning',
                'attributes' => [
                    'type' => 'Running Shoes',
                    'sizes' => ['US 7-13'],
                    'colors' => ['Black/White', 'Grey/Volt'],
                    'gender' => 'Unisex'
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'DJI Mini 2 Drone',
                'description' => 'Ultralight and foldable drone with 4K camera',
                'attributes' => [
                    'flight_time' => '31 minutes',
                    'camera' => '4K',
                    'weight' => '249g',
                    'range' => '10km'
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Dyson V15 Detect',
                'description' => 'Cordless vacuum with laser dust detection',
                'attributes' => [
                    'runtime' => '60 minutes',
                    'bin_capacity' => '0.75L',
                    'weight' => '6.8 lbs',
                    'features' => ['Laser Detection', 'LCD Screen']
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'LEGO Star Wars Millennium Falcon',
                'description' => 'Detailed replica of the iconic spacecraft',
                'attributes' => [
                    'pieces' => 7541,
                    'age_range' => '16+',
                    'dimensions' => '33x22x8 inches',
                    'includes_minifigures' => true
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Kindle Paperwhite',
                'description' => 'Waterproof e-reader with 6.8" display and adjustable warm light',
                'attributes' => [
                    'storage' => '8GB',
                    'screen_size' => '6.8 inches',
                    'waterproof_rating' => 'IPX8',
                    'battery_life' => '10 weeks'
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Automatic Pet Feeder',
                'description' => 'Smart pet feeder with programmable meal times',
                'attributes' => [
                    'capacity' => '6L',
                    'meal_settings' => '1-4 meals/day',
                    'power_source' => ['Battery', 'USB'],
                    'app_control' => true
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Herman Miller Aeron Chair',
                'description' => 'Ergonomic office chair with PostureFit SL support',
                'attributes' => [
                    'size_options' => ['A', 'B', 'C'],
                    'material' => '8Z Pellicle',
                    'warranty' => '12 years',
                    'adjustable_features' => ['Height', 'Tilt', 'Arms']
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'MAC Professional Makeup Kit',
                'description' => 'Complete makeup artist kit with essential products',
                'attributes' => [
                    'includes' => ['Eyeshadows', 'Lipsticks', 'Brushes'],
                    'shades' => 48,
                    'professional_grade' => true,
                    'travel_case' => true
                ],
                'category_id' => Category::inRandomOrder()->first()->id,
            ],
        ];

        foreach ($sellerProducts as $product) {
            SellerProduct::create($product);
        }

        $statuses = [
            ['name' => 'draft'],
            ['name' => 'pending_approval'],
            ['name' => 'approved'],
            ['name' => 'active'],
            ['name' => 'inactive'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }

        $sellerVariants = [
            // Sony Headphones Variants
            [
                'name' => 'WH-1000XM4 - Midnight Black',
                'description' => 'Classic black colorway with premium finish',
                'sku' => 'SONY-WH4-BLK',
                'attributes' => [
                    'color' => 'Midnight Black',
                    'warranty' => '1 year',
                    'includes' => ['Carrying Case', 'Audio Cable', 'USB-C Cable']
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'WH-1000XM4 - Platinum Silver',
                'description' => 'Sleek silver edition with matching accessories',
                'sku' => 'SONY-WH4-SLV',
                'attributes' => [
                    'color' => 'Platinum Silver',
                    'warranty' => '1 year',
                    'includes' => ['Carrying Case', 'Audio Cable', 'USB-C Cable']
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Le Creuset Variants
            [
                'name' => 'Dutch Oven - Flame Orange 5.5Qt',
                'description' => 'Signature flame orange color, 5.5-quart capacity',
                'sku' => 'LC-DO-55-FLM',
                'attributes' => [
                    'color' => 'Flame Orange',
                    'size' => '5.5 Qt',
                    'material' => 'Enameled Cast Iron'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Dutch Oven - Marine Blue 7.25Qt',
                'description' => 'Deep marine blue color, larger 7.25-quart capacity',
                'sku' => 'LC-DO-72-MRN',
                'attributes' => [
                    'color' => 'Marine Blue',
                    'size' => '7.25 Qt',
                    'material' => 'Enameled Cast Iron'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Nike Shoes Variants
            [
                'name' => 'Pegasus 38 - Black/White US 9',
                'description' => 'Classic black and white colorway, size US 9',
                'sku' => 'NK-PG38-BW-09',
                'attributes' => [
                    'color' => 'Black/White',
                    'size' => 'US 9',
                    'width' => 'Regular'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Pegasus 38 - Grey/Volt US 10',
                'description' => 'Grey with volt accents, size US 10',
                'sku' => 'NK-PG38-GV-10',
                'attributes' => [
                    'color' => 'Grey/Volt',
                    'size' => 'US 10',
                    'width' => 'Wide'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Dyson Variants
            [
                'name' => 'V15 Detect - Complete',
                'description' => 'Complete set with all attachments',
                'sku' => 'DYS-V15-CMP',
                'attributes' => [
                    'model' => 'Complete',
                    'attachments' => ['All Standard', 'Extra Tools'],
                    'warranty' => '2 years'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'V15 Detect - Essential',
                'description' => 'Essential package with standard attachments',
                'sku' => 'DYS-V15-ESS',
                'attributes' => [
                    'model' => 'Essential',
                    'attachments' => ['Standard Only'],
                    'warranty' => '2 years'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Kindle Variants
            [
                'name' => 'Paperwhite 8GB - With Ads',
                'description' => 'Standard version with special offers',
                'attributes' => [
                    'storage' => '8GB',
                    'special_offers' => true,
                    'color' => 'Black'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Paperwhite 16GB - No Ads',
                'description' => 'Extended storage without special offers',
                'attributes' => [
                    'storage' => '16GB',
                    'special_offers' => false,
                    'color' => 'Black'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Herman Miller Variants
            [
                'name' => 'Aeron - Size A Graphite',
                'description' => 'Small size in graphite finish',
                'attributes' => [
                    'size' => 'A',
                    'color' => 'Graphite',
                    'lumbar_support' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Aeron - Size B Mineral',
                'description' => 'Medium size in mineral finish',
                'attributes' => [
                    'size' => 'B',
                    'color' => 'Mineral',
                    'lumbar_support' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // MAC Makeup Variants
            [
                'name' => 'Pro Kit - Warm Tones',
                'description' => 'Professional kit focused on warm shades',
                'attributes' => [
                    'palette_type' => 'Warm',
                    'number_of_shades' => 24,
                    'includes_brushes' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Pro Kit - Cool Tones',
                'description' => 'Professional kit focused on cool shades',
                'attributes' => [
                    'palette_type' => 'Cool',
                    'number_of_shades' => 24,
                    'includes_brushes' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // DJI Drone Variants
            [
                'name' => 'Mini 2 - Standard',
                'description' => 'Standard package with basic accessories',
                'attributes' => [
                    'package_type' => 'Standard',
                    'batteries' => 1,
                    'carrying_case' => 'Basic'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Mini 2 - Fly More Combo',
                'description' => 'Extended package with additional accessories',
                'attributes' => [
                    'package_type' => 'Fly More',
                    'batteries' => 3,
                    'carrying_case' => 'Premium'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Pet Feeder Variants
            [
                'name' => 'Smart Feeder - Standard',
                'description' => 'Basic model with essential features',
                'attributes' => [
                    'capacity' => '4L',
                    'wifi_enabled' => false,
                    'battery_backup' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Smart Feeder - Premium',
                'description' => 'Advanced model with WiFi connectivity',
                'attributes' => [
                    'capacity' => '6L',
                    'wifi_enabled' => true,
                    'battery_backup' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // LEGO Variants
            [
                'name' => 'Millennium Falcon - Standard Edition',
                'description' => 'Regular retail version',
                'attributes' => [
                    'edition' => 'Standard',
                    'exclusive_minifigures' => false,
                    'display_stand' => false
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Millennium Falcon - Collector\'s Edition',
                'description' => 'Limited collector\'s version with extras',
                'attributes' => [
                    'edition' => 'Collector',
                    'exclusive_minifigures' => true,
                    'display_stand' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            // Additional Generic Variants
            [
                'name' => 'Limited Holiday Edition',
                'description' => 'Special holiday season variant',
                'attributes' => [
                    'edition_type' => 'Limited',
                    'season' => 'Holiday 2023',
                    'numbered' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Starter Bundle',
                'description' => 'Perfect for beginners',
                'attributes' => [
                    'bundle_type' => 'Starter',
                    'includes_guide' => true,
                    'bonus_items' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Premium Bundle',
                'description' => 'Complete set with all accessories',
                'attributes' => [
                    'bundle_type' => 'Premium',
                    'exclusive_content' => true,
                    'extended_warranty' => true
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Professional Edition',
                'description' => 'Enhanced version for professional users',
                'attributes' => [
                    'edition' => 'Professional',
                    'pro_features' => true,
                    'support_level' => 'Priority'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
            [
                'name' => 'Basic Edition',
                'description' => 'Standard version for regular users',
                'attributes' => [
                    'edition' => 'Basic',
                    'pro_features' => false,
                    'support_level' => 'Standard'
                ],
                'seller_product_id' => SellerProduct::inRandomOrder()->first()->id,
                'status_id' => Status::inRandomOrder()->first()->id,
            ],
        ];

        foreach ($sellerVariants as $variant) {
            SellerVariant::create($variant);
        }

        $locations = [
            [
                'name' => 'Central Distribution Center',
                'address' => '2250 Parkway Drive, Denver, CO 80216',
                'default_delivery_days' => 2
            ],
            [
                'name' => 'West Coast Fulfillment Center',
                'address' => '1100 Harbor Bay Parkway, Oakland, CA 94502',
                'default_delivery_days' => 3
            ],
            [
                'name' => 'East Coast Warehouse',
                'address' => '200 Liberty Way, Newark, NJ 07114',
                'default_delivery_days' => 2
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        // Create multiple stock entries for each variant across different locations
        $stocks = [];
        
        // Get all variants and locations to distribute stock across them
        $variants = SellerVariant::all();
        $locations = Location::all();
        
        // Create stock entries ensuring good distribution
        foreach ($variants as $variant) {
            // Randomly decide how many locations this variant is stocked in (1-3)
            $numberOfLocations = rand(1, 3);
            $randomLocations = $locations->random($numberOfLocations);
            
            foreach ($randomLocations as $location) {
                $stocks[] = [
                    'quantity' => rand(50, 500),
                    'reserved' => rand(0, 20),
                    'safety_stock' => rand(10, 50),
                    'seller_variant_id' => $variant->id,
                    'location_id' => $location->id
                ];
            }
        }
        
        // Trim to exactly 35 entries if we generated more
        $stocks = array_slice($stocks, 0, 35);
        
        foreach ($stocks as $stock) {
            Stock::create($stock);
        }

        $currencies = [
            [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'US Dollar',
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro',
            ],
            [
                'code' => 'GBP',
                'symbol' => '£',
                'name' => 'British Pound Sterling',
            ]
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }

        // Get all variants and currencies
        $variants = SellerVariant::all();
        $currencies = Currency::all();

        // Create a price for each variant in each currency
        foreach ($variants as $variant) {
            // Base price in USD (between $10 and $2000)
            $basePrice = rand(1000, 200000);  // in cents

            foreach ($currencies as $currency) {
                // Apply conversion rates (approximate)
                $value = match ($currency->code) {
                    'USD' => $basePrice,
                    'EUR' => (int)($basePrice * 0.93),  // USD to EUR
                    'GBP' => (int)($basePrice * 0.79),  // USD to GBP
                    default => $basePrice
                };


                $price = new Price([
                    'value' => $value,
                    'seller_variant_id' => $variant->id,
                    'currency_id' => $currency->id
                ]);
                $price->save();
            }
        }
    }
}
