<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\SellerData;
use Illuminate\Database\Seeder;

class SellerDataSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::first();

        SellerData::create([
            'seller_id' => $seller->id,
            'description' => 'BarSeller Inc. is a company that sells fashion products.',
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
