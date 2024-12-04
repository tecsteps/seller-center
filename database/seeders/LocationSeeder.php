<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::first();

        Location::create([
            'seller_id' => $seller->id,
            'name' => 'Main Warehouse',
            'address' => '789 Warehouse Ave, Unit 101, San Francisco, CA 94107',
            'default_delivery_days' => 3,
        ]);

        // Create additional locations
        $locations = [
            [
                'name' => 'Central Distribution Center',
                'address' => '2250 Parkway Drive, Denver, CO 80216',
                'default_delivery_days' => 2,
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'West Coast Fulfillment Center',
                'address' => '1100 Harbor Bay Parkway, Oakland, CA 94502',
                'default_delivery_days' => 3,
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'East Coast Warehouse',
                'address' => '200 Liberty Way, Newark, NJ 07114',
                'default_delivery_days' => 2,
                'seller_id' => $seller->id,
            ],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }
    }
}
