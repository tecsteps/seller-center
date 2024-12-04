<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        $locales = [
            [
                'code' => 'en',
                'name' => 'English',
                'default' => true,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'default' => false,
            ],
            [
                'code' => 'fr',
                'name' => 'French',
                'default' => false,
            ],
        ];

        foreach ($locales as $locale) {
            Locale::create($locale);
        }
    }
}
