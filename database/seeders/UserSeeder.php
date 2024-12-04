<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seller;
use App\Models\SellerData;
use App\Models\Partnership;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create users
        $ownerUser = User::factory()->create([
            'name' => 'Mr Operator',
            'email' => 'owner@ossc.tech',
            'password' => bcrypt('owner@ossc.tech'),
            'is_seller' => false,
        ]);

        $sellerUser = User::factory()->create([
            'name' => 'Mr Seller',
            'email' => 'seller@ossc.tech',
            'password' => bcrypt('seller@ossc.tech'),
            'is_seller' => true,
        ]);

        // Create seller and associate with user
        $seller = Seller::create([
            'name' => 'BarSeller'
        ]);

        $sellerUser->sellers()->attach($seller);

        // Create partnership
        Partnership::create([
            'seller_id' => $seller->id,
            'status' => 'submitted',
            'notes' => 'Initial partnership application'
        ]);

        // Create seller data
        SellerData::create([
            'seller_id' => $seller->id,
            'description' => 'BarSeller Inc. is a company that sells wooden furniture.',
            'company_name' => 'BarSeller Inc.',
            'address_line1' => '123 Market Street',
            'address_line2' => 'Suite 456',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94105',
            'country_code' => 'US',
            'phone' => '+1-415-555-0123',
            'vat' => 'US123456789',
            'tin' => '12-3456789',
            'eori' => 'US12345678901234',
            'iban' => 'US123456789012345678901234',
            'swift_bic' => 'CHASUS33XXX',
            'bank_name' => 'Chase Bank'
        ]);
    }
}
