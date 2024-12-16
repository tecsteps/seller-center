<?php

namespace App\Services;

use App\Models\GoldenProduct;
use App\Models\SellerProduct;
use App\Models\ProductType;
use App\Models\Locale;
use App\Models\ProductTypeAttribute;
use App\Models\ProductTypeAttributeOptionValue;
use App\Models\GoldenProductAttribute;
use App\Models\GoldenProductAttributeValue;
use App\Services\OpenAIService;
use App\Services\AttributeMappingService;
use App\Services\ProductTypeClassificationService;
use App\Services\ProductTranslationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GoldenProductService
{
    public function __construct(
        private readonly OpenAIService $openAIService,
        private readonly AttributeMappingService $attributeMapper,
        private readonly ProductTypeClassificationService $productTypeClassifier,
        private readonly ProductTranslationService $translator,
    ) {}

    /**
     * TODO This should happen asynchronously
     * TODO When an required field cannot be set, we don't save the product and throw an error
     * TODO Save the remaining fields into unmapped attributes
     * TODO Unit conversion and field splitting
     */
    public function createFromSellerProduct(SellerProduct $sellerProduct): GoldenProduct
    {
        // we don't create a golden product if it already exists
        // TODO
        // if ($sellerProduct->golden_product_id) {
        //     return $sellerProduct->goldenProduct;
        // }

        $productType = $this->productTypeClassifier->determineProductType($sellerProduct);
        $translations = $this->translateProductName($sellerProduct);

        $mappedAttributes = $this->attributeMapper->mapAttributes($sellerProduct->attributes, $productType);
        $attributes = $this->translateTextAttributeValues($mappedAttributes, $productType, $translations);
        $attributes = $this->addAttributeOptionValues($attributes, $mappedAttributes, $productType);

        $goldenProduct = $this->createGoldenProduct($productType);

        $this->createTranslations($goldenProduct, $translations);
        $this->createAttributes($goldenProduct, $attributes);

        $this->updateSellerProduct($sellerProduct, $goldenProduct);

        return $goldenProduct;
    }



    private function createGoldenProduct(ProductType $productType): GoldenProduct
    {
        $goldenProduct = GoldenProduct::create([
            'product_type_id' => $productType->id,
        ]);

        return $goldenProduct;
    }

    public function translateProductName(SellerProduct $sellerProduct): array
    {
        $translations = [];
        foreach (Locale::all() as $locale) {
            $translations[$locale->id] = $this->translator->translateTexts([
                'name' => $sellerProduct->name,
                'description' => $sellerProduct->description
            ], $locale->code);
        }
        return $translations;
    }

    public function translateTextAttributeValues(array $mappedAttributes, ProductType $productType, array $translations): array
    {
        $attributes = [];
        foreach (Locale::all() as $locale) {
            $attributes[$locale->id] = [];
            $toBetranslatedValues = [];
            foreach ($mappedAttributes as $attributeConfigId => $value) {
                $attributeConfiguration = $productType->attributes->find($attributeConfigId); // TODO too many queries

                if ($attributeConfiguration->type !== 'select' && $attributeConfiguration->is_translatable === true) {
                    // $translatedValue = $this->translator->translateString($value, $locale->code);
                    $toBetranslatedValues[$attributeConfigId] = $value;
                }
            }
            $translations = $this->translator->translateTexts($toBetranslatedValues, $locale->code);
            foreach ($mappedAttributes as $attributeConfigId => $value) {
                if (array_key_exists($attributeConfigId, $translations)) {
                    $attributes[$locale->id][$attributeConfigId] = $translations[$attributeConfigId];
                } else {
                    $attributes[$locale->id][$attributeConfigId] = $value;
                }
            }
        }
        return $attributes;
    }

    public function addAttributeOptionValues(array $attributes, array $mappedAttributes, ProductType $productType): array
    {
        foreach ($mappedAttributes as $attributeConfigId => $value) {
            $attributeConfiguration = $productType->attributes->find($attributeConfigId); // TODO too many queries

            foreach (Locale::all() as $locale) {
                if ($attributeConfiguration->type === 'select') {
                    $optionEntity = ProductTypeAttributeOptionValue::where('product_type_attribute_option_id', $value)
                        ->where('locale_id', $locale->id)
                        ->first();
                    $attributes[$locale->id][$attributeConfigId] = $optionEntity;
                }
            }
        }
        return $attributes;
    }

    private function createTranslations(GoldenProduct $goldenProduct, array $translations): void
    {
        foreach (Locale::all() as $locale) {
            $translation = $translations[$locale->id];
            $goldenProduct->translations()->create([
                'name' => $translation['name'],
                'description' => $translation['description'],
                'locale_id' => $locale->id,
            ]);
        }
    }

    private function createAttributes(GoldenProduct $goldenProduct, array $attributes): void
    {

        $saved = [];
        foreach ($attributes as $localeId => $attributeValues) {
            foreach ($attributeValues as $attributeConfigId => $value) {

                if (!array_key_exists($attributeConfigId, $saved)) {
                    $goldenProductAttribute = GoldenProductAttribute::create([
                        'golden_product_id' => $goldenProduct->id,
                        'product_type_attribute_id' => $attributeConfigId,
                    ]);
                    $saved[$attributeConfigId] = $goldenProductAttribute;
                } else {
                    $goldenProductAttribute = $saved[$attributeConfigId];
                }

                if (is_scalar($value)) {
                    GoldenProductAttributeValue::create([
                        'golden_product_attribute_id' => $goldenProductAttribute->id,
                        'value' => $value,
                        'locale_id' => $localeId
                    ]);
                } else if ($value instanceof ProductTypeAttributeOptionValue) {
                    // Directly insert into the pivot table
                    DB::table('golden_product_attribute_product_type_attribute_option_value')->insert([
                        'golden_product_attribute_id' => $goldenProductAttribute->id,
                        'product_type_attribute_option_value_id' => $value->id,
                    ]);
                    $goldenProductAttribute->is_option = true;
                    $goldenProductAttribute->save();
                } else {
                    throw new \Exception('Unsupported attribute value type for value: ' . $value);
                }
            }
        }
    }

    private function updateSellerProduct(SellerProduct $sellerProduct, GoldenProduct $goldenProduct): void
    {
        $sellerProduct->update(['golden_product_id' => $goldenProduct->id]);
    }
}
