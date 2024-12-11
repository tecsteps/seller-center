<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductTypeSeeder::class,
            LocaleSeeder::class,
            AttributeSeeder::class,
            SellerDataSeeder::class,
            LocationSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
