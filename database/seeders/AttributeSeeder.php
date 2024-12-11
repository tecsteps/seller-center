<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\ProductType;
use App\Models\ProductTypeAttribute;
use App\Models\ProductTypeAttributeOption;
use App\Models\ProductTypeAttributeOptionValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $locales = Locale::all();
        if ($locales->isEmpty()) {
            Log::error('No locales found in the database. Make sure LocaleSeeder has been run.');
            return;
        }

        $attributesByType = [
            'Sneakers' => [
                [
                    'name' => 'Material',
                    'slug' => 'material',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Leather',
                                'de' => 'Leder',
                                'fr' => 'Cuir'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Canvas',
                                'de' => 'Leinen',
                                'fr' => 'Toile'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Synthetic',
                                'de' => 'Synthetisch',
                                'fr' => 'Synthétique'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Mesh',
                                'de' => 'Mesh',
                                'fr' => 'Maille'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Suede',
                                'de' => 'Wildleder',
                                'fr' => 'Daim'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Knit',
                                'de' => 'Strick',
                                'fr' => 'Tricot'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Is Waterproof',
                    'type' => 'boolean',
                    'field' => 'Toggle',
                    'required' => true
                ],
                [
                    'name' => 'Style',
                    'slug' => 'style',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => false,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Athletic',
                                'de' => 'Athletic',
                                'fr' => 'Athletic'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Casual',
                                'de' => 'Casual',
                                'fr' => 'Casual'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Lifestyle',
                                'de' => 'Lifestyle',
                                'fr' => 'Lifestyle'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Running',
                                'de' => 'Running',
                                'fr' => 'Running'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Basketball',
                                'de' => 'Basketball',
                                'fr' => 'Basketball'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Skateboarding',
                                'de' => 'Skateboarding',
                                'fr' => 'Skateboarding'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Closure Type',
                    'slug' => 'closure-type',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Lace-up',
                                'de' => 'Schnürsenkel',
                                'fr' => 'Lacets'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Slip-on',
                                'de' => 'Schlupfschuh',
                                'fr' => 'Sans lacets'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Velcro',
                                'de' => 'Klettverschluss',
                                'fr' => 'Velcro'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Zip',
                                'de' => 'Reißverschluss',
                                'fr' => 'Fermeture éclair'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'BOA System',
                                'de' => 'BOA System',
                                'fr' => 'Système BOA'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Main Color',
                    'type' => 'color',
                    'field' => 'ColorPicker',
                    'required' => true
                ]
            ],
            'T-Shirt' => [
                [
                    'name' => 'Fabric',
                    'slug' => 'fabric',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => '100% Cotton',
                                'de' => '100% Baumwolle',
                                'fr' => '100% Coton'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Cotton Blend',
                                'de' => 'Baumwollmischung',
                                'fr' => 'Mélange de coton'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Polyester',
                                'de' => 'Polyester',
                                'fr' => 'Polyester'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Modal',
                                'de' => 'Modal',
                                'fr' => 'Modal'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Bamboo',
                                'de' => 'Bambus',
                                'fr' => 'Bambou'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Merino Wool',
                                'de' => 'Merinowolle',
                                'fr' => 'Laine mérinos'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Neckline',
                    'slug' => 'neckline',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => false,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Crew Neck',
                                'de' => 'Crew Neck',
                                'fr' => 'Crew Neck'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'V-Neck',
                                'de' => 'V-Neck',
                                'fr' => 'V-Neck'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Scoop Neck',
                                'de' => 'Scoop Neck',
                                'fr' => 'Scoop Neck'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Henley',
                                'de' => 'Henley',
                                'fr' => 'Henley'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Polo',
                                'de' => 'Polo',
                                'fr' => 'Polo'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Sleeve Type',
                    'slug' => 'sleeve-type',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Sleeveless',
                                'de' => 'Ärmellos',
                                'fr' => 'Sans manches'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Cap Sleeve',
                                'de' => 'Kurzarm',
                                'fr' => 'Manche courte'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Short Sleeve',
                                'de' => 'Kurzarm',
                                'fr' => 'Manche courte'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Long Sleeve',
                                'de' => 'Langarm',
                                'fr' => 'Manche longue'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Bell Sleeve',
                                'de' => 'Glockenärmel',
                                'fr' => 'Manche cloche'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Flutter Sleeve',
                                'de' => 'Flatterärmel',
                                'fr' => 'Manche papillon'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Is Sustainable',
                    'type' => 'boolean',
                    'field' => 'Toggle',
                    'required' => false
                ],
                [
                    'name' => 'Print Type',
                    'slug' => 'print-type',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => false,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Screen Print',
                                'de' => 'Siebdruck',
                                'fr' => 'Sérigraphie'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Digital Print',
                                'de' => 'Digitaldruck',
                                'fr' => 'Impression numérique'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Embroidery',
                                'de' => 'Stickerei',
                                'fr' => 'Broderie'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Heat Transfer',
                                'de' => 'Wärmeübertragung',
                                'fr' => 'Transfert thermique'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'No Print',
                                'de' => 'Kein Druck',
                                'fr' => 'Sans impression'
                            ]
                        ]
                    ]
                ]
            ],
            'Jeans' => [
                [
                    'name' => 'Fit',
                    'slug' => 'fit',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => false,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Skinny',
                                'de' => 'Skinny',
                                'fr' => 'Skinny'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Slim',
                                'de' => 'Slim',
                                'fr' => 'Slim'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Regular',
                                'de' => 'Regular',
                                'fr' => 'Regular'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Relaxed',
                                'de' => 'Relaxed',
                                'fr' => 'Relaxed'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Wide Leg',
                                'de' => 'Wide Leg',
                                'fr' => 'Wide Leg'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Boot Cut',
                                'de' => 'Boot Cut',
                                'fr' => 'Boot Cut'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Rise',
                    'slug' => 'rise',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Low Rise',
                                'de' => 'Niedriger Bund',
                                'fr' => 'Taille basse'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Mid Rise',
                                'de' => 'Mittlerer Bund',
                                'fr' => 'Taille moyenne'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'High Rise',
                                'de' => 'Hoher Bund',
                                'fr' => 'Taille haute'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Ultra High Rise',
                                'de' => 'Ultrahoher Bund',
                                'fr' => 'Taille très haute'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Wash',
                    'slug' => 'wash',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Light Wash',
                                'de' => 'Heller Waschgang',
                                'fr' => 'Délavage clair'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Medium Wash',
                                'de' => 'Mittlerer Waschgang',
                                'fr' => 'Délavage moyen'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Dark Wash',
                                'de' => 'Dunkler Waschgang',
                                'fr' => 'Délavage foncé'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Raw/Unwashed',
                                'de' => 'Roh/Ungebleicht',
                                'fr' => 'Brut/Non lavé'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Acid Wash',
                                'de' => 'Säurewaschgang',
                                'fr' => 'Délavage acide'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Stone Wash',
                                'de' => 'Steinwaschgang',
                                'fr' => 'Délavage pierre'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Stretch Level',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'No Stretch',
                                'de' => 'Keine Dehnung',
                                'fr' => 'Pas d\'élasticité'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Slight Stretch',
                                'de' => 'Geringe Dehnung',
                                'fr' => 'Légère élasticité'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Stretch',
                                'de' => 'Dehnung',
                                'fr' => 'Élasticité'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Super Stretch',
                                'de' => 'Superdehnung',
                                'fr' => 'Super élasticité'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Inseam Length',
                    'type' => 'number',
                    'field' => 'TextInput',
                    'required' => true,
                    'unit' => 'cm'
                ]
            ],
            'Dress' => [
                [
                    'name' => 'Length',
                    'slug' => 'length',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => false,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Mini',
                                'de' => 'Mini',
                                'fr' => 'Mini'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Midi',
                                'de' => 'Midi',
                                'fr' => 'Midi'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Maxi',
                                'de' => 'Maxi',
                                'fr' => 'Maxi'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Above Knee',
                                'de' => 'Above Knee',
                                'fr' => 'Above Knee'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Knee Length',
                                'de' => 'Knee Length',
                                'fr' => 'Knee Length'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Silhouette',
                    'slug' => 'silhouette',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => false,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'A-Line',
                                'de' => 'A-Line',
                                'fr' => 'A-Line'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Bodycon',
                                'de' => 'Bodycon',
                                'fr' => 'Bodycon'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Empire',
                                'de' => 'Empire',
                                'fr' => 'Empire'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Shift',
                                'de' => 'Shift',
                                'fr' => 'Shift'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Wrap',
                                'de' => 'Wrap',
                                'fr' => 'Wrap'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Ball Gown',
                                'de' => 'Ball Gown',
                                'fr' => 'Ball Gown'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Neckline',
                    'slug' => 'neckline',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => false,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'V-Neck',
                                'de' => 'V-Neck',
                                'fr' => 'V-Neck'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Round Neck',
                                'de' => 'Round Neck',
                                'fr' => 'Round Neck'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Square Neck',
                                'de' => 'Square Neck',
                                'fr' => 'Square Neck'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Sweetheart',
                                'de' => 'Sweetheart',
                                'fr' => 'Sweetheart'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Halter',
                                'de' => 'Halter',
                                'fr' => 'Halter'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Off-Shoulder',
                                'de' => 'Off-Shoulder',
                                'fr' => 'Off-Shoulder'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Sleeve Type',
                    'slug' => 'sleeve-type',
                    'type' => 'select',
                    'field' => 'Select',
                    'required' => true,
                    'is_translatable' => true,
                    'options' => [
                        [
                            'translations' => [
                                'en' => 'Sleeveless',
                                'de' => 'Ärmellos',
                                'fr' => 'Sans manches'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Cap Sleeve',
                                'de' => 'Kurzarm',
                                'fr' => 'Manche courte'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Short Sleeve',
                                'de' => 'Kurzarm',
                                'fr' => 'Manche courte'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Long Sleeve',
                                'de' => 'Langarm',
                                'fr' => 'Manche longue'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Bell Sleeve',
                                'de' => 'Glockenärmel',
                                'fr' => 'Manche cloche'
                            ]
                        ],
                        [
                            'translations' => [
                                'en' => 'Flutter Sleeve',
                                'de' => 'Flatterärmel',
                                'fr' => 'Manche papillon'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Main Color',
                    'type' => 'color',
                    'field' => 'ColorPicker',
                    'required' => true
                ],
                [
                    'name' => 'Has Pockets',
                    'type' => 'boolean',
                    'field' => 'Toggle',
                    'required' => false
                ]
            ]
        ];

        foreach ($attributesByType as $typeName => $attributes) {
            $type = ProductType::where('name', $typeName)->first();

            if (!$type) {
                Log::warning("Product type not found: {$typeName}");
                continue;
            }

            foreach ($attributes as $rank => $attributeData) {
                $attribute = ProductTypeAttribute::create([
                    'name' => $attributeData['name'],
                    'slug' => $attributeData['slug'] ?? Str::slug($attributeData['name']),
                    'type' => $attributeData['type'],
                    'field' => $attributeData['field'],
                    'required' => $attributeData['required'],
                    'rank' => $rank,
                    'unit' => $attributeData['unit'] ?? null,
                    'is_translatable' => $attributeData['is_translatable'] ?? false,
                    'product_type_id' => $type->id,
                ]);

                Log::info("Created attribute: {$attributeData['name']} for type: {$typeName}");

                // Create options if they exist
                if (isset($attributeData['options'])) {
                    foreach ($attributeData['options'] as $option) {
                        try {
                            // Create the option
                            $attributeOption = ProductTypeAttributeOption::create([
                                'product_type_attribute_id' => $attribute->id,
                            ]);

                            Log::info("Created option for attribute: {$attributeData['name']}");

                            // Create localized values for each locale
                            foreach ($locales as $locale) {
                                // Get the translated value for this locale, fallback to English if not available
                                $translatedValue = $option['translations'][$locale->code] ?? $option['translations']['en'];

                                $value = ProductTypeAttributeOptionValue::create([
                                    'value' => $translatedValue,
                                    'locale_id' => $locale->id,
                                    'product_type_attribute_option_id' => $attributeOption->id,
                                ]);

                                Log::info("Created option value for locale: {$locale->code}, value: {$value->value}");
                            }
                        } catch (\Exception $e) {
                            Log::error("Error creating option: " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }
}
